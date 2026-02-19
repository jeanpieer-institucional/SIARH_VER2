<?php
/**
 * SIARH - Sistema Integral de Asistencia y RRHH
 * Archivo de Configuración Principal
 */

// Configuración de Base de Datos
define('DB_HOST', 'localhost');
define('DB_NAME', 'siarh_db');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Configuración de la Aplicación
define('APP_NAME', 'SIARH');
define('APP_VERSION', '1.0.0');
define('APP_URL', 'http://localhost/siarh');
define('BASE_PATH', dirname(__DIR__));

// Configuración de Sesión
define('SESSION_LIFETIME', 3600); // 1 hora
define('SESSION_NAME', 'SIARH_SESSION');

// Configuración de Seguridad
define('BCRYPT_COST', 10);
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOCKOUT_TIME', 900); // 15 minutos

// Configuración de Archivos
define('UPLOAD_DIR', BASE_PATH . '/uploads');
define('MAX_FILE_SIZE', 5242880); // 5MB
define('ALLOWED_EXTENSIONS', ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx']);

// Configuración de Zona Horaria
date_default_timezone_set('America/Lima');

// Configuración de Errores (Desarrollo)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Configuración de Logs
define('LOG_DIR', BASE_PATH . '/logs');
define('LOG_LEVEL', 'DEBUG'); // DEBUG, INFO, WARNING, ERROR

// Configuración de Reportes
define('REPORTS_DIR', BASE_PATH . '/reports');
define('TEMP_DIR', BASE_PATH . '/temp');

// Autoload de clases
spl_autoload_register(function ($class) {
    $paths = [
        BASE_PATH . '/app/models/',
        BASE_PATH . '/app/controllers/',
        BASE_PATH . '/app/core/',
        BASE_PATH . '/app/helpers/'
    ];
    
    foreach ($paths as $path) {
        $file = $path . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// Iniciar sesión
session_name(SESSION_NAME);
session_start();
