<?php
class HomeController {
    private $articleModel;
    private $categoryModel;
    
    public function __construct($db) {
        $articleDao = new ArticleDao($db);
        $categorieDao = new CategorieDao($db);
        
        $this->articleModel = new Article($articleDao);
        $this->categoryModel = new Category($categorieDao);
    }
    
    public function index() {
        $categoryId = isset($_GET["categorie"]) ? $_GET["categorie"] : null;
        $categories = $this->categoryModel->getAll();
        
        if ($categoryId) {
            $articles = $this->articleModel->getByCategory($categoryId);
        } else {
            $articles = $this->articleModel->getAll();
        }
        
        require_once "views/home/index.php";
    }
}
?>
