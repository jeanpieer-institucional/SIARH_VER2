<?php
/**
 * Evaluacion Model
 */

class Evaluacion extends Model {
    protected $table = 'evaluaciones_docente';
    
    /**
     * Obtener todas las evaluaciones de un docente con info del evaluador
     */
    public function getByDocente($docenteId) {
        $sql = "SELECT e.*, 
                       u.username as evaluador_nombre,
                       CONCAT(d.nombres, ' ', d.apellidos) as docente_nombre
                FROM {$this->table} e
                INNER JOIN usuarios u ON e.evaluador_id = u.id
                INNER JOIN docentes d ON e.docente_id = d.id
                WHERE e.docente_id = :docente_id 
                ORDER BY e.created_at DESC";
        return $this->query($sql, ['docente_id' => $docenteId]);
    }
    
    /**
     * Obtener promedios de calificación de un docente
     */
    public function getPromedioPorDocente($docenteId) {
        $sql = "SELECT 
                    AVG(puntuacion_metodologia) as promedio_metodologia,
                    AVG(puntuacion_puntualidad) as promedio_puntualidad,
                    AVG(puntuacion_relacion) as promedio_relacion,
                    COUNT(*) as total_evaluaciones
                FROM {$this->table}
                WHERE docente_id = :docente_id";
        $result = $this->query($sql, ['docente_id' => $docenteId]);
        
        if (!empty($result) && $result[0]['total_evaluaciones'] > 0) {
            $prom = $result[0];
            $prom['promedio_general'] = ($prom['promedio_metodologia'] + $prom['promedio_puntualidad'] + $prom['promedio_relacion']) / 3;
            return $prom;
        }
        
        return [
            'promedio_metodologia' => 0,
            'promedio_puntualidad' => 0,
            'promedio_relacion' => 0,
            'promedio_general' => 0,
            'total_evaluaciones' => 0
        ];
    }
}
