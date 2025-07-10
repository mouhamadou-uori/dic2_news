<?php
class Article {
    private $id;
    private $titre;
    private $contenu;
    private $dateCreation;
    private $categorie;
    private $categorie_nom;
    private $articleDao;
    
    public function __construct($articleDao = null) {
        $this->articleDao = $articleDao;
    }
    
    public function getId() {  
        return $this->id;
    }
    
    public function getTitre() {
        return $this->titre;
    }
    
    public function getContenu() {
        return $this->contenu;  
    }
    
    public function getDateCreation() {
        return $this->dateCreation;
    }
    
    public function getCategorie() {
        return $this->categorie;
    }
    
    public function getCategorieName() {
        return $this->categorie_nom;
    }
    
    public function getSummary($maxLength = 200) {
        if (strlen($this->contenu) <= $maxLength) {
            return $this->contenu;
        }
        
        $summary = substr($this->contenu, 0, $maxLength);
        $lastSpace = strrpos($summary, ' ');
        
        if ($lastSpace !== false) {
            $summary = substr($summary, 0, $lastSpace);
        }
        
        return $summary . '...';
    }
    
    public function setId($id) {
        $this->id = $id;
        return $this;
    }
    
    public function setTitre($titre) {
        $this->titre = $titre;
        return $this;
    }
    
    public function setContenu($contenu) {
        $this->contenu = $contenu;
        return $this;
    }
    
    public function setDateCreation($dateCreation) {
        $this->dateCreation = $dateCreation;
        return $this;  
    }
    
    public function setCategorie($categorie) {
        $this->categorie = $categorie;
        return $this;
    }
    
    public function setCategorieName($categorie_nom) {
        $this->categorie_nom = $categorie_nom;
        return $this;
    }
    
    public static function fromArray($data) {
        $article = new self();
        $article->setId($data['id'])
                ->setTitre($data['titre'])
                ->setContenu($data['contenu'])  
                ->setDateCreation($data['dateCreation'])   
                ->setCategorie($data['categorie'])
                ->setCategorieName($data['categorie_nom']);
        return $article;
    }
    
    public function getAll() {
        $articlesData = $this->articleDao->getAll();
        $articles = [];
        
        foreach ($articlesData as $articleData) {
            $articles[] = self::fromArray($articleData);
        }
        
        return $articles;
    }
    
    public function getById($id) {
        $articleData = $this->articleDao->getById($id);
        
        if (!$articleData) {
            return null;
        }
        
        return self::fromArray($articleData);
    }
    
    public function getByCategory($categoryId) {
        $articlesData = $this->articleDao->getByCategory($categoryId);
        $articles = [];
        
        foreach ($articlesData as $articleData) {
            $articles[] = self::fromArray($articleData);
        }
        
        return $articles;
    }
    
    public function getAllPaginated($limit, $offset, $categoryId = null) {
        $articlesData = $this->articleDao->getAllPaginated($limit, $offset, $categoryId);
        $articles = [];
        
        foreach ($articlesData as $articleData) {
            $articles[] = self::fromArray($articleData);
        }
        
        return $articles;
    }
    
    public function getTotalCount($categoryId = null) {
        return $this->articleDao->getTotalCount($categoryId);
    }
}
?>
