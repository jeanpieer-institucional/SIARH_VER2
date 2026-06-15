<?php
/**
 * MailHelper - Utilitario para envío de notificaciones por Correo Electrónico
 */

class MailHelper {
    
    /**
     * Enviar un correo electrónico
     * 
     * @param string $to Correo del destinatario
     * @param string $subject Asunto del correo
     * @param string $body Contenido en HTML o Texto plano
     * @return bool True si se envió o registró con éxito, False en caso contrario
     */
    public static function send($to, $subject, $body) {
        $isLocal = ($_SERVER['HTTP_HOST'] === 'localhost' || $_SERVER['SERVER_ADDR'] === '127.0.0.1' || $_SERVER['REMOTE_ADDR'] === '::1');
        
        if ($isLocal) {
            // En entorno local, registramos el correo en un log para auditoría sin requerir servidor SMTP configurado
            $logPath = BASE_PATH . '/logs/email.log';
            $timestamp = date('Y-m-d H:i:s');
            
            $logEntry = "=================================================================\n";
            $logEntry .= "[{$timestamp}] ENVÍO DE CORREO LOCAL (SIMULADO)\n";
            $logEntry .= "PARA: {$to}\n";
            $logEntry .= "ASUNTO: {$subject}\n";
            $logEntry .= "-----------------------------------------------------------------\n";
            $logEntry .= "CONTENIDO:\n{$body}\n";
            $logEntry .= "=================================================================\n\n";
            
            return file_put_contents($logPath, $logEntry, FILE_APPEND) !== false;
        } else {
            // En producción, usamos la función mail() nativa configurada en el servidor de hosting
            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
            $headers .= "From: SIARH Notificaciones <no-reply@siarh-sistema.com>" . "\r\n";
            
            // Cuerpo HTML básico para el correo
            $htmlBody = "
            <html>
            <head>
                <title>" . htmlspecialchars($subject) . "</title>
                <style>
                    body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                    .container { max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px; }
                    .header { background: #4f46e5; color: white; padding: 10px 20px; text-align: center; border-radius: 5px 5px 0 0; }
                    .content { padding: 20px; }
                    .footer { text-align: center; font-size: 0.8em; color: #777; margin-top: 20px; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <h2>SIARH - Control de Personal</h2>
                    </div>
                    <div class='content'>
                        {$body}
                    </div>
                    <div class='footer'>
                        <p>© " . date('Y') . " SIARH. Todos los derechos reservados.</p>
                    </div>
                </div>
            </body>
            </html>
            ";
            
            return mail($to, utf8_decode($subject), $htmlBody, $headers);
        }
    }
}
