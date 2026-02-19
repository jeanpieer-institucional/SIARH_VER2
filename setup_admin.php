<?php
/**
 * Script de Verificación y Creación de Usuario Admin
 * Ejecutar desde: http://localhost/siarh/setup_admin.php
 */

// Cargar configuración
require_once __DIR__ . '/config/config.php';

echo "<h1>SIARH - Configuración de Usuario Admin</h1>";
echo "<style>body{font-family:Arial;padding:20px;} .success{color:green;} .error{color:red;} .info{color:blue;}</style>";

try {
    $db = Database::getInstance()->getConnection();
    echo "<p class='success'>✓ Conexión a base de datos exitosa</p>";
    
    // Verificar si existe la tabla usuarios
    $stmt = $db->query("SHOW TABLES LIKE 'usuarios'");
    if ($stmt->rowCount() == 0) {
        echo "<p class='error'>✗ La tabla 'usuarios' no existe. Por favor importa el archivo schema.sql primero.</p>";
        echo "<p class='info'>Importa: c:\\xampp\\htdocs\\siarh\\database\\schema.sql</p>";
        exit;
    }
    echo "<p class='success'>✓ Tabla 'usuarios' existe</p>";
    
    // Verificar si existe el usuario admin
    $stmt = $db->prepare("SELECT * FROM usuarios WHERE username = 'admin'");
    $stmt->execute();
    $admin = $stmt->fetch();
    
    if ($admin) {
        echo "<p class='info'>Usuario 'admin' encontrado en la base de datos</p>";
        echo "<p>ID: {$admin['id']}</p>";
        echo "<p>Email: {$admin['email']}</p>";
        echo "<p>Rol: {$admin['rol']}</p>";
        echo "<p>Estado: {$admin['estado']}</p>";
        
        // Probar la contraseña
        $testPassword = 'admin123';
        if (password_verify($testPassword, $admin['password'])) {
            echo "<p class='success'>✓ La contraseña 'admin123' es CORRECTA</p>";
            echo "<h3>Puedes iniciar sesión con:</h3>";
            echo "<p><strong>Usuario:</strong> admin</p>";
            echo "<p><strong>Contraseña:</strong> admin123</p>";
        } else {
            echo "<p class='error'>✗ La contraseña 'admin123' NO coincide con el hash almacenado</p>";
            echo "<p class='info'>Regenerando contraseña...</p>";
            
            // Regenerar contraseña
            $newHash = password_hash('admin123', PASSWORD_BCRYPT, ['cost' => 10]);
            $updateStmt = $db->prepare("UPDATE usuarios SET password = :password WHERE username = 'admin'");
            $updateStmt->execute(['password' => $newHash]);
            
            echo "<p class='success'>✓ Contraseña regenerada exitosamente</p>";
            echo "<h3>Ahora puedes iniciar sesión con:</h3>";
            echo "<p><strong>Usuario:</strong> admin</p>";
            echo "<p><strong>Contraseña:</strong> admin123</p>";
        }
    } else {
        echo "<p class='error'>✗ Usuario 'admin' NO existe en la base de datos</p>";
        echo "<p class='info'>Creando usuario admin...</p>";
        
        // Crear usuario admin
        $password = password_hash('admin123', PASSWORD_BCRYPT, ['cost' => 10]);
        $stmt = $db->prepare("INSERT INTO usuarios (username, password, rol, email, estado, ip_registro) VALUES (:username, :password, :rol, :email, :estado, :ip)");
        $stmt->execute([
            'username' => 'admin',
            'password' => $password,
            'rol' => 'admin',
            'email' => 'admin@siarh.com',
            'estado' => 'activo',
            'ip' => '127.0.0.1'
        ]);
        
        echo "<p class='success'>✓ Usuario admin creado exitosamente</p>";
        echo "<h3>Puedes iniciar sesión con:</h3>";
        echo "<p><strong>Usuario:</strong> admin</p>";
        echo "<p><strong>Contraseña:</strong> admin123</p>";
    }
    
    echo "<hr>";
    echo "<h3>Siguiente paso:</h3>";
    echo "<p><a href='" . APP_URL . "' style='background:#6366f1;color:white;padding:10px 20px;text-decoration:none;border-radius:5px;'>Ir al Sistema SIARH</a></p>";
    
} catch (Exception $e) {
    echo "<p class='error'>✗ Error: " . $e->getMessage() . "</p>";
    echo "<p class='info'>Asegúrate de que XAMPP esté ejecutándose y que hayas importado la base de datos.</p>";
}
?>
