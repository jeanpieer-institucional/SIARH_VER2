<?php
/**
 * Horario Model
 */

class Horario extends Model {
    protected $table = 'horarios_docente';
    
    /**
     * Obtener todos los horarios de un docente con información del docente
     */
    public function getByDocente($docenteId) {
        $sql = "SELECT h.*, CONCAT(d.nombres, ' ', d.apellidos) as docente_nombre 
                FROM {$this->table} h
                INNER JOIN docentes d ON h.docente_id = d.id
                WHERE h.docente_id = :docente_id 
                ORDER BY FIELD(h.dia_semana, 'lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado', 'domingo'), h.hora_entrada ASC";
        return $this->query($sql, ['docente_id' => $docenteId]);
    }
    
    /**
     * Obtener el horario de un docente para un día específico
     */
    public function getByDocenteYDia($docenteId, $diaSemana) {
        $sql = "SELECT * FROM {$this->table} 
                WHERE docente_id = :docente_id 
                AND dia_semana = :dia_semana 
                AND estado = 'activo' 
                LIMIT 1";
        $result = $this->query($sql, [
            'docente_id' => $docenteId,
            'dia_semana' => strtolower($diaSemana)
        ]);
        return !empty($result) ? $result[0] : null;
    }
    
    /**
     * Verificar si existe un cruce de horarios para un docente en un mismo día
     */
    public function verificarCruce($docenteId, $diaSemana, $horaEntrada, $horaSalida, $excludeId = null) {
        $sql = "SELECT COUNT(*) as total FROM {$this->table} 
                WHERE docente_id = :docente_id 
                AND dia_semana = :dia_semana 
                AND estado = 'activo' 
                AND (:hora_entrada < hora_salida AND :hora_salida > hora_entrada)";
        
        $params = [
            'docente_id' => $docenteId,
            'dia_semana' => $diaSemana,
            'hora_entrada' => $horaEntrada,
            'hora_salida' => $horaSalida
        ];
        
        if ($excludeId !== null) {
            $sql .= " AND id != :exclude_id";
            $params['exclude_id'] = $excludeId;
        }
        
        $result = $this->query($sql, $params);
        return $result[0]['total'] > 0;
    }
}
