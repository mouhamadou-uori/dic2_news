<?php
class Category {
    private $id;
    private $libelle;
    private $slug;
    private $description;
    private $created_by;
    private $dateCreation;
    private $dateModification;
    private $actif;
    private $categorieDao;
    
    public function __construct($categorieDao = null) {
        $this->categorieDao = $categorieDao;
    }
    
    public function getId() {
        return $this->id;
    }
    
    public function getLibelle() {
        return $this->libelle;
    }
    
    public function getSlug() {
        return $this->slug;
    }
    
    public function getDescription() {
        return $this->description;
    }
    
    public function getCreatedBy() {
        return $this->created_by;
    }
    
    public function getDateCreation() {
        return $this->dateCreation;
    }
    
    public function getDateModification() {
        return $this->dateModification;
    }
    
    public function isActif() {
        return $this->actif;
    }
    
    public function setId($id) {
        $this->id = $id;
        return $this;
    }
    
    public function setLibelle($libelle) {
        $this->libelle = $libelle;
        return $this;
    }
    
    public function setSlug($slug) {
        $this->slug = $slug;
        return $this;
    }
    
    public function setDescription($description) {
        $this->description = $description;
        return $this;
    }
    
    public function setCreatedBy($created_by) {
        $this->created_by = $created_by;
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
    
    public static function fromArray($data) {
        $category = new self();
        $category->setId($data['id']);
        
        if (isset($data['libelle'])) {
            $category->setLibelle($data['libelle']);
        }
        
        if (isset($data['slug'])) {
            $category->setSlug($data['slug']);
        }
        
        if (isset($data['description'])) {
            $category->setDescription($data['description']);
        }
        
        if (isset($data['created_by'])) {
            $category->setCreatedBy($data['created_by']);
        }
        
        if (isset($data['dateCreation'])) {
            $category->setDateCreation($data['dateCreation']);
        }
        
        if (isset($data['dateModification'])) {
            $category->setDateModification($data['dateModification']);
        }
        
        if (isset($data['actif'])) {
            $category->setActif($data['actif']);
        }
        
        return $category;
    }
    
    public function getAll() {
        return $this->categorieDao->getAll();
    }
    
    public function getById($id) {
        return $this->categorieDao->getById($id);
    }
    
    public function create($libelle, $slug, $description, $created_by) {
        return $this->categorieDao->create($libelle, $slug, $description, $created_by);
    }
    
    public function update($id, $libelle, $slug, $description) {
        return $this->categorieDao->update($id, $libelle, $slug, $description);
    }
    
    public function delete($id) {
        return $this->categorieDao->delete($id);
    }
}
?>
