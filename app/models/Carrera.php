<?php
/**
 * Carrera Model
 */

class Carrera extends Model {
    protected $table = 'carreras';
    
    /**
     * Obtener carreras activas
     */
    public function getActivas() {
        return $this->getAll(['estado' => 'activo'], 'nombre ASC');
    }
    
    /**
     * Obtener carrera con estadísticas
     */
    public function getWithStats($id) {
        $sql = "SELECT c.*, 
                       COUNT(d.id) as total_docentes,
                       SUM(CASE WHEN d.estado = 'activo' THEN 1 ELSE 0 END) as docentes_activos
                FROM {$this->table} c
                LEFT JOIN docentes d ON c.id = d.carrera_id
                WHERE c.id = :id
                GROUP BY c.id";
        
        $result = $this->query($sql, ['id' => $id]);
        return $result[0] ?? null;
    }
}
