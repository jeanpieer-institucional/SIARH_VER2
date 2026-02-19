<?php
/**
 * Script de Verificación y Corrección de Usuario Supervisor
 * Ejecutar desde: http://localhost/siarh/fix_supervisor.php
 */

// Cargar configuración
require_once __DIR__ . '/config/config.php';

echo "<h1>SIARH - Reparación de Cuenta Supervisor</h1>";
echo "<style>body{font-family:Arial;padding:20px;} .success{color:green;} .error{color:red;} .info{color:blue;} .warning{color:orange;}</style>";

try {
    $db = Database::getInstance()->getConnection();
    echo "<p class='success'>✓ Conexión a base de datos exitosa</p>";
    
    // Verificar si existe el rol supervisor en la tabla
    // El enum de roles es: 'admin', 'supervisor', 'docente', 'super_admin'
    // Vamos a buscar un usuario con rol 'supervisor'
    
    // 1. Verificar si existe el usuario 'supervisor'
    $stmt = $db->prepare("SELECT * FROM usuarios WHERE username = 'supervisor'");
    $stmt->execute();
    $user = $stmt->fetch();
    
    if ($user) {
        echo "<p class='info'>Usuario 'supervisor' encontrado (ID: {$user['id']})</p>";
        
        // Verificar contraseña actual
        $testPass = 'supervisor123';
        if (password_verify($testPass, $user['password'])) {
            echo "<p class='success'>✓ La contraseña actual ES 'supervisor123'</p>";
        } else {
            echo "<p class='warning'>⚠ La contraseña actual NO es 'supervisor123'</p>";
            
            // Actualizar contraseña
            $newHash = password_hash($testPass, PASSWORD_BCRYPT, ['cost' => 10]);
            $update = $db->prepare("UPDATE usuarios SET password = :p WHERE id = :id");
            $update->execute(['p' => $newHash, 'id' => $user['id']]);
            
            echo "<p class='success'>✓ Contraseña restablecida a: <strong>supervisor123</strong></p>";
        }
        
    } else {
        echo "<p class='warning'>⚠ El usuario 'supervisor' no existe.</p>";
        
        // Verificar si existe 'supervisor1' (del archivo de datos de ejemplo)
        $stmt2 = $db->prepare("SELECT * FROM usuarios WHERE username = 'supervisor1'");
        $stmt2->execute();
        $user2 = $stmt2->fetch();
        
        if ($user2) {
             echo "<p class='info'>Se encontró el usuario 'supervisor1' en su lugar. ¿Quizás intentabas entrar con ese?</p>";
             echo "<p>Si deseas usar 'supervisor', lo crearé ahora.</p>";
        }
        
        // Crear usuario supervisor
        echo "<p class='info'>Creando usuario 'supervisor'...</p>";
        
        $password = password_hash('supervisor123', PASSWORD_BCRYPT, ['cost' => 10]);
        $email = 'supervisor@siarh.com';
        
        // Verificar si el email ya existe para evitar error de unique constraint
        $checkEmail = $db->prepare("SELECT id FROM usuarios WHERE email = :email");
        $checkEmail->execute(['email' => $email]);
        if ($checkEmail->fetch()) {
            $email = 'supervisor_new@siarh.com'; // Email alternativo si ya existe
            echo "<p class='warning'>El email supervisor@siarh.com ya existe, usando {$email}</p>";
        }
        
        $insert = $db->prepare("INSERT INTO usuarios (username, password, rol, email, estado, ip_registro) VALUES (:u, :p, 'supervisor', :e, 'activo', :ip)");
        $insert->execute([
            'u' => 'supervisor',
            'p' => $password,
            'e' => $email,
            'ip' => '127.0.0.1'
        ]);
        
        echo "<p class='success'>✓ Usuario 'supervisor' creado exitosamente</p>";
    }
    
    echo "<div style='background:#f0f0f0; padding:15px; margin-top:20px; border-radius:5px;'>";
    echo "<h3>Credenciales de Acceso:</h3>";
    echo "<p><strong>Usuario:</strong> supervisor</p>";
    echo "<p><strong>Contraseña:</strong> supervisor123</p>";
    echo "</div>";
    
    echo "<p><a href='" . APP_URL . "/login' style='background:#6366f1;color:white;padding:10px 20px;text-decoration:none;border-radius:5px;'>Ir al Login</a></p>";

} catch (Exception $e) {
    echo "<p class='error'>Error: " . $e->getMessage() . "</p>";
}
