<?php
/**
 * Script para crear un usuario Super Admin
 * URL: http://localhost/SIARH_VER2/crear_super_admin.php
 */

require_once __DIR__ . '/config/config.php';

try {
    $db = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset='.DB_CHARSET, DB_USER, DB_PASS);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $username = 'superadmin';
    $plainPassword = 'superadmin123';
    
    // Generar hash
    $password = password_hash($plainPassword, PASSWORD_BCRYPT, ['cost' => BCRYPT_COST]);
    $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';

    // Verificar si ya existe
    $stmt = $db->prepare("SELECT id FROM usuarios WHERE username = :username");
    $stmt->execute(['username' => $username]);
    
    echo "<h1>Configuración de Super Admin</h1>";
    
    if ($stmt->rowCount() > 0) {
        // Ya existe, actualizar contraseña
        $stmt = $db->prepare("UPDATE usuarios SET password = :password, rol = 'super_admin' WHERE username = :username");
        $stmt->execute([
            'password' => $password,
            'username' => $username
        ]);
        echo "<p style='color: green;'>✅ El usuario <b>{$username}</b> ya existía y ha sido restablecido como super_admin.</p>";
    } else {
        // Insertar nuevo super_admin
        $stmt = $db->prepare("INSERT INTO usuarios (username, password, rol, email, estado, ip_registro) VALUES (:username, :password, 'super_admin', 'superadmin@siarh.com', 'activo', :ip)");
        $stmt->execute([
            'username' => $username,
            'password' => $password,
            'ip' => $ip
        ]);
        echo "<p style='color: blue;'>✅ Nuevo usuario <b>{$username}</b> creado exitosamente como super_admin.</p>";
    }
    
    echo "<h3>Credenciales:</h3>";
    echo "<ul>";
    echo "<li><b>Usuario:</b> {$username}</li>";
    echo "<li><b>Contraseña:</b> {$plainPassword}</li>";
    echo "</ul>";
    
    echo "<p>⚠️ <b>Atención:</b> Ya puedes iniciar sesión y eliminar a los demás usuarios desde la interfaz de 'Usuarios', si así lo deseas.</p>";
    echo "<a href='" . APP_URL . "/login'>Ir a Iniciar Sesión</a>";

} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error en la base de datos: " . $e->getMessage() . "</p>";
}
