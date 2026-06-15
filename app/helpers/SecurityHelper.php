<?php
/**
 * SecurityHelper - Funciones de ayuda para seguridad y saneamiento
 */

class SecurityHelper {
    /**
     * Escapa caracteres especiales de HTML para prevenir ataques XSS (Cross-Site Scripting).
     *
     * @param string|null $string La cadena a escapar.
     * @return string La cadena escapada de forma segura.
     */
    public static function escape($string) {
        if ($string === null) {
            return '';
        }
        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }
}
