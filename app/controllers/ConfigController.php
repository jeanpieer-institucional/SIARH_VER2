<?php
/**
 * ConfigController - Controlador de Configuración
 */

class ConfigController extends Controller {
    private $configFile;
    
    public function __construct() {
        $this->configFile = BASE_PATH . '/config/settings.json';
    }
    
    /**
     * Dashboard de Configuración
     */
    public function index() {
        $this->requireRole('admin');
        
        $settings = $this->getSettings();
        
        $data = [
            'pageTitle' => 'Configuración del Sistema',
            'settings' => $settings
        ];
        
        $this->view('configuracion/index', $data);
    }
    
    /**
     * Guardar configuración
     */
    public function update() {
        $this->requireRole('admin');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/configuracion');
        }
        
        if (!Csrf::verify($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Error de seguridad: Token CSRF inválido';
            $this->redirect('/configuracion');
        }
        
        $newSettings = [
            'empresa_nombre' => $_POST['empresa_nombre'] ?? 'SIARH',
            'empresa_direccion' => $_POST['empresa_direccion'] ?? '',
            'timezone' => $_POST['timezone'] ?? 'America/Lima',
            'backup_frecuencia' => $_POST['backup_frecuencia'] ?? 'diario',
            'permitir_registro_tardio' => isset($_POST['permitir_registro_tardio']),
            'minutos_tolerancia' => intval($_POST['minutos_tolerancia'] ?? 15),
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_by' => $_SESSION['username']
        ];
        
        if ($this->saveSettings($newSettings)) {
            $_SESSION['success'] = 'Configuración actualizada exitosamente';
        } else {
            $_SESSION['error'] = 'Error al guardar la configuración';
        }
        
        $this->redirect('/configuracion');
    }
    
    /**
     * Obtener ajustes actuales
     */
    private function getSettings() {
        if (file_exists($this->configFile)) {
            $content = file_get_contents($this->configFile);
            return json_decode($content, true) ?? $this->getDefaultSettings();
        }
        return $this->getDefaultSettings();
    }
    
    /**
     * Guardar ajustes
     */
    private function saveSettings($settings) {
        $content = json_encode($settings, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        return file_put_contents($this->configFile, $content) !== false;
    }
    
    /**
     * Ajustes por defecto
     */
    private function getDefaultSettings() {
        return [
            'empresa_nombre' => 'SIARH',
            'empresa_direccion' => 'Av. Principal 123',
            'timezone' => 'America/Lima',
            'backup_frecuencia' => 'diario',
            'permitir_registro_tardio' => true,
            'minutos_tolerancia' => 15
        ];
    }
}
