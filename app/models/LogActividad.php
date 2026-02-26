<?php
/**
 * LogActividad Model
 */

class LogActividad extends Model {
    protected $table = 'logs_actividad';
    
    /**
     * Registrar actividad
     */
    public function registrar($usuarioId, $accion, $modulo, $descripcion = '', $datosAnteriores = null, $datosNuevos = null) {
        return $this->create([
            'usuario_id' => $usuarioId,
            'accion' => $accion,
            'modulo' => $modulo,
            'descripcion' => $descripcion,
            'datos_anteriores' => $datosAnteriores ? json_encode($datosAnteriores) : null,
            'datos_nuevos' => $datosNuevos ? json_encode($datosNuevos) : null,
            'ip_address' => $_SERVER['REMOTE_ADDR'],
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? ''
        ]);
    }
    
    /**
     * Obtener logs recientes
     */
    public function getRecientes($limit = 50) {
        $sql = "SELECT l.*, u.username
                FROM {$this->table} l
                LEFT JOIN usuarios u ON l.usuario_id = u.id
                ORDER BY l.created_at DESC
                LIMIT :limit";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Obtener logs por usuario
     */
    public function getByUsuario($usuarioId, $limit = 100) {
        $sql = "SELECT * FROM {$this->table}
                WHERE usuario_id = :usuario_id
                ORDER BY created_at DESC
                LIMIT :limit";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':usuario_id', $usuarioId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Obtener logs por módulo
     */
    public function getByModulo($modulo, $fechaInicio = null, $fechaFin = null) {
        $sql = "SELECT l.*, u.username
                FROM {$this->table} l
                LEFT JOIN usuarios u ON l.usuario_id = u.id
                WHERE l.modulo = :modulo";
        
        $params = ['modulo' => $modulo];
        
        if ($fechaInicio) {
            $sql .= " AND DATE(l.created_at) >= :fecha_inicio";
            $params['fecha_inicio'] = $fechaInicio;
        }
        
        if ($fechaFin) {
            $sql .= " AND DATE(l.created_at) <= :fecha_fin";
            $params['fecha_fin'] = $fechaFin;
        }
        
        $sql .= " ORDER BY l.created_at DESC";
        
        return $this->query($sql, $params);
    }
}
