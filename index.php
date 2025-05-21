<?php
require_once 'config/database.php';
require_once 'models/dao/ArticleDao.php';
require_once 'models/dao/CategorieDao.php';
require_once 'models/domaine/Article.php';
require_once 'models/domaine/Category.php';
require_once 'controllers/HomeController.php';
require_once 'controllers/ArticleController.php';

$db = getDbConnection();

$controller = isset($_GET['controller']) ? $_GET['controller'] : 'home';
$action = isset($_GET['action']) ? $_GET['action'] : 'index';

switch ($controller) {
    case 'article':
        $articleController = new ArticleController($db);
        
        if ($action === 'view' && isset($_GET['id'])) {
            $articleController->view($_GET['id']);
        } else {
            header('Location: index.php');
        }
        break;
        
    case 'home':
    default:
        $homeController = new HomeController($db);
        $homeController->index();
        break;
}
?>
