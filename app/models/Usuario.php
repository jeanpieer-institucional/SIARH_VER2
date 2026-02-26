<?php
/**
 * Usuario Model
 */

class Usuario extends Model {
    protected $table = 'usuarios';
    
    /**
     * Autenticar usuario
     */
    public function authenticate($username, $password) {
        $sql = "SELECT * FROM {$this->table} WHERE username = :username AND estado = 'activo' LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            // Actualizar último acceso
            $this->update($user['id'], [
                'ultimo_acceso' => date('Y-m-d H:i:s'),
                'ip_registro' => $_SERVER['REMOTE_ADDR']
            ]);
            
            return $user;
        }
        
        return false;
    }
    
    /**
     * Crear usuario con password hasheado
     */
    public function createUser($data) {
        if (isset($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => BCRYPT_COST]);
        }
        $data['ip_registro'] = $_SERVER['REMOTE_ADDR'];
        return $this->create($data);
    }
    
    /**
     * Cambiar contraseña
     */
    public function changePassword($userId, $newPassword) {
        $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT, ['cost' => BCRYPT_COST]);
        return $this->update($userId, ['password' => $hashedPassword]);
    }
    
    /**
     * Incrementar intentos fallidos
     */
    public function incrementarIntentos($id) {
        $sql = "UPDATE {$this->table} SET intentos_fallidos = intentos_fallidos + 1 WHERE id = :id";
        return $this->execute($sql, ['id' => $id]);
    }
    
    /**
     * Bloquear temporalmente
     */
    public function bloquearTemporalmente($id) {
        return $this->update($id, [
            'bloqueado_hasta' => date('Y-m-d H:i:s', strtotime('+10 minutes')),
            'bloqueos_totales' => 1
        ]);
    }
    
    /**
     * Bloquear permanentemente
     */
    public function bloquearPermanentemente($id) {
        return $this->update($id, [
            'estado' => 'inactivo',
            'bloqueos_totales' => 2
        ]);
    }
    
    /**
     * Restablecer estado de seguridad de la cuenta
     */
    public function resetearSeguridad($id) {
        return $this->update($id, [
            'intentos_fallidos' => 0,
            'bloqueos_totales' => 0,
            'bloqueado_hasta' => null
        ]);
    }
    
    /**
     * Verificar si username existe
     */
    public function usernameExists($username, $excludeId = null) {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE username = :username";
        if ($excludeId) {
            $sql .= " AND id != :id";
        }
        $stmt = $this->db->prepare($sql);
        $params = ['username' => $username];
        if ($excludeId) {
            $params['id'] = $excludeId;
        }
        $stmt->execute($params);
        $result = $stmt->fetch();
        return $result['count'] > 0;
    }
    
    /**
     * Obtener usuario por username (incluso si está bloqueado/inactivo)
     */
    public function getByUsername($username) {
        $sql = "SELECT * FROM {$this->table} WHERE username = :username LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['username' => $username]);
        return $stmt->fetch();
    }
}
