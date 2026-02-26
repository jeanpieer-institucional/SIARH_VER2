<?php
/**
 * Script para reiniciar el sistema y dejarlo limpio para un cliente.
 * ADVERTENCIA: Este script elimina todos los docentes, asistencias, licencias, registros y usuarios.
 */

require_once __DIR__ . '/config/config.php';

try {
    $db = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset='.DB_CHARSET, DB_USER, DB_PASS);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Deshabilitar la verificación de llaves foráneas para poder hacer TRUNCATE
    $db->exec('SET FOREIGN_KEY_CHECKS = 0;');

    // Tablas a vaciar
    $tables = [
        'notificaciones',
        'logs_actividad',
        'licencias',
        'asistencias',
        'docentes',
        'carreras',
        'usuarios'
    ];

    foreach ($tables as $table) {
        $db->exec("TRUNCATE TABLE {$table};");
    }

    // Volver a habilitar llaves foráneas
    $db->exec('SET FOREIGN_KEY_CHECKS = 1;');

    // Crear un único super_admin limpio
    $username = 'superadmin';
    $plainPassword = 'superadmin123';
    $password = password_hash($plainPassword, PASSWORD_BCRYPT, ['cost' => BCRYPT_COST]);
    $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';

    $stmt = $db->prepare("INSERT INTO usuarios (username, password, rol, email, estado, ip_registro) VALUES (:username, :password, 'super_admin', 'contacto@sistema.com', 'activo', :ip)");
    $stmt->execute([
        'username' => $username,
        'password' => $password,
        'ip' => $ip
    ]);

    echo "<h1>Sistema Reiniciado Exitosamente</h1>";
    echo "<p style='color: green;'>✅ Se han eliminado todos los registros operativos (docentes, asistencias, licencias, reportes).</p>";
    echo "<p style='color: green;'>✅ Se configuró una cuenta limpia de super_admin para entrega.</p>";
    
    echo "<h3>Nuevas Credenciales Únicas del Sistema:</h3>";
    echo "<ul>";
    echo "<li><b>Usuario:</b> {$username}</li>";
    echo "<li><b>Contraseña:</b> {$plainPassword}</li>";
    echo "</ul>";
    
    echo "<p>⚠️ <b>Recomendación de Seguridad:</b> Borra o renombra este archivo (<code>limpiar_sistema.php</code>) antes de subirlo a producción para evitar que alguien borre el sistema por accidente.</p>";
    echo "<a href='" . APP_URL . "/login'>Ir a Iniciar Sesión</a>";

} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error al reiniciar la base de datos: " . $e->getMessage() . "</p>";
}
