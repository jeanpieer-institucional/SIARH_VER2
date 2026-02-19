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
        
        // Intentar autenticar
        $user = $this->usuarioModel->authenticate($username, $password);
        
        if ($user) {
            // Protección contra fijación de sesión
            session_regenerate_id(true);
            
            // Crear sesión
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['user_role'] = $user['rol'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['login_time'] = time();
            
            // Generar nuevo token CSRF para la nueva sesión
            // Borramos el anterior para forzar uno nuevo
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
            // Login fallido
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
            $this->logModel->registrar(
                $_SESSION['user_id'],
                'logout',
                'autenticacion',
                "Usuario {$_SESSION['username']} cerró sesión"
            );
        }
        
        session_destroy();
        $this->redirect('/login');
    }
    
    /**
     * Verificar sesión (AJAX)
     */
    public function checkSession() {
        $this->json([
            'authenticated' => isset($_SESSION['user_id']),
            'user' => $this->getCurrentUser()
        ]);
    }
}
