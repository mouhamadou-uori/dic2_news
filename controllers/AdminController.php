<?php
require_once 'models/dao/ArticleDao.php';
require_once 'models/dao/CategorieDao.php';
require_once 'models/dao/AuthTokenDao.php';
require_once 'models/domaine/Article.php';
require_once 'models/domaine/Category.php';
require_once 'models/domaine/AuthToken.php';
require_once 'controllers/AuthController.php';

class AdminController {
    private $db;
    private $articleModel;
    private $categoryModel;
    private $userModel;
    private $tokenModel;
    
    public function __construct($db) {
        $this->db = $db;
        $articleDao = new ArticleDao($db);
        $categorieDao = new CategorieDao($db);
        $userDao = new UserDao($db);
        $tokenDao = new AuthTokenDao($db);
        
        $this->articleModel = new Article($articleDao);
        $this->categoryModel = new Category($categorieDao);
        $this->userModel = new User($userDao);
        $this->tokenModel = $tokenDao;
        
        // Vérifier que l'utilisateur est un éditeur ou un administrateur
        AuthController::requireEditor();
    }
    
    /**
     * Affiche le tableau de bord d'administration
     */
    public function dashboard() {
        $pageTitle = "Tableau de bord d'administration";
        $categories = $this->categoryModel->getAll();
        
        ob_start();
        include 'views/admin/dashboard.php';
        $content = ob_get_clean();
        
        include 'views/layouts/admin.php';
    }
    
    /**
     * Affiche la liste des articles pour administration
     */
    public function listArticles() {
        $pageTitle = "Gestion des articles";
        $categories = $this->categoryModel->getAll();
        
        // Pagination parameters
        $page = isset($_GET['page_num']) ? max(1, intval($_GET['page_num'])) : 1;
        $articlesPerPage = 10; // Plus d'articles par page dans l'admin
        $offset = ($page - 1) * $articlesPerPage;
        
        // Get all articles for admin
        $articles = $this->articleModel->getAllForAdmin($articlesPerPage, $offset);
        
        // Get total count for pagination
        $totalArticles = $this->articleModel->getTotalCount();
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
        
        ob_start();
        include 'views/admin/articles/index.php';
        $content = ob_get_clean();
        
        include 'views/layouts/admin.php';
    }
    
    /**
     * Affiche le formulaire de création d'article
     */
    public function createArticleForm() {
        $pageTitle = "Créer un article";
        $categories = $this->categoryModel->getAll();
        $error = isset($_SESSION['article_error']) ? $_SESSION['article_error'] : null;
        $old = isset($_SESSION['article_old']) ? $_SESSION['article_old'] : [];
        
        unset($_SESSION['article_error']);
        unset($_SESSION['article_old']);
        
        ob_start();
        include 'views/admin/articles/create.php';
        $content = ob_get_clean();
        
        include 'views/layouts/admin.php';
    }
    
