<?php
/**
 * AuthController - Controlador de Autenticación
 */

class AuthController extends Controller {
    private $usuarioModel;
    private $logModel;
    
    public function __construct() {
        $this->usuarioModel = new Usuario();
        $this->logModel = new LogActividad();
    }
    
    /**
     * Mostrar formulario de login
     */
    public function showLogin() {
        // Si ya está autenticado, redirigir al dashboard
        if (isset($_SESSION['user_id'])) {
            $this->redirect('/dashboard');
        }
        
        $this->view('auth/login');
    }
    
    /**
     * Procesar login
     */
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/login');
        }
        
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        
        // Validar campos
        if (empty($username) || empty($password)) {
            $_SESSION['error'] = 'Por favor, complete todos los campos';
            $this->redirect('/login');
        }
        
        // Validar CSRF
        if (!Csrf::verify($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Error de seguridad: Token CSRF inválido';
            $this->redirect('/login');
        }
        
        // Intentar obtener usuario primero para chequear bloqueos
        $userObj = $this->usuarioModel->getByUsername($username);
        
        if ($userObj) {
            // Chequear si está bloqueado permanentemente
            if ($userObj['estado'] === 'inactivo' && $userObj['bloqueos_totales'] >= 2) {
                $_SESSION['error'] = 'Tu cuenta ha sido bloqueada permanentemente. Contacta al administrador.';
                $this->redirect('/login');
            }
            // Chequear si está inactivo por otra razón
            if ($userObj['estado'] === 'inactivo') {
                $_SESSION['error'] = 'Tu cuenta está inactiva.';
                $this->redirect('/login');
            }
            
            // Chequear bloqueo temporal
            if ($userObj['bloqueado_hasta'] !== null) {
                if (strtotime($userObj['bloqueado_hasta']) > time()) {
                    $_SESSION['error'] = 'Has excedido el número de intentos. Cuenta bloqueada temporalmente.';
                    $this->redirect('/login');
                }
            }
        }
        
        // Intentar autenticar
        $user = $this->usuarioModel->authenticate($username, $password);
        
        if ($user) {
            // Login exitoso
            // Resetear seguridad
            $this->usuarioModel->resetearSeguridad($user['id']);
            
            // Protección contra fijación de sesión
            session_regenerate_id(true);
            
            // Crear sesión
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['user_role'] = $user['rol'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['login_time'] = time();
            $_SESSION['last_activity'] = time();
            
            // Generar nuevo token CSRF para la nueva sesión
            unset($_SESSION['csrf_token']);
            Csrf::generate();
            
            // Registrar en logs
            $this->logModel->registrar(
                $user['id'],
                'login',
                'autenticacion',
                "Usuario {$username} inició sesión"
            );
            
            $_SESSION['success'] = '¡Bienvenido, ' . $username . '!';
            $this->redirect('/dashboard');
        } else {
            // Si el user existía pero falló la pass, incrementar errores
            if ($userObj) {
                $this->usuarioModel->incrementarIntentos($userObj['id']);
                
                // Volver a obtener para ver en cuánto quedó
                $userActualizado = $this->usuarioModel->getById($userObj['id']);
                
                if ($userActualizado['intentos_fallidos'] >= 3) {
                    if ($userActualizado['bloqueos_totales'] == 0) {
                        // Primer bloqueo (10 mins)
                        $this->usuarioModel->bloquearTemporalmente($userActualizado['id']);
                        $_SESSION['error'] = '3 intentos fallidos. Tu cuenta ha sido bloqueada por 10 minutos.';
                    } else {
                        // Segundo bloqueo (Permanente)
                        $this->usuarioModel->bloquearPermanentemente($userActualizado['id']);
                        $_SESSION['error'] = 'Múltiples intentos fallidos. Tu cuenta ha sido bloqueada permanentemente. Contacta al administrador.';
                    }
                    $this->logModel->registrar(null, 'bloqueo_cuenta', 'seguridad', "Cuenta {$username} bloqueada (Nivel {$userActualizado['bloqueos_totales']})");
                    $this->redirect('/login');
                }
            }
            
            // Login fallido "normal"
            $this->logModel->registrar(
                null,
                'login_fallido',
                'autenticacion',
                "Intento de login fallido para usuario: {$username}"
            );
            
            $_SESSION['error'] = 'Credenciales incorrectas';
            $this->redirect('/login');
        }
    }
    
    /**
     * Cerrar sesión
     */
    public function logout() {
        if (isset($_SESSION['user_id'])) {
            $userExists = $this->usuarioModel->getById($_SESSION['user_id']);
            $usuarioId = $userExists ? $_SESSION['user_id'] : null;
            
            $this->logModel->registrar(
                $usuarioId,
                'logout',
                'autenticacion',
                "Usuario {$_SESSION['username']} cerró sesión"
            );
        }
        
        $timeout = $_GET['timeout'] ?? '';
        session_destroy();
        
        if ($timeout === 'inactivity') {
            $this->redirect('/login?timeout=inactivity');
        } elseif ($timeout === 'absolute') {
            $this->redirect('/login?timeout=absolute');
        } else {
            $this->redirect('/login');
        }
    }
    
    /**
     * Verificar sesión (AJAX)
     */
    public function checkSession() {
        $authenticated = isset($_SESSION['user_id']);
        if ($authenticated && isset($_GET['refresh']) && $_GET['refresh'] == 1) {
            $_SESSION['last_activity'] = time();
        }
        $this->json([
            'authenticated' => $authenticated,
            'user' => $this->getCurrentUser()
        ]);
    }
}
