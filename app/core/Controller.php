<?php
/**
 * Controller Base - Clase base para todos los controladores
 */

class Controller {
    protected $model;
    
    /**
     * Cargar vista
     */
    protected function view($view, $data = []) {
        extract($data);
        
        $viewFile = BASE_PATH . '/app/views/' . $view . '.php';
        
        if (file_exists($viewFile)) {
            require_once $viewFile;
        } else {
            die("Vista no encontrada: {$view}");
        }
    }
    
    /**
     * Cargar modelo
     */
    protected function loadModel($model) {
        $modelFile = BASE_PATH . '/app/models/' . $model . '.php';
        
        if (file_exists($modelFile)) {
            require_once $modelFile;
            return new $model();
        }
        return null;
    }
    
    /**
     * Redireccionar
     */
    protected function redirect($url) {
        header("Location: " . APP_URL . $url);
        exit;
    }
    
    /**
     * Respuesta JSON
     */
    protected function json($data, $statusCode = 200) {
        // Limpiar cualquier salida previa (notices, warnings, espacios)
        if (ob_get_length()) ob_clean();
        
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        
        $json = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PARTIAL_OUTPUT_ON_ERROR);
        
        if ($json === false) {
            // Si falla la codificación JSON
            http_response_code(500);
            echo json_encode(['error' => 'Error de codificación JSON: ' . json_last_error_msg()]);
        } else {
            echo $json;
        }
        exit;
    }
    
    /**
     * Verificar autenticación
     */
    protected function requireAuth() {
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('/login');
        }
    }
    
    /**
     * Verificar rol
     */
    protected function requireRole($roles) {
        $this->requireAuth();
        
        // Super Admin tiene acceso total ("God Mode")
        if ($_SESSION['user_role'] === 'super_admin') {
            return;
        }
        
        if (!is_array($roles)) {
            $roles = [$roles];
        }
        
        if (!in_array($_SESSION['user_role'], $roles)) {
            $this->redirect('/dashboard');
        }
    }
    
    /**
     * Obtener usuario actual
     */
    protected function getCurrentUser() {
        if (isset($_SESSION['user_id'])) {
            return [
                'id' => $_SESSION['user_id'],
                'username' => $_SESSION['username'],
                'role' => $_SESSION['user_role'],
                'email' => $_SESSION['email'] ?? ''
            ];
        }
        return null;
    }
}
