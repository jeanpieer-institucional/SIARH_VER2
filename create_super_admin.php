<?php
/**
 * Script para crear un Super Admin
 * Ejecutar desde línea de comandos o navegador
 */

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/app/core/Database.php';
require_once __DIR__ . '/app/core/Model.php';
require_once __DIR__ . '/app/models/Usuario.php';

// Configuración del usuario
$username = 'superadmin';
$password = 'superadmin123'; // ¡Cambiar esto después!
$email = 'superadmin@siarh.com';

echo "Creando usuario Super Admin...\n";
echo "Usuario: $username\n";
echo "Password: $password\n";

try {
    $usuarioModel = new Usuario();
    
    // Verificar si ya existe
    if ($usuarioModel->usernameExists($username)) {
        echo "Error: El usuario '$username' ya existe.\n";
        exit;
    }
    
    // Crear usuario
    $result = $usuarioModel->createUser([
        'username' => $username,
        'password' => $password,
        'email' => $email,
        'rol' => 'super_admin',
        'estado' => 'activo'
    ]);
    
    if ($result) {
        echo "¡Éxito! Usuario Super Admin creado correctamente.\n";
        echo "Ahora puedes iniciar sesión y administrar el sistema.\n";
    } else {
        echo "Error: No se pudo crear el usuario.\n";
    }
    
} catch (Exception $e) {
    echo "Excepción: " . $e->getMessage() . "\n";
}