    /**
     * Traite la soumission du formulaire de création d'article
     */
    public function createArticle() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?page=admin&action=createArticleForm');
            exit;
        }
        
        // Récupérer les données du formulaire
        $titre = isset($_POST['titre']) ? trim($_POST['titre']) : '';
        $contenu = isset($_POST['contenu']) ? trim($_POST['contenu']) : '';
        $categorie = isset($_POST['categorie']) ? intval($_POST['categorie']) : 0;
        $statut = isset($_POST['statut']) ? $_POST['statut'] : 'brouillon';
        
        // Sauvegarder les données pour les réafficher en cas d'erreur
        $_SESSION['article_old'] = [
            'titre' => $titre,
            'contenu' => $contenu,
            'categorie' => $categorie,
            'statut' => $statut
        ];
        
        // Validation
        if (empty($titre) || empty($contenu) || $categorie <= 0) {
            $_SESSION['article_error'] = "Tous les champs sont obligatoires.";
            header('Location: index.php?page=admin&action=createArticleForm');
            exit;
        }
        
        // Créer l'article
        $slug = $this->createSlug($titre);
        $auteur_id = $_SESSION['user_id'];
        
        $result = $this->articleModel->create($titre, $slug, $contenu, $categorie, $auteur_id, $statut);
        
        if ($result) {
            // Création réussie
            $_SESSION['success_message'] = "L'article a été créé avec succès.";
            header('Location: index.php?page=admin&action=listArticles');
            exit;
        } else {
            $_SESSION['article_error'] = "Une erreur est survenue lors de la création de l'article.";
            header('Location: index.php?page=admin&action=createArticleForm');
            exit;
        }
    }
    
    /**
     * Affiche le formulaire de modification d'article
     */
    public function editArticleForm($id) {
        $article = $this->articleModel->getById($id);
        
        if (!$article) {
            header('Location: index.php?page=admin&action=listArticles');
            exit;
        }
        
        $pageTitle = "Modifier l'article";
        $categories = $this->categoryModel->getAll();
        $error = isset($_SESSION['article_error']) ? $_SESSION['article_error'] : null;
        
        unset($_SESSION['article_error']);
        
        ob_start();
        include 'views/admin/articles/edit.php';
        $content = ob_get_clean();
        
        include 'views/layouts/admin.php';
    }
    
    /**
     * Traite la soumission du formulaire de modification d'article
     */
    public function updateArticle($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?page=admin&action=editArticleForm&id=' . $id);
            exit;
        }
        
        $article = $this->articleModel->getById($id);
        
        if (!$article) {
            header('Location: index.php?page=admin&action=listArticles');
            exit;
        }
        
        // Récupérer les données du formulaire
        $titre = isset($_POST['titre']) ? trim($_POST['titre']) : '';
        $contenu = isset($_POST['contenu']) ? trim($_POST['contenu']) : '';
        $categorie = isset($_POST['categorie']) ? intval($_POST['categorie']) : 0;
        $statut = isset($_POST['statut']) ? $_POST['statut'] : 'brouillon';
        
        // Validation
        if (empty($titre) || empty($contenu) || $categorie <= 0) {
            $_SESSION['article_error'] = "Tous les champs sont obligatoires.";
            header('Location: index.php?page=admin&action=editArticleForm&id=' . $id);
            exit;
        }
        
        // Mettre à jour l'article
        $slug = $this->createSlug($titre);
        
        $result = $this->articleModel->update($id, $titre, $slug, $contenu, $categorie, $statut);
        
        if ($result) {
            // Mise à jour réussie
            $_SESSION['success_message'] = "L'article a été mis à jour avec succès.";
            header('Location: index.php?page=admin&action=listArticles');
            exit;
        } else {
            $_SESSION['article_error'] = "Une erreur est survenue lors de la mise à jour de l'article.";
            header('Location: index.php?page=admin&action=editArticleForm&id=' . $id);
            exit;
        }
    }
    
    /**
     * Supprime un article
     */
    public function deleteArticle($id) {
        $article = $this->articleModel->getById($id);
        
        if (!$article) {
            $_SESSION['error_message'] = "L'article n'existe pas.";
            header('Location: index.php?page=admin&action=listArticles');
            exit;
        }
        
        try {
            $result = $this->articleModel->delete($id);
            
            if ($result) {
                $_SESSION['success_message'] = "L'article a été supprimé avec succès.";
            } else {
                $_SESSION['error_message'] = "Une erreur est survenue lors de la suppression de l'article.";
            }
        } catch (PDOException $e) {
            // Si l'erreur est due à une contrainte de clé étrangère
            if ($e->getCode() == '23000') {
                $_SESSION['error_message'] = "Impossible de supprimer cet article car il est lié à une catégorie. Veuillez d'abord modifier sa catégorie.";
            } else {
                $_SESSION['error_message'] = "Une erreur est survenue lors de la suppression de l'article.";
            }
        }
        
        header('Location: index.php?page=admin&action=listArticles');
        exit;
    }
    
    /**
     * Affiche la liste des catégories pour administration
     */
    public function listCategories() {
        $pageTitle = "Gestion des catégories";
        $categories = $this->categoryModel->getAll();
        
        ob_start();
        include 'views/admin/categories/index.php';
        $content = ob_get_clean();
        
        include 'views/layouts/admin.php';
    }
    
    /**
     * Affiche le formulaire de création de catégorie
     */
    public function createCategoryForm() {
        $pageTitle = "Créer une catégorie";
        $categories = $this->categoryModel->getAll();
        $error = isset($_SESSION['category_error']) ? $_SESSION['category_error'] : null;
        $old = isset($_SESSION['category_old']) ? $_SESSION['category_old'] : [];
        
        unset($_SESSION['category_error']);
        unset($_SESSION['category_old']);
        
        ob_start();
        include 'views/admin/categories/create.php';
        $content = ob_get_clean();
        
        include 'views/layouts/admin.php';
    }
    
    /**
     * Traite la soumission du formulaire de création de catégorie
     */
    public function createCategory() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?page=admin&action=createCategoryForm');
            exit;
        }
        
        // Récupérer les données du formulaire
        $libelle = isset($_POST['libelle']) ? trim($_POST['libelle']) : '';
        $description = isset($_POST['description']) ? trim($_POST['description']) : '';
        
        // Sauvegarder les données pour les réafficher en cas d'erreur
        $_SESSION['category_old'] = [
            'libelle' => $libelle,
            'description' => $description
        ];
        
        // Validation
        if (empty($libelle)) {
            $_SESSION['category_error'] = "Le libellé est obligatoire.";
            header('Location: index.php?page=admin&action=createCategoryForm');
            exit;
        }
        
        // Créer la catégorie
        $slug = $this->createSlug($libelle);
        $created_by = $_SESSION['user_id'];
        
        $result = $this->categoryModel->create($libelle, $slug, $description, $created_by);
        
        if ($result) {
            // Création réussie
            $_SESSION['success_message'] = "La catégorie a été créée avec succès.";
            header('Location: index.php?page=admin&action=listCategories');
            exit;
        } else {
            $_SESSION['category_error'] = "Une erreur est survenue lors de la création de la catégorie.";
            header('Location: index.php?page=admin&action=createCategoryForm');
            exit;
        }
    }
    
    /**
     * Affiche le formulaire de modification de catégorie
     */
    public function editCategoryForm($id) {
        $category = $this->categoryModel->getById($id);
        
        if (!$category) {
            header('Location: index.php?page=admin&action=listCategories');
            exit;
        }
        
        $pageTitle = "Modifier la catégorie";
        $categories = $this->categoryModel->getAll();
        $error = isset($_SESSION['category_error']) ? $_SESSION['category_error'] : null;
        
        unset($_SESSION['category_error']);
        
        ob_start();
        include 'views/admin/categories/edit.php';
        $content = ob_get_clean();
        
        include 'views/layouts/admin.php';
    }
    
    /**
     * Traite la soumission du formulaire de modification de catégorie
     */
    public function updateCategory($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?page=admin&action=editCategoryForm&id=' . $id);
            exit;
        }
        
        $category = $this->categoryModel->getById($id);
        
        if (!$category) {
            header('Location: index.php?page=admin&action=listCategories');
            exit;
        }
        
        // Récupérer les données du formulaire
        $libelle = isset($_POST['libelle']) ? trim($_POST['libelle']) : '';
        $description = isset($_POST['description']) ? trim($_POST['description']) : '';
        
        // Validation
        if (empty($libelle)) {
            $_SESSION['category_error'] = "Le libellé est obligatoire.";
            header('Location: index.php?page=admin&action=editCategoryForm&id=' . $id);
            exit;
        }
        
        // Mettre à jour la catégorie
        $slug = $this->createSlug($libelle);
        
        $result = $this->categoryModel->update($id, $libelle, $slug, $description);
        
        if ($result) {
            // Mise à jour réussie
            $_SESSION['success_message'] = "La catégorie a été mise à jour avec succès.";
            header('Location: index.php?page=admin&action=listCategories');
            exit;
        } else {
            $_SESSION['category_error'] = "Une erreur est survenue lors de la mise à jour de la catégorie.";
            header('Location: index.php?page=admin&action=editCategoryForm&id=' . $id);
            exit;
        }
    }
    
    /**
     * Supprime une catégorie
     */
    public function deleteCategory($id) {
        $category = $this->categoryModel->getById($id);
        
        if (!$category) {
            header('Location: index.php?page=admin&action=listCategories');
            exit;
        }
        
        $result = $this->categoryModel->delete($id);
        
        if ($result) {
            $_SESSION['success_message'] = "La catégorie a été supprimée avec succès.";
        } else {
            $_SESSION['error_message'] = "Une erreur est survenue lors de la suppression de la catégorie. Vérifiez qu'aucun article n'est associé à cette catégorie.";
        }
        
        header('Location: index.php?page=admin&action=listCategories');
        exit;
    }
    
    /**
     * Affiche la liste des utilisateurs
     */
    public function listUsers() {
        // Vérifier que l'utilisateur est un administrateur
        if ($_SESSION['role'] !== 'administrateur') {
            $_SESSION['error_message'] = "Accès refusé. Vous devez être administrateur pour gérer les utilisateurs.";
            header('Location: index.php?page=admin');
            exit;
        }
        
        $pageTitle = "Gestion des utilisateurs";
        $users = $this->userModel->getAll();
        
        ob_start();
        include 'views/admin/users/index.php';
        $content = ob_get_clean();
        
        include 'views/layouts/admin.php';
    }
    
    /**
     * Affiche le formulaire de création d'utilisateur
     */
    public function createUserForm() {
        // Vérifier que l'utilisateur est un administrateur
        if ($_SESSION['role'] !== 'administrateur') {
            $_SESSION['error_message'] = "Accès refusé. Vous devez être administrateur pour gérer les utilisateurs.";
            header('Location: index.php?page=admin');
            exit;
        }
        
        $pageTitle = "Créer un utilisateur";
        $error = isset($_SESSION['user_error']) ? $_SESSION['user_error'] : null;
        $old = isset($_SESSION['user_old']) ? $_SESSION['user_old'] : [];
        
        unset($_SESSION['user_error']);
        unset($_SESSION['user_old']);
        
        ob_start();
        include 'views/admin/users/create.php';
        $content = ob_get_clean();
        
        include 'views/layouts/admin.php';
    }
    
    /**
     * Traite la soumission du formulaire de création d'utilisateur
     */
    public function createUser() {
        // Vérifier que l'utilisateur est un administrateur
        if ($_SESSION['role'] !== 'administrateur') {
            $_SESSION['error_message'] = "Accès refusé. Vous devez être administrateur pour gérer les utilisateurs.";
            header('Location: index.php?page=admin');
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?page=admin&action=createUserForm');
            exit;
        }
        
        // Récupérer les données du formulaire
        $username = isset($_POST['username']) ? trim($_POST['username']) : '';
        $email = isset($_POST['email']) ? trim($_POST['email']) : '';
        $password = isset($_POST['password']) ? $_POST['password'] : '';
        $password_confirm = isset($_POST['password_confirm']) ? $_POST['password_confirm'] : '';
        $nom = isset($_POST['nom']) ? trim($_POST['nom']) : '';
        $prenom = isset($_POST['prenom']) ? trim($_POST['prenom']) : '';
        $role = isset($_POST['role']) ? $_POST['role'] : '';
        
        // Sauvegarder les données pour les réafficher en cas d'erreur
        $_SESSION['user_old'] = [
            'username' => $username,
            'email' => $email,
            'nom' => $nom,
            'prenom' => $prenom,
            'role' => $role
        ];
        
        // Validation
        if (empty($username) || empty($email) || empty($password) || empty($password_confirm) || empty($nom) || empty($prenom) || empty($role)) {
            $_SESSION['user_error'] = "Tous les champs sont obligatoires.";
            header('Location: index.php?page=admin&action=createUserForm');
            exit;
        }
        
        if ($password !== $password_confirm) {
            $_SESSION['user_error'] = "Les mots de passe ne correspondent pas.";
            header('Location: index.php?page=admin&action=createUserForm');
            exit;
        }
        
        try {
            $result = $this->userModel->create($username, $email, $password, $nom, $prenom, $role);
            
            if ($result) {
                $_SESSION['success_message'] = "L'utilisateur a été créé avec succès.";
                header('Location: index.php?page=admin&action=listUsers');
                exit;
            } else {
                $_SESSION['user_error'] = "Une erreur est survenue lors de la création de l'utilisateur.";
                header('Location: index.php?page=admin&action=createUserForm');
                exit;
            }
        } catch (Exception $e) {
            $_SESSION['user_error'] = $e->getMessage();
            header('Location: index.php?page=admin&action=createUserForm');
            exit;
        }
    }
    
    /**
     * Affiche le formulaire de modification d'utilisateur
     */
    public function editUserForm($id) {
        // Vérifier que l'utilisateur est un administrateur
        if ($_SESSION['role'] !== 'administrateur') {
            $_SESSION['error_message'] = "Accès refusé. Vous devez être administrateur pour gérer les utilisateurs.";
            header('Location: index.php?page=admin');
            exit;
        }
        
        $user = $this->userModel->getById($id);
        
        if (!$user) {
            header('Location: index.php?page=admin&action=listUsers');
            exit;
        }
        
        $pageTitle = "Modifier l'utilisateur";
        $error = isset($_SESSION['user_error']) ? $_SESSION['user_error'] : null;
        
        unset($_SESSION['user_error']);
        
        ob_start();
        include 'views/admin/users/edit.php';
        $content = ob_get_clean();
        
        include 'views/layouts/admin.php';
    }
    
    /**
     * Traite la soumission du formulaire de modification d'utilisateur
     */
    public function updateUser($id) {
        // Vérifier que l'utilisateur est un administrateur
        if ($_SESSION['role'] !== 'administrateur') {
            $_SESSION['error_message'] = "Accès refusé. Vous devez être administrateur pour gérer les utilisateurs.";
            header('Location: index.php?page=admin');
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?page=admin&action=editUserForm&id=' . $id);
            exit;
        }
        
        $user = $this->userModel->getById($id);
        
        if (!$user) {
            header('Location: index.php?page=admin&action=listUsers');
            exit;
        }
        
        // Récupérer les données du formulaire
        $username = isset($_POST['username']) ? trim($_POST['username']) : '';
        $email = isset($_POST['email']) ? trim($_POST['email']) : '';
        $password = isset($_POST['password']) ? $_POST['password'] : '';
        $password_confirm = isset($_POST['password_confirm']) ? $_POST['password_confirm'] : '';
        $nom = isset($_POST['nom']) ? trim($_POST['nom']) : '';
        $prenom = isset($_POST['prenom']) ? trim($_POST['prenom']) : '';
        $role = isset($_POST['role']) ? $_POST['role'] : '';
        
        // Validation
        if (empty($username) || empty($email) || empty($nom) || empty($prenom) || empty($role)) {
            $_SESSION['user_error'] = "Tous les champs sont obligatoires.";
            header('Location: index.php?page=admin&action=editUserForm&id=' . $id);
            exit;
        }
        
        if (!empty($password) && $password !== $password_confirm) {
            $_SESSION['user_error'] = "Les mots de passe ne correspondent pas.";
            header('Location: index.php?page=admin&action=editUserForm&id=' . $id);
            exit;
        }
        
        try {
            $result = $this->userModel->update($id, $username, $email, $nom, $prenom, $role, $password);
            
            if ($result) {
                $_SESSION['success_message'] = "L'utilisateur a été mis à jour avec succès.";
                header('Location: index.php?page=admin&action=listUsers');
                exit;
            } else {
                $_SESSION['user_error'] = "Une erreur est survenue lors de la mise à jour de l'utilisateur.";
                header('Location: index.php?page=admin&action=editUserForm&id=' . $id);
                exit;
            }
        } catch (Exception $e) {
            $_SESSION['user_error'] = $e->getMessage();
            header('Location: index.php?page=admin&action=editUserForm&id=' . $id);
            exit;
        }
    }
    
    /**
     * Supprime un utilisateur
     */
    public function deleteUser($id) {
        // Vérifier que l'utilisateur est un administrateur
        if ($_SESSION['role'] !== 'administrateur') {
            $_SESSION['error_message'] = "Accès refusé. Vous devez être administrateur pour gérer les utilisateurs.";
            header('Location: index.php?page=admin');
            exit;
        }
        
        // Empêcher la suppression de son propre compte
        if ($id == $_SESSION['user_id']) {
            $_SESSION['error_message'] = "Vous ne pouvez pas supprimer votre propre compte.";
            header('Location: index.php?page=admin&action=listUsers');
            exit;
        }
        
        $user = $this->userModel->getById($id);
        
        if (!$user) {
            header('Location: index.php?page=admin&action=listUsers');
            exit;
        }
        
        $result = $this->userModel->delete($id);
        
        if ($result) {
            $_SESSION['success_message'] = "L'utilisateur a été supprimé avec succès.";
        } else {
            $_SESSION['error_message'] = "Une erreur est survenue lors de la suppression de l'utilisateur.";
        }
        
        header('Location: index.php?page=admin&action=listUsers');
        exit;
    }
    
    /**
     * Crée un slug à partir d'une chaîne de caractères
     */
    private function createSlug($string) {
        // Remplacer les caractères spéciaux
        $string = preg_replace('/[^\p{L}\p{N}\s-]/u', '', $string);
        // Remplacer les espaces par des tirets
        $string = preg_replace('/\s+/', '-', $string);
        // Convertir en minuscules
        $string = mb_strtolower($string, 'UTF-8');
        // Supprimer les tirets multiples
        $string = preg_replace('/-+/', '-', $string);
        // Supprimer les tirets au début et à la fin
        $string = trim($string, '-');
        
        return $string;
    }
    
    // ========================================
    // GESTION DES JETONS D'AUTHENTIFICATION
    // ========================================
    
    /**
     * Affiche la liste des jetons d'authentification
     */
    public function listTokens() {
        // Vérifier que l'utilisateur est un administrateur
        if ($_SESSION['role'] !== 'administrateur') {
            $_SESSION['error_message'] = "Accès refusé. Vous devez être administrateur pour gérer les jetons d'authentification.";
            header('Location: index.php?page=admin');
            exit;
        }
        
        $pageTitle = "Gestion des jetons d'authentification";
        $tokens = $this->tokenModel->getAll();
        
        ob_start();
        include 'views/admin/tokens/index.php';
        $content = ob_get_clean();
        
        include 'views/layouts/admin.php';
    }
    
    /**
     * Affiche le formulaire de création de jeton
     */
    public function createTokenForm() {
        // Vérifier que l'utilisateur est un administrateur
        if ($_SESSION['role'] !== 'administrateur') {
            $_SESSION['error_message'] = "Accès refusé. Vous devez être administrateur pour gérer les jetons d'authentification.";
            header('Location: index.php?page=admin');
            exit;
        }
        
        $pageTitle = "Créer un jeton d'authentification";
        $users = $this->userModel->getAll();
        $error = isset($_SESSION['token_error']) ? $_SESSION['token_error'] : null;
        $old = isset($_SESSION['token_old']) ? $_SESSION['token_old'] : [];
        
        unset($_SESSION['token_error']);
        unset($_SESSION['token_old']);
        
        ob_start();
        include 'views/admin/tokens/create.php';
        $content = ob_get_clean();
        
        include 'views/layouts/admin.php';
    }
    
    /**
     * Traite la soumission du formulaire de création de jeton
     */
    public function createToken() {
        // Vérifier que l'utilisateur est un administrateur
        if ($_SESSION['role'] !== 'administrateur') {
            $_SESSION['error_message'] = "Accès refusé. Vous devez être administrateur pour gérer les jetons d'authentification.";
            header('Location: index.php?page=admin');
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?page=admin&action=createTokenForm');
            exit;
        }
        
        // Récupérer les données du formulaire
        $user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;
        $type = isset($_POST['type']) ? $_POST['type'] : 'api';
        $expires_in = isset($_POST['expires_in']) ? $_POST['expires_in'] : '+30 days';
        
        // Sauvegarder les données pour les réafficher en cas d'erreur
        $_SESSION['token_old'] = [
            'user_id' => $user_id,
            'type' => $type,
            'expires_in' => $expires_in
        ];
        
        // Validation
        if ($user_id <= 0) {
            $_SESSION['token_error'] = "Veuillez sélectionner un utilisateur.";
            header('Location: index.php?page=admin&action=createTokenForm');
            exit;
        }
        
        try {
            $result = $this->tokenModel->create($user_id, $type, $expires_in);
            
            if ($result) {
                $_SESSION['success_message'] = "Le jeton d'authentification a été créé avec succès.";
                header('Location: index.php?page=admin&action=listTokens');
                exit;
            } else {
                $_SESSION['token_error'] = "Une erreur est survenue lors de la création du jeton.";
                header('Location: index.php?page=admin&action=createTokenForm');
                exit;
            }
        } catch (Exception $e) {
            $_SESSION['token_error'] = $e->getMessage();
            header('Location: index.php?page=admin&action=createTokenForm');
            exit;
        }
    }
    
    /**
     * Affiche les détails d'un jeton
     */
    public function viewToken($id) {
        // Vérifier que l'utilisateur est un administrateur
        if ($_SESSION['role'] !== 'administrateur') {
            $_SESSION['error_message'] = "Accès refusé. Vous devez être administrateur pour gérer les jetons d'authentification.";
            header('Location: index.php?page=admin');
            exit;
        }
        
        $token = $this->tokenModel->getById($id);
        if (!$token) {
            header('Location: index.php?page=admin&action=listTokens');
            exit;
        }
        $userDao = new UserDao($this->db);
        $user = $userDao->getById($token->getUserId());
        $pageTitle = "Détails du jeton d'authentification";
        ob_start();
        include 'views/admin/tokens/view.php';
        $content = ob_get_clean();
        include 'views/layouts/admin.php';
    }
    
    /**
     * Révoque un jeton (le désactive)
     */
    public function revokeToken($id) {
        // Vérifier que l'utilisateur est un administrateur
        if ($_SESSION['role'] !== 'administrateur') {
            $_SESSION['error_message'] = "Accès refusé. Vous devez être administrateur pour gérer les jetons d'authentification.";
            header('Location: index.php?page=admin');
            exit;
        }
        
        $token = $this->tokenModel->getById($id);
        
        if (!$token) {
            header('Location: index.php?page=admin&action=listTokens');
            exit;
        }
        
        $result = $this->tokenModel->revoke($id);
        
        if ($result) {
            $_SESSION['success_message'] = "Le jeton a été révoqué avec succès.";
        } else {
            $_SESSION['error_message'] = "Une erreur est survenue lors de la révocation du jeton.";
        }
        
        header('Location: index.php?page=admin&action=listTokens');
        exit;
    }
    
    /**
     * Supprime définitivement un jeton
     */
    public function deleteToken($id) {
        // Vérifier que l'utilisateur est un administrateur
        if ($_SESSION['role'] !== 'administrateur') {
            $_SESSION['error_message'] = "Accès refusé. Vous devez être administrateur pour gérer les jetons d'authentification.";
            header('Location: index.php?page=admin');
            exit;
        }
        
        $token = $this->tokenModel->getById($id);
        
        if (!$token) {
            header('Location: index.php?page=admin&action=listTokens');
            exit;
        }
        
        $result = $this->tokenModel->delete($id);
        
        if ($result) {
            $_SESSION['success_message'] = "Le jeton a été supprimé définitivement.";
        } else {
            $_SESSION['error_message'] = "Une erreur est survenue lors de la suppression du jeton.";
        }
        
        header('Location: index.php?page=admin&action=listTokens');
        exit;
    }
}
?> 