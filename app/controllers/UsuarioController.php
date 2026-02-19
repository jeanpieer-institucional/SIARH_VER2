<?php
/**
 * UsuarioController - Controlador de Gestión de Usuarios
 */

class UsuarioController extends Controller {
    private $usuarioModel;
    private $logModel;
    
    public function __construct() {
        $this->usuarioModel = new Usuario();
        $this->logModel = new LogActividad();
    }
    
    /**
     * Listar usuarios
     */
    public function index() {
        $this->requireRole('super_admin');
        
        $usuarios = $this->usuarioModel->getAll([], 'rol, username');
        
        $data = [
            'usuarios' => $usuarios
        ];
        
        $this->view('usuarios/index', $data);
    }
    
    /**
     * Formulario de creación
     */
    public function create() {
        $this->requireRole('super_admin');
        
        $this->view('usuarios/form');
    }
    
    /**
     * Guardar nuevo usuario
     */
    public function store() {
        $this->requireRole('super_admin');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/usuarios');
        }
        
        // CSRF Check
        if (!Csrf::verify($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Error de seguridad: Token CSRF inválido';
            $this->redirect('/usuarios/create');
        }
        
        // Validar username único
        if ($this->usuarioModel->usernameExists($_POST['username'])) {
            $_SESSION['error'] = 'El nombre de usuario ya existe';
            $this->redirect('/usuarios/create');
        }
        
        $userData = [
            'username' => $_POST['username'],
            'email' => $_POST['email'],
            'password' => $_POST['password'], // UsuarioModel hashea esto
            'rol' => $_POST['rol'],
            'estado' => $_POST['estado'] ?? 'activo'
        ];
        
        $usuarioId = $this->usuarioModel->createUser($userData);
        
        if ($usuarioId) {
            $this->logModel->registrar(
                $_SESSION['user_id'],
                'crear_usuario',
                'usuarios',
                "Usuario creado: {$_POST['username']} (Rol: {$_POST['rol']})"
            );
            
            $_SESSION['success'] = 'Usuario creado exitosamente';
            $this->redirect('/usuarios');
        } else {
            $_SESSION['error'] = 'Error al crear usuario';
            $this->redirect('/usuarios/create');
        }
    }
    
    /**
     * Formulario de edición
     */
    public function edit($id) {
        $this->requireRole('super_admin');
        
        $usuario = $this->usuarioModel->getById($id);
        
        if (!$usuario) {
            $_SESSION['error'] = 'Usuario no encontrado';
            $this->redirect('/usuarios');
        }
        
        $data = [
            'usuario' => $usuario,
            'is_edit' => true
        ];
        
        $this->view('usuarios/form', $data);
    }
    
    /**
     * Actualizar usuario
     */
    public function update($id) {
        $this->requireRole('super_admin');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/usuarios');
        }
        
        // CSRF Check
        if (!Csrf::verify($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Error de seguridad: Token CSRF inválido';
            $this->redirect("/usuarios/edit/{$id}");
        }
        
        // Validar username único (excluyendo actual)
        if ($this->usuarioModel->usernameExists($_POST['username'], $id)) {
            $_SESSION['error'] = 'El nombre de usuario ya existe';
            $this->redirect("/usuarios/edit/{$id}");
        }
        
        $userData = [
            'username' => $_POST['username'],
            'email' => $_POST['email'],
            'rol' => $_POST['rol'],
            'estado' => $_POST['estado']
        ];
        
        // Solo actualizar contraseña si se proporciona
        if (!empty($_POST['password'])) {
            $this->usuarioModel->changePassword($id, $_POST['password']);
            $this->logModel->registrar(
                $_SESSION['user_id'],
                'cambiar_password_admin',
                'usuarios',
                "Cambio de contraseña para: {$_POST['username']}"
            );
        }
        
        // Actualizar otros datos (UsuarioModel no tiene un 'update' genérico, se debe implementar o usar query)
        // Por ahora, asumimos que UsuarioModel tiene update o lo implementamos aquí
        // Nota: UsuarioModel::update no existe en el código visto anteriormente.
        // Voy a usar el método genérico del Model si es protected, pero aquí llamamos al model public.
        // Revisaré UsuarioModel si es necesario, pero por ahora uso update() asumiendo que hereda o existe.
        // Si no existe, lo agregaré.
        
        // Implementación directa por si acaso
        $sql = "UPDATE usuarios SET username = :username, email = :email, rol = :rol, estado = :estado WHERE id = :id";
        $params = [
            ':username' => $userData['username'],
            ':email' => $userData['email'],
            ':rol' => $userData['rol'],
            ':estado' => $userData['estado'],
            ':id' => $id
        ];
        
        if ($this->usuarioModel->query($sql, $params)) {
            $this->logModel->registrar(
                $_SESSION['user_id'],
                'actualizar_usuario',
                'usuarios',
                "Usuario actualizado: {$_POST['username']}"
            );
            $_SESSION['success'] = 'Usuario actualizado correctamente';
        } else {
            $_SESSION['error'] = 'Error al actualizar usuario';
        }
        
        $this->redirect('/usuarios');
    }
    
    /**
     * Eliminar usuario
     */
    public function delete($id) {
        $this->requireRole(['super_admin']); // Solo Super Admin puede borrar
        
        // Evitar auto-borrado
        if ($id == $_SESSION['user_id']) {
            $_SESSION['error'] = 'No puedes eliminar tu propia cuenta';
            $this->redirect('/usuarios');
        }
        
        $usuario = $this->usuarioModel->getById($id);
        
        if ($usuario) {
            // Soft delete o hard delete? Usaremos DELETE físico por ahora
            $sql = "DELETE FROM usuarios WHERE id = :id";
            if ($this->usuarioModel->query($sql, [':id' => $id])) {
                 $this->logModel->registrar(
                    $_SESSION['user_id'],
                    'eliminar_usuario',
                    'usuarios',
                    "Usuario eliminado: {$usuario['username']}"
                );
                $_SESSION['success'] = 'Usuario eliminado correctamente';
            } else {
                $_SESSION['error'] = 'Error al eliminar usuario (puede tener datos relacionados)';
            }
        }
        
        $this->redirect('/usuarios');
    }
}
