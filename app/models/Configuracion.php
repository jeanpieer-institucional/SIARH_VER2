<?php
/**
 * Configuracion Model
 */

class Configuracion extends Model {
    protected $table = 'configuracion';
    protected $primaryKey = 'id';
    
    /**
     * Obtener valor de configuración
     */
    public function getValor($clave) {
        $sql = "SELECT valor, tipo FROM {$this->table} WHERE clave = :clave LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['clave' => $clave]);
        $result = $stmt->fetch();
        
        if (!$result) {
            return null;
        }
        
        // Convertir según tipo
        switch ($result['tipo']) {
            case 'int':
                return intval($result['valor']);
            case 'boolean':
                return $result['valor'] === 'true';
            case 'json':
                return json_decode($result['valor'], true);
            default:
                return $result['valor'];
        }
    }
    
    /**
     * Establecer valor de configuración
     */
    public function setValor($clave, $valor) {
        $sql = "UPDATE {$this->table} SET valor = :valor WHERE clave = :clave";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['clave' => $clave, 'valor' => $valor]);
    }
    
    /**
     * Obtener todas las configuraciones agrupadas
     */
    public function getAllGrouped() {
        $all = $this->getAll();
        $grouped = [];
        
        foreach ($all as $config) {
            $grouped[$config['clave']] = $this->getValor($config['clave']);
        }
        
        return $grouped;
    }
}
