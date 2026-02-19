<?php
/**
 * Licencia Model
 */

class Licencia extends Model {
    protected $table = 'licencias';
    
    /**
     * Obtener licencias con información completa
     */
    public function getAllWithDetails($filters = []) {
        $sql = "SELECT l.*, 
                       CONCAT(d.nombres, ' ', d.apellidos) as nombre_docente,
                       d.codigo_empleado,
                       c.nombre as carrera,
                       u.username as aprobado_por_username
                FROM {$this->table} l
                INNER JOIN docentes d ON l.docente_id = d.id
                LEFT JOIN carreras c ON d.carrera_id = c.id
                LEFT JOIN usuarios u ON l.aprobado_por = u.id";
        
        $params = [];
        if (!empty($filters)) {
            $conditions = [];
            foreach ($filters as $key => $value) {
                // Prefijar con 'l.' si no tiene punto para evitar ambigüedad
                $field = (strpos($key, '.') === false) ? "l.{$key}" : $key;
                // Manejar nombre de parámetro seguro
                $paramName = str_replace('.', '_', $key);
                
                $conditions[] = "{$field} = :{$paramName}";
                $params[":{$paramName}"] = $value;
            }
            $sql .= " WHERE " . implode(' AND ', $conditions);
        }
        
        $sql .= " ORDER BY l.created_at DESC";
        
        return $this->query($sql, $params);
    }
    
    /**
     * Obtener licencias pendientes
     */
    public function getPendientes() {
        return $this->getAll(['estado' => 'pendiente'], 'created_at DESC');
    }
    
    /**
     * Aprobar licencia
     */
    public function aprobar($id, $userId, $comentarios = '') {
        return $this->update($id, [
            'estado' => 'aprobado',
            'aprobado_por' => $userId,
            'fecha_aprobacion' => date('Y-m-d H:i:s'),
            'comentarios_aprobacion' => $comentarios
        ]);
    }
    
    /**
     * Rechazar licencia
     */
    public function rechazar($id, $userId, $comentarios) {
        return $this->update($id, [
            'estado' => 'rechazado',
            'aprobado_por' => $userId,
            'fecha_aprobacion' => date('Y-m-d H:i:s'),
            'comentarios_aprobacion' => $comentarios
        ]);
    }
    
    /**
     * Verificar licencia activa
     */
    public function tieneActiva($docenteId, $fecha) {
        $sql = "SELECT COUNT(*) as count FROM {$this->table}
                WHERE docente_id = :docente_id
                  AND estado = 'aprobado'
                  AND :fecha BETWEEN fecha_inicio AND fecha_fin";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['docente_id' => $docenteId, 'fecha' => $fecha]);
        $result = $stmt->fetch();
        return $result['count'] > 0;
    }
    
    /**
     * Obtener licencias por vencer (próximos 7 días)
     */
    public function getPorVencer() {
        $sql = "SELECT l.*, 
                       CONCAT(d.nombres, ' ', d.apellidos) as nombre_docente
                FROM {$this->table} l
                INNER JOIN docentes d ON l.docente_id = d.id
                WHERE l.estado = 'aprobado'
                  AND l.fecha_fin BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)
                ORDER BY l.fecha_fin";
        
        return $this->query($sql);
    }
}
