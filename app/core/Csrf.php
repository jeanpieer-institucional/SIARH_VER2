<?php
/**
 * Csrf Helper Class
 * Protege contra ataques Cross-Site Request Forgery
 */

class Csrf {
    /**
     * Generar y guardar un token CSRF en la sesión
     */
    public static function generate() {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
    
    /**
     * Obtener el token actual
     */
    public static function getToken() {
        return $_SESSION['csrf_token'] ?? self::generate();
    }
    
    /**
     * Verificar si el token recibido coincide con el de la sesión
     */
    public static function verify($token) {
        if (!isset($_SESSION['csrf_token']) || empty($token)) {
            return false;
        }
        return hash_equals($_SESSION['csrf_token'], $token);
    }
    
    /**
     * Generar un campo input hidden con el token
     */
    public static function input() {
        $token = self::getToken();
        return '<input type="hidden" name="csrf_token" value="' . $token . '">';
    }
}
