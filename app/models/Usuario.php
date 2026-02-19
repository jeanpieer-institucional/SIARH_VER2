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
}
