<?php
/**
 * Script para corregir el problema del rol Super Admin
 * 1. Modifica la tabla usuarios para permitir el rol 'super_admin'
 * 2. Asigna correctamente el rol al usuario 'superadmin'
 */

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/app/core/Database.php';

echo "Iniciando reparación del sistema de roles...\n";

try {
    $db = Database::getInstance()->getConnection();
    
    // 1. Alterar la tabla para permitir 'super_admin'
    echo "1. Actualizando estructura de la base de datos... ";
    $sql = "ALTER TABLE usuarios MODIFY COLUMN rol ENUM('admin', 'supervisor', 'docente', 'super_admin') NOT NULL";
    $db->exec($sql);
    echo "OK\n";
    
    // 2. Corregir el usuario superadmin
    echo "2. Corrigiendo usuario 'superadmin'... ";
    $stmt = $db->prepare("UPDATE usuarios SET rol = 'super_admin' WHERE username = 'superadmin'");
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        echo "OK (Usuario actualizado)\n";
    } else {
        echo "OK (No se requirieron cambios o usuario no encontrado)\n";
    }
    
    echo "\n¡Reparación completada con éxito!\n";
    echo "Por favor, CIERRE SESIÓN y vuelva a ingresar para ver los cambios.\n";

} catch (PDOException $e) {
    echo "\nError de Base de Datos: " . $e->getMessage() . "\n";
} catch (Exception $e) {
    echo "\nError General: " . $e->getMessage() . "\n";
}
