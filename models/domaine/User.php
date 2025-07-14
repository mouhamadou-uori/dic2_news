<?php
class User {
    private $userDao;
    private $id;
    private $username;
    private $email;
    private $password;
    private $nom;
    private $prenom;
    private $role;
    private $dateCreation;
    private $dateModification;
    private $actif;
    private $derniereConnexion;
    
    public function __construct($userDao = null) {
        $this->userDao = $userDao;
    }
    
    public function getAll() {
        $users = $this->userDao->getAll();
        return array_map(function($user) {
            return self::fromArray($user);
        }, $users);
    }
    
    public function getById($id) {
        $user = $this->userDao->getById($id);
        return $user ? self::fromArray($user) : null;
    }
    
    public function create($username, $email, $password, $nom, $prenom, $role) {
        if ($this->userDao->usernameExists($username)) {
            throw new Exception("Ce nom d'utilisateur existe déjà.");
        }
        if ($this->userDao->emailExists($email)) {
            throw new Exception("Cette adresse email existe déjà.");
        }
        return $this->userDao->create($username, $email, $password, $nom, $prenom, $role);
    }
    
    public function update($id, $username, $email, $nom, $prenom, $role, $password = null) {
        if ($this->userDao->usernameExists($username, $id)) {
            throw new Exception("Ce nom d'utilisateur existe déjà.");
        }
        if ($this->userDao->emailExists($email, $id)) {
            throw new Exception("Cette adresse email existe déjà.");
        }
        return $this->userDao->update($id, $username, $email, $nom, $prenom, $role, $password);
    }
    
    public function delete($id) {
        return $this->userDao->delete($id);
    }
    
    // Getters
    public function getId() { return $this->id; }
    public function getUsername() { return $this->username; }
    public function getEmail() { return $this->email; }
    public function getPassword() { return $this->password; }
    public function getNom() { return $this->nom; }
    public function getPrenom() { return $this->prenom; }
    public function getRole() { return $this->role; }
    public function getDateCreation() { return $this->dateCreation; }
    public function getDateModification() { return $this->dateModification; }
    public function isActif() { return $this->actif; }
    public function getDerniereConnexion() { return $this->derniereConnexion; }
    public function getNomComplet() { return $this->prenom . ' ' . $this->nom; }
    
    // Setters
    public function setId($id) {
        $this->id = $id;
        return $this;
    }
    
    public function setUsername($username) {
        $this->username = $username;
        return $this;
    }
    
    public function setEmail($email) {
        $this->email = $email;
        return $this;
    }
    
    public function setPassword($password) {
        $this->password = $password;
        return $this;
    }
    
    public function setNom($nom) {
        $this->nom = $nom;
        return $this;
    }
    
    public function setPrenom($prenom) {
        $this->prenom = $prenom;
        return $this;
    }
    
    public function setRole($role) {
        $this->role = $role;
        return $this;
    }
    
    public function setDateCreation($dateCreation) {
        $this->dateCreation = $dateCreation;
        return $this;
    }
    
    public function setDateModification($dateModification) {
        $this->dateModification = $dateModification;
        return $this;
    }
    
    public function setActif($actif) {
        $this->actif = $actif;
        return $this;
    }
    
    public function setDerniereConnexion($derniereConnexion) {
        $this->derniereConnexion = $derniereConnexion;
        return $this;
    }
    
    public static function fromArray($data) {
        $user = new self();
        $user->setId($data['id'])
             ->setUsername($data['username'])
             ->setEmail($data['email'])
             ->setPassword($data['password'])
             ->setNom($data['nom'])
             ->setPrenom($data['prenom'])
             ->setRole($data['role'])
             ->setActif($data['actif']);
        
        if (isset($data['dateCreation'])) {
            $user->setDateCreation($data['dateCreation']);
        }
        
        if (isset($data['dateModification'])) {
            $user->setDateModification($data['dateModification']);
        }
        
        if (isset($data['derniere_connexion'])) {
            $user->setDerniereConnexion($data['derniere_connexion']);
        }
        
        return $user;
    }
}