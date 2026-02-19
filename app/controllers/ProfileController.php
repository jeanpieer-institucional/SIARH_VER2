<?php
/**
 * ProfileController - Controlador de Perfil de Usuario
 */

class ProfileController extends Controller {
    private $usuarioModel;
    private $logModel;
    
    public function __construct() {
        $this->usuarioModel = new Usuario();
        $this->logModel = new LogActividad();
    }
    
    /**
     * Mostrar perfil del usuario
     */
    public function index() {
        $this->requireAuth();
        
        // Obtener datos frescos de la base de datos
        $user = $this->usuarioModel->getById($_SESSION['user_id']);
        
        // Si por alguna razón no se encuentra (borrado manual?), usar datos de sesión
        if (!$user) {
            $user = $this->getCurrentUser();
            // Mapear rol si usamos getCurrentUser fallback
            $user['rol'] = $user['role'] ?? 'user';
        }
        
        $data = [
            'user' => $user
        ];
        
        $this->view('perfil/index', $data);
    }
    
    /**
     * Actualizar contraseña
     */
    public function updatePassword() {
        $this->requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/perfil');
        }
        
        // Verificar CSRF
        if (!Csrf::verify($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Error de seguridad: Token CSRF inválido';
            $this->redirect('/perfil');
        }
        
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        
        // Validaciones básicas
        if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
            $_SESSION['error'] = 'Todos los campos son obligatorios';
            $this->redirect('/perfil');
        }
        
        if ($newPassword !== $confirmPassword) {
            $_SESSION['error'] = 'Las nuevas contraseñas no coinciden';
            $this->redirect('/perfil');
        }
        
        // Verificar contraseña actual
        $user = $this->usuarioModel->getById($_SESSION['user_id']);
        if (!password_verify($currentPassword, $user['password'])) {
            $_SESSION['error'] = 'La contraseña actual es incorrecta';
            $this->redirect('/perfil');
        }
        
        // Cambiar contraseña
        if ($this->usuarioModel->changePassword($_SESSION['user_id'], $newPassword)) {
            $this->logModel->registrar(
                $_SESSION['user_id'],
                'cambiar_password',
                'usuarios',
                "Usuario cambió su propia contraseña"
            );
            
            $_SESSION['success'] = 'Contraseña actualizada exitosamente';
        } else {
            $_SESSION['error'] = 'Error al actualizar la contraseña';
        }
        
        $this->redirect('/perfil');
    }
}
