<?php
// Démarrer la session
session_start();

require_once 'config/database.php';
require_once 'models/dao/ArticleDao.php';
require_once 'models/dao/CategorieDao.php';
require_once 'models/dao/UserDao.php';
require_once 'models/dao/AuthTokenDao.php';
require_once 'models/domaine/Article.php';
require_once 'models/domaine/Category.php';
require_once 'models/domaine/User.php';
require_once 'models/domaine/AuthToken.php';
require_once 'controllers/HomeController.php';
require_once 'controllers/ArticleController.php';
require_once 'controllers/AuthController.php';
require_once 'controllers/AdminController.php';

$db = getDbConnection();

$page = isset($_GET['page']) ? $_GET['page'] : 'home';
$action = isset($_GET['action']) ? $_GET['action'] : 'index';

switch ($page) {
    case 'article':
        $articleController = new ArticleController($db);
        
        if ($action === 'view' && isset($_GET['id'])) {
            $articleController->view($_GET['id']);
        } else {
            header('Location: index.php');
        }
        break;
        
    case 'login':
        $authController = new AuthController($db);
        
        if ($action === 'submit') {
            $authController->login();
        } else {
            $authController->showLoginForm();
        }
        break;
        
    case 'register':
        $authController = new AuthController($db);
        
        if ($action === 'submit') {
            $authController->register();
        } else {
            $authController->showRegisterForm();
        }
        break;
        
    case 'logout':
        $authController = new AuthController($db);
        $authController->logout();
        break;
        
    case 'admin':
        $adminController = new AdminController($db);
        
        switch ($action) {
            case 'dashboard':
                $adminController->dashboard();
                break;
                
            // Gestion des articles
            case 'listArticles':
                $adminController->listArticles();
                break;
                
            case 'createArticleForm':
                $adminController->createArticleForm();
                break;
                
            case 'createArticle':
                $adminController->createArticle();
                break;
                
            case 'editArticleForm':
                if (isset($_GET['id'])) {
                    $adminController->editArticleForm($_GET['id']);
                } else {
                    header('Location: index.php?page=admin&action=listArticles');
                }
                break;
                
            case 'updateArticle':
                if (isset($_GET['id'])) {
                    $adminController->updateArticle($_GET['id']);
                } else {
                    header('Location: index.php?page=admin&action=listArticles');
                }
                break;
                
            case 'deleteArticle':
                if (isset($_GET['id'])) {
                    $adminController->deleteArticle($_GET['id']);
                } else {
                    header('Location: index.php?page=admin&action=listArticles');
                }
                break;
                
            // Gestion des catégories
            case 'listCategories':
                $adminController->listCategories();
                break;
                
            case 'createCategoryForm':
                $adminController->createCategoryForm();
                break;
                
            case 'createCategory':
                $adminController->createCategory();
                break;
                
            case 'editCategoryForm':
                if (isset($_GET['id'])) {
                    $adminController->editCategoryForm($_GET['id']);
                } else {
                    header('Location: index.php?page=admin&action=listCategories');
                }
                break;
                
            case 'updateCategory':
                if (isset($_GET['id'])) {
                    $adminController->updateCategory($_GET['id']);
                } else {
                    header('Location: index.php?page=admin&action=listCategories');
                }
                break;
                
            case 'deleteCategory':
                if (isset($_GET['id'])) {
                    $adminController->deleteCategory($_GET['id']);
                } else {
                    header('Location: index.php?page=admin&action=listCategories');
                }
                break;
                
            // Routes pour la gestion des utilisateurs
            case 'listUsers':
                $adminController->listUsers();
                break;
                
            case 'createUserForm':
                $adminController->createUserForm();
                break;
                
            case 'createUser':
                $adminController->createUser();
                break;
                
            case 'editUserForm':
                if (isset($_GET['id'])) {
                    $adminController->editUserForm($_GET['id']);
                } else {
                    header('Location: index.php?page=admin&action=listUsers');
                }
                break;
                
            case 'updateUser':
                if (isset($_GET['id'])) {
                    $adminController->updateUser($_GET['id']);
                } else {
                    header('Location: index.php?page=admin&action=listUsers');
                }
                break;
                
            case 'deleteUser':
                if (isset($_GET['id'])) {
                    $adminController->deleteUser($_GET['id']);
                } else {
                    header('Location: index.php?page=admin&action=listUsers');
                }
                break;
                
            // Routes pour la gestion des jetons d'authentification
            case 'listTokens':
                $adminController->listTokens();
                break;
                
            case 'createTokenForm':
                $adminController->createTokenForm();
                break;
                
            case 'createToken':
                $adminController->createToken();
                break;
                
            case 'viewToken':
                if (isset($_GET['id'])) {
                    $adminController->viewToken($_GET['id']);
                } else {
                    header('Location: index.php?page=admin&action=listTokens');
                }
                break;
                
            case 'revokeToken':
                if (isset($_GET['id'])) {
                    $adminController->revokeToken($_GET['id']);
                } else {
                    header('Location: index.php?page=admin&action=listTokens');
                }
                break;
                
            case 'deleteToken':
                if (isset($_GET['id'])) {
                    $adminController->deleteToken($_GET['id']);
                } else {
                    header('Location: index.php?page=admin&action=listTokens');
                }
                break;
                
            default:
                $adminController->dashboard();
                break;
        }
        break;
        
    case 'home':
    default:
        $homeController = new HomeController($db);
        $homeController->index();
        break;
}
?>
