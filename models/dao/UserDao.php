<?php
require_once 'models/domaine/User.php';

class UserDao {
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    public function getAll() {
        $query = "SELECT * FROM User ORDER BY dateCreation DESC";
        $stmt = $this->db->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getById($id) {
        $query = "SELECT * FROM User WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $userData = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$userData) {
            return null;
        }
        return User::fromArray($userData);
    }
    
    public function create($username, $email, $password, $nom, $prenom, $role = 'visiteur') {
        $query = "INSERT INTO User (username, email, password, nom, prenom, role) 
                  VALUES (:username, :email, :password, :nom, :prenom, :role)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':password', password_hash($password, PASSWORD_DEFAULT), PDO::PARAM_STR);
        $stmt->bindParam(':nom', $nom, PDO::PARAM_STR);
        $stmt->bindParam(':prenom', $prenom, PDO::PARAM_STR);
        $stmt->bindParam(':role', $role, PDO::PARAM_STR);
        return $stmt->execute();
    }
    
    public function update($id, $username, $email, $nom, $prenom, $role, $password = null) {
        if ($password) {
            $query = "UPDATE User 
                      SET username = :username, 
                          email = :email, 
                          password = :password,
                          nom = :nom,
                          prenom = :prenom,
                          role = :role,
                          dateModification = CURRENT_TIMESTAMP
                      WHERE id = :id";
        } else {
            $query = "UPDATE User 
                      SET username = :username, 
                          email = :email,
                          nom = :nom,
                          prenom = :prenom,
                          role = :role,
                          dateModification = CURRENT_TIMESTAMP
                      WHERE id = :id";
        }
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':nom', $nom, PDO::PARAM_STR);
        $stmt->bindParam(':prenom', $prenom, PDO::PARAM_STR);
        $stmt->bindParam(':role', $role, PDO::PARAM_STR);
        
        if ($password) {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt->bindParam(':password', $hashedPassword, PDO::PARAM_STR);
        }
        
        return $stmt->execute();
    }
    
    public function delete($id) {
        $query = "DELETE FROM User WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
    
    public function emailExists($email, $excludeId = null) {
        $query = "SELECT COUNT(*) FROM User WHERE email = :email";
        if ($excludeId) {
            $query .= " AND id != :id";
        }
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        if ($excludeId) {
            $stmt->bindParam(':id', $excludeId, PDO::PARAM_INT);
        }
        $stmt->execute();
        
        return (int)$stmt->fetchColumn() > 0;
    }
    
    public function usernameExists($username, $excludeId = null) {
        $query = "SELECT COUNT(*) FROM User WHERE username = :username";
        if ($excludeId) {
            $query .= " AND id != :id";
        }
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        if ($excludeId) {
            $stmt->bindParam(':id', $excludeId, PDO::PARAM_INT);
        }
        $stmt->execute();
        
        return (int)$stmt->fetchColumn() > 0;
    }

    public function getByUsername($username) {
        $query = "SELECT * FROM User WHERE username = :username";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->execute();
        $userData = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$userData) {
            return null;
        }
        
        return User::fromArray($userData);
    }
    
    public function getByEmail($email) {
        $query = "SELECT * FROM User WHERE email = :email";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        $userData = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$userData) {
            return null;
        }
        
        return User::fromArray($userData);
    }
    
    public function updateLastLogin($id) {
        $query = "UPDATE User SET derniere_connexion = CURRENT_TIMESTAMP WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
?> 