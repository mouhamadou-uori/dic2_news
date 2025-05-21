<?php
class Category {
    private $id;
    private $libelle;
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
    
    public function setId($id) {
        $this->id = $id;
        return $this;
    }
    
    public function setLibelle($libelle) {
        $this->libelle = $libelle;
        return $this;
    }
    
    public static function fromArray($data) {
        $category = new self();
        $category->setId($data['id'])
                 ->setLibelle($data['libelle']);
        return $category;
    }
    
    public function getAll() {
        $categoriesData = $this->categorieDao->getAll();
        $categories = [];
        
        foreach ($categoriesData as $categoryData) {
            $categories[] = self::fromArray($categoryData);
        }
        
        return $categories;
    }
    
    public function getById($id) {
        $categoryData = $this->categorieDao->getById($id);
        
        if (!$categoryData) {
            return null;
        }
        
        return self::fromArray($categoryData);
    }
}
?>
