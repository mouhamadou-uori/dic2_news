<?php

class AuthToken {
    private $id;
    private $user_id;
    private $token;
    private $type;
    private $expires_at;
    private $created_at;
    private $last_used;
    private $is_active;
    private $user_agent;
    
    public function __construct() {
        $this->is_active = 1;
        $this->created_at = date('Y-m-d H:i:s');
    }
    
    // Getters
    public function getId() {
        return $this->id;
    }
    
    public function getUserId() {
        return $this->user_id;
    }
    
    public function getToken() {
        return $this->token;
    }
    
    public function getType() {
        return $this->type;
    }
    
    public function getExpiresAt() {
        return $this->expires_at;
    }
    
    public function getCreatedAt() {
        return $this->created_at;
    }
    
    public function getLastUsed() {
        return $this->last_used;
    }
    
    public function getIsActive() {
        return $this->is_active;
    }
    
    public function getUserAgent() {
        return $this->user_agent;
    }
    
    // Setters
    public function setId($id) {
        $this->id = $id;
        return $this;
    }
    
    public function setUserId($user_id) {
        $this->user_id = $user_id;
        return $this;
    }
    
    public function setToken($token) {
        $this->token = $token;
        return $this;
    }
    
    public function setType($type) {
        $this->type = $type;
        return $this;
    }
    
    public function setExpiresAt($expires_at) {
        $this->expires_at = $expires_at;
        return $this;
    }
    
    public function setCreatedAt($created_at) {
        $this->created_at = $created_at;
        return $this;
    }
    
    public function setLastUsed($last_used) {
        $this->last_used = $last_used;
        return $this;
    }
    
    public function setIsActive($is_active) {
        $this->is_active = $is_active;
        return $this;
    }
    
    public function setUserAgent($user_agent) {
        $this->user_agent = $user_agent;
        return $this;
    }
    
    /**
     * Génère un token sécurisé
     */
    public function generateToken($length = 64) {
        $this->token = bin2hex(random_bytes($length / 2));
        return $this->token;
    }
    
    /**
     * Vérifie si le token est expiré
     */
    public function isExpired() {
        if (!$this->expires_at || $this->expires_at === null) {
            return false; // Token permanent
        }
        return strtotime($this->expires_at) < time();
    }
    
    /**
     * Vérifie si le token est valide (actif et non expiré)
     */
    public function isValid() {
        return $this->is_active && !$this->isExpired();
    }
    
    /**
     * Crée une instance AuthToken à partir d'un tableau de données
     */
    public static function fromArray($data) {
        $token = new self();
        
        if (isset($data['id'])) $token->setId($data['id']);
        if (isset($data['user_id'])) $token->setUserId($data['user_id']);
        if (isset($data['token'])) $token->setToken($data['token']);
        if (isset($data['type'])) $token->setType($data['type']);
        if (isset($data['expires_at'])) $token->setExpiresAt($data['expires_at']);
        if (isset($data['created_at'])) $token->setCreatedAt($data['created_at']);
        if (isset($data['last_used'])) $token->setLastUsed($data['last_used']);
        if (isset($data['is_active'])) $token->setIsActive($data['is_active']);
        if (isset($data['user_agent'])) $token->setUserAgent($data['user_agent']);
        
        return $token;
    }
    
    /**
     * Convertit l'objet en tableau
     */
    public function toArray() {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'token' => $this->token,
            'type' => $this->type,
            'expires_at' => $this->expires_at,
            'created_at' => $this->created_at,
            'last_used' => $this->last_used,
            'is_active' => $this->is_active,
            'user_agent' => $this->user_agent
        ];
    }
    
    /**
     * Retourne une représentation JSON de l'objet
     */
    public function toJson() {
        return json_encode($this->toArray());
    }
    
    /**
     * Retourne le token masqué pour l'affichage (sécurité)
     */
    public function getMaskedToken() {
        if (strlen($this->token) <= 8) {
            return str_repeat('*', strlen($this->token));
        }
        return substr($this->token, 0, 8) . str_repeat('*', strlen($this->token) - 16) . substr($this->token, -8);
    }
    
    /**
     * Retourne le statut du token sous forme de texte
     */
    public function getStatusText() {
        if (!$this->is_active) {
            return 'Révoké';
        }
        if ($this->isExpired()) {
            return 'Expiré';
        }
        return 'Actif';
    }
    
    /**
     * Retourne la classe CSS pour le statut
     */
    public function getStatusClass() {
        if (!$this->is_active) {
            return 'text-danger';
        }
        if ($this->isExpired()) {
            return 'text-warning';
        }
        return 'text-success';
    }
}