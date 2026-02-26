<?php
/**
 * Documento Model
 */

class Documento extends Model {
    protected $table = 'documentos_docente';
    
    /**
     * Obtener documentos de un docente
     */
    public function getByDocente($docenteId) {
        $sql = "SELECT d.*, u.username as subido_por_username 
                FROM {$this->table} d
                LEFT JOIN usuarios u ON d.subido_por = u.id
                WHERE d.docente_id = :docente_id 
                ORDER BY d.fecha_subida DESC";
                
        return $this->query($sql, ['docente_id' => $docenteId]);
    }
}
