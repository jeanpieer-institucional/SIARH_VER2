<?php
/**
 * Docente Model
 */

class Docente extends Model {
    protected $table = 'docentes';
    
    /**
     * Obtener docentes con información de carrera
     */
    public function getAllWithCarrera() {
        $sql = "SELECT d.*, c.nombre as carrera_nombre, c.codigo as carrera_codigo,
                       u.username, u.email as usuario_email
                FROM {$this->table} d
                LEFT JOIN carreras c ON d.carrera_id = c.id
                LEFT JOIN usuarios u ON d.usuario_id = u.id
                ORDER BY d.apellidos, d.nombres";
        return $this->query($sql);
    }
    
    /**
     * Buscar docentes
     */
    public function search($term, $filters = []) {
        $sql = "SELECT d.*, c.nombre as carrera_nombre
                FROM {$this->table} d
                LEFT JOIN carreras c ON d.carrera_id = c.id
                WHERE (d.nombres LIKE :term1 
                   OR d.apellidos LIKE :term2 
                   OR d.dni LIKE :term3 
                   OR d.codigo_empleado LIKE :term4)";
        
        $termLike = "%{$term}%";
        $params = [
            'term1' => $termLike,
            'term2' => $termLike,
            'term3' => $termLike,
            'term4' => $termLike
        ];
        
        if (!empty($filters['estado'])) {
            $sql .= " AND d.estado = :estado";
            $params['estado'] = $filters['estado'];
        }
        
        if (!empty($filters['carrera_id'])) {
            $sql .= " AND d.carrera_id = :carrera_id";
            $params['carrera_id'] = $filters['carrera_id'];
        }
        
        $sql .= " ORDER BY d.apellidos, d.nombres";
        
        return $this->query($sql, $params);
    }
    
    /**
     * Obtener estadísticas de docentes
     */
    public function getEstadisticas() {
        $stats = [];
        
        // Total de docentes
        $stats['total'] = $this->count();
        
        // Docentes activos
        $stats['activos'] = $this->count(['estado' => 'activo']);
        
        // Docentes con huella
        $sql = "SELECT COUNT(*) as total FROM {$this->table} WHERE tiene_huella = 1";
        $result = $this->query($sql);
        $stats['con_huella'] = $result[0]['total'];
        
        // Por carrera
        $sql = "SELECT c.nombre, COUNT(d.id) as total
                FROM carreras c
                LEFT JOIN {$this->table} d ON c.id = d.carrera_id
                GROUP BY c.id, c.nombre";
        $stats['por_carrera'] = $this->query($sql);
        
        return $stats;
    }
    
    /**
     * Verificar DNI único
     */
    public function dniExists($dni, $excludeId = null) {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE dni = :dni";
        if ($excludeId) {
            $sql .= " AND id != :id";
        }
        $stmt = $this->db->prepare($sql);
        $params = ['dni' => $dni];
        if ($excludeId) {
            $params['id'] = $excludeId;
        }
        $stmt->execute($params);
        $result = $stmt->fetch();
        return $result['count'] > 0;
    }
    
    /**
     * Generar código de empleado único
     */
    public function generarCodigoEmpleado() {
        $year = date('Y');
        $sql = "SELECT codigo_empleado FROM {$this->table} 
                WHERE codigo_empleado LIKE :pattern 
                ORDER BY codigo_empleado DESC LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['pattern' => "DOC{$year}%"]);
        $last = $stmt->fetch();
        
        if ($last) {
            $num = intval(substr($last['codigo_empleado'], -4)) + 1;
        } else {
            $num = 1;
        }
        
        return "DOC{$year}" . str_pad($num, 4, '0', STR_PAD_LEFT);
    }
}
