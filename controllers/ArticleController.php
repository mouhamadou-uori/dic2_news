<?php
class ArticleController {
    private $articleModel;
    private $categoryModel;
    
    public function __construct($db) {
        $articleDao = new ArticleDao($db);
        $categorieDao = new CategorieDao($db);
        
        $this->articleModel = new Article($articleDao);
        $this->categoryModel = new Category($categorieDao);
    }
    
    public function view($id) {
        if (!$id || !is_numeric($id)) {
            header("Location: index.php");
            exit;
        }
        
        $article = $this->articleModel->getById($id);
        
        if (!$article) {
            header("Location: index.php");
            exit;
        }
        
        $categories = $this->categoryModel->getAll();
        
        require_once "views/articles/view.php";
    }
}
?>
