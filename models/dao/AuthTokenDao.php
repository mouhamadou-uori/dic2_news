<?php
require_once __DIR__ . '/../../models/domaine/AuthToken.php';

class AuthTokenDao {
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    public function getAll() {
        $query = "SELECT * FROM AuthToken ORDER BY created_at DESC";
        $stmt = $this->db->query($query);
        $tokensData = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $tokens = [];
        
        foreach ($tokensData as $tokenData) {
            $tokens[] = AuthToken::fromArray($tokenData);
        }
        
        return $tokens;
    }
    
    public function getById($id) {
        $query = "SELECT * FROM AuthToken WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $tokenData = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$tokenData) {
            return null;
        }
        
        return AuthToken::fromArray($tokenData);
    }
    
    public function getByToken($token) {
        $query = "SELECT * FROM AuthToken WHERE token = :token";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':token', $token, PDO::PARAM_STR);
        $stmt->execute();
        $tokenData = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$tokenData) {
            return null;
        }
        
        return AuthToken::fromArray($tokenData);
    }
    
    public function create($user_id, $type = 'api', $expires_in = '+30 days') {
        $token = new AuthToken();
        $token->generateToken();
        
        // Gérer le cas où le token n'expire jamais
        if ($expires_in === 'never') {
            $expires_at = null;
        } else {
            $expires_at = date('Y-m-d H:i:s', strtotime($expires_in));
        }
        
        $query = "INSERT INTO AuthToken (user_id, token, type, expires_at) 
                  VALUES (:user_id, :token, :type, :expires_at)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':token', $token->getToken(), PDO::PARAM_STR);
        $stmt->bindParam(':type', $type, PDO::PARAM_STR);
        $stmt->bindParam(':expires_at', $expires_at, PDO::PARAM_STR);
        
        if ($stmt->execute()) {
            return $this->getById($this->db->lastInsertId());
        }
        
        return false;
    }
    
    public function revoke($id) {
        $query = "UPDATE AuthToken SET is_active = 0 WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
    
    public function delete($id) {
        $query = "DELETE FROM AuthToken WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
    
    public function updateLastUsed($id) {
        $query = "UPDATE AuthToken SET last_used = CURRENT_TIMESTAMP WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
    
    public function isValid($token) {
        $query = "SELECT COUNT(*) FROM AuthToken 
                  WHERE token = :token 
                  AND is_active = 1 
                  AND expires_at > NOW()";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':token', $token, PDO::PARAM_STR);
        $stmt->execute();
        return (int)$stmt->fetchColumn() > 0;
    }
}
?>