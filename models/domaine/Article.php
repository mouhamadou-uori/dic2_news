<?php
class Article {
    private $id;
    private $titre;
    private $contenu;
    private $dateCreation;
    private $dateModification;
    private $categorie;
    private $categorie_nom;
    private $auteur_id;
    private $auteur_nom;
    private $statut;
    private $slug;
    private $meta_description;
    private $featured_image;
    private $views_count;
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
    
    public function getDateModification() {
        return $this->dateModification;
    }
    
    public function getCategorie() {
        return $this->categorie;
    }
    
    public function getCategorieName() {
        return $this->categorie_nom;
    }
    
    public function getAuteurId() {
        return $this->auteur_id;
    }
    
    public function getAuteurNom() {
        return $this->auteur_nom;
    }
    
    public function getStatut() {
        return $this->statut;
    }
    
    public function getSlug() {
        return $this->slug;
    }
    
    public function getMetaDescription() {
        return $this->meta_description;
    }
    
    public function getFeaturedImage() {
        return $this->featured_image;
    }
    
    public function getViewsCount() {
        return $this->views_count;
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
    
    public function setDateModification($dateModification) {
        $this->dateModification = $dateModification;
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
    
    public function setAuteurId($auteur_id) {
        $this->auteur_id = $auteur_id;
        return $this;
    }
    
    public function setAuteurNom($auteur_nom) {
        $this->auteur_nom = $auteur_nom;
        return $this;
    }
    
    public function setStatut($statut) {
        $this->statut = $statut;
        return $this;
    }
    
    public function setSlug($slug) {
        $this->slug = $slug;
        return $this;
    }
    
    public function setMetaDescription($meta_description) {
        $this->meta_description = $meta_description;
        return $this;
    }
    
    public function setFeaturedImage($featured_image) {
        $this->featured_image = $featured_image;
        return $this;
    }
    
    public function setViewsCount($views_count) {
        $this->views_count = $views_count;
        return $this;
    }
    
    public static function fromArray($data) {
        $article = new self();
        $article->setId($data['id'])
                ->setTitre($data['titre'])
                ->setContenu($data['contenu'])  
                ->setDateCreation($data['dateCreation'])
                ->setCategorie($data['categorie']);
        
        if (isset($data['categorie_nom'])) {
            $article->setCategorieName($data['categorie_nom']);
        }
        
        if (isset($data['dateModification'])) {
            $article->setDateModification($data['dateModification']);
        }
        
        if (isset($data['auteur_id'])) {
            $article->setAuteurId($data['auteur_id']);
        }
        
        if (isset($data['auteur_nom'])) {
            $article->setAuteurNom($data['auteur_nom']);
        }
        
        if (isset($data['statut'])) {
            $article->setStatut($data['statut']);
        }
        
        if (isset($data['slug'])) {
            $article->setSlug($data['slug']);
        }
        
        if (isset($data['meta_description'])) {
            $article->setMetaDescription($data['meta_description']);
        }
        
        if (isset($data['featured_image'])) {
            $article->setFeaturedImage($data['featured_image']);
        }
        
        if (isset($data['views_count'])) {
            $article->setViewsCount($data['views_count']);
        }
        
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
    
    public function getAllForAdmin($limit = 10, $offset = 0) {
        $articlesData = $this->articleDao->getAllForAdmin($limit, $offset);
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
    
    public function create($titre, $slug, $contenu, $categorie, $auteur_id, $statut = 'brouillon') {
        return $this->articleDao->create($titre, $slug, $contenu, $categorie, $auteur_id, $statut);
    }
    
    public function update($id, $titre, $slug, $contenu, $categorie, $statut) {
        return $this->articleDao->update($id, $titre, $slug, $contenu, $categorie, $statut);
    }
    
    public function delete($id) {
        return $this->articleDao->delete($id);
    }
}
?>
