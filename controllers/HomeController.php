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
        
        // Pagination parameters
        $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
        $articlesPerPage = 2; // Number of articles per page
        $offset = ($page - 1) * $articlesPerPage;
        
        // Get paginated articles
        $articles = $this->articleModel->getAllPaginated($articlesPerPage, $offset, $categoryId);
        
        // Get total count for pagination
        $totalArticles = $this->articleModel->getTotalCount($categoryId);
        $totalPages = ceil($totalArticles / $articlesPerPage);
        
        // Pagination info
        $pagination = [
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalArticles' => $totalArticles,
            'articlesPerPage' => $articlesPerPage,
            'hasNext' => $page < $totalPages,
            'hasPrevious' => $page > 1
        ];
        
        require_once "views/home/index.php";
    }
}
?>
