<?php
/**
 * Script de Creación/Reparación de Super Admin
 * Ejecutar desde: http://localhost/siarh/create_superadmin.php
 */

require_once __DIR__ . '/config/config.php';

echo "<h1>SIARH - Gestión de Super Admin</h1>";
echo "<style>body{font-family:Arial;padding:20px;} .success{color:green;} .error{color:red;} .info{color:blue;} .warning{color:orange;}</style>";

try {
    $db = Database::getInstance()->getConnection();
    echo "<p class='success'>✓ Conexión a base de datos exitosa</p>";
    
    $username = 'superadmin';
    $password = 'superadmin123';
    $role = 'super_admin';
    $email = 'superadmin@siarh.com';
    
    // 1. Verificar si existe el usuario
    $stmt = $db->prepare("SELECT * FROM usuarios WHERE username = :u");
    $stmt->execute(['u' => $username]);
    $user = $stmt->fetch();
    
    if ($user) {
        echo "<p class='info'>El usuario '{$username}' ya existe (ID: {$user['id']})</p>";
        
        // Actualizar rol y contraseña para asegurar acceso
        $newHash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 10]);
        
        $update = $db->prepare("UPDATE usuarios SET password = :p, rol = :r, estado = 'activo' WHERE id = :id");
        $update->execute([
            'p' => $newHash,
            'r' => $role,
            'id' => $user['id']
        ]);
        
        echo "<p class='success'>✓ Usuario actualizado correcamente. Rol: {$role}, Contraseña: {$password}</p>";
        
    } else {
        echo "<p class='warning'>El usuario '{$username}' no existe. Creando...</p>";
        
        // Verificar email duplicado (por si otro usuario tiene ese email)
        $checkEmail = $db->prepare("SELECT id FROM usuarios WHERE email = :email");
        $checkEmail->execute(['email' => $email]);
        if ($checkEmail->fetch()) {
            $email = 'superadmin_new@siarh.com'; 
            echo "<p class='warning'>El email {$email} ya existe, usando {$email}</p>";
        }
        
        $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 10]);
        
        $insert = $db->prepare("INSERT INTO usuarios (username, password, rol, email, estado, ip_registro) VALUES (:u, :p, :r, :e, 'activo', :ip)");
        $insert->execute([
            'u' => $username,
            'p' => $hash,
            'r' => $role,
            'e' => $email,
            'ip' => '127.0.0.1'
        ]);
        
        echo "<p class='success'>✓ Usuario '{$username}' creado exitosamente con rol '{$role}'</p>";
    }
    
    echo "<div style='background:#f0f0f0; padding:15px; margin-top:20px; border-radius:5px;'>";
    echo "<h3>Credenciales de Acceso Super Admin:</h3>";
    echo "<p><strong>Usuario:</strong> {$username}</p>";
    echo "<p><strong>Contraseña:</strong> {$password}</p>";
    echo "<p><em>Este usuario tiene acceso TOTAL al sistema sin restricciones.</em></p>";
    echo "</div>";
    
    echo "<p><a href='" . APP_URL . "/login' style='background:#6366f1;color:white;padding:10px 20px;text-decoration:none;border-radius:5px;'>Ir al Login</a></p>";

} catch (Exception $e) {
    echo "<p class='error'>Error: " . $e->getMessage() . "</p>";
}
