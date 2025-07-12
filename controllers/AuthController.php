<?php
require_once 'models/dao/UserDao.php';
require_once 'models/dao/CategorieDao.php';
require_once 'models/domaine/User.php';
require_once 'models/domaine/Category.php';

class AuthController {
    private $db;
    private $userDao;
    private $categoryModel;
    
    public function __construct($db) {
        $this->db = $db;
        $this->userDao = new UserDao($db);
        $categorieDao = new CategorieDao($db);
        $this->categoryModel = new Category($categorieDao);
    }
    
    /**
     * Affiche le formulaire de connexion
     */
    public function showLoginForm() {
        // Vérifier si l'utilisateur est déjà connecté
        if (isset($_SESSION['user_id'])) {
            header('Location: index.php');
            exit;
        }
        
        $pageTitle = "Connexion";
        $error = isset($_SESSION['login_error']) ? $_SESSION['login_error'] : null;
        unset($_SESSION['login_error']);
        
        // Récupérer les catégories pour le menu
        $categories = $this->categoryModel->getAll();
        
        // Inclure la vue
        ob_start();
        include 'views/auth/login.php';
        $content = ob_get_clean();
        
        include 'views/layouts/default.php';
    }
    
    /**
     * Traite la soumission du formulaire de connexion
     */
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?page=login');
            exit;
        }
        
        $username = isset($_POST['username']) ? trim($_POST['username']) : '';
        $password = isset($_POST['password']) ? $_POST['password'] : '';
        
        // Validation de base
        if (empty($username) || empty($password)) {
            $_SESSION['login_error'] = "Tous les champs sont obligatoires.";
            header('Location: index.php?page=login');
            exit;
        }
        
        // Vérifier si l'utilisateur existe (par nom d'utilisateur ou email)
        $user = $this->userDao->getByUsername($username);
        if (!$user) {
            $user = $this->userDao->getByEmail($username); // Permet la connexion avec l'email aussi
        }
        
        if (!$user || !password_verify($password, $user->getPassword())) {
            $_SESSION['login_error'] = "Identifiants incorrects.";
            header('Location: index.php?page=login');
            exit;
        }
        
        // Vérifier si le compte est actif
        if (!$user->isActif()) {
            $_SESSION['login_error'] = "Votre compte a été désactivé.";
            header('Location: index.php?page=login');
            exit;
        }
        
        // Connexion réussie
        $_SESSION['user_id'] = $user->getId();
        $_SESSION['username'] = $user->getUsername();
        $_SESSION['role'] = $user->getRole();
        $_SESSION['nom_complet'] = $user->getNomComplet();
        
        // Mettre à jour la dernière connexion
        $this->userDao->updateLastLogin($user->getId());
        
        // Rediriger vers la page d'accueil
        header('Location: index.php');
        exit;
    }
    
    /**
     * Affiche le formulaire d'inscription
     */
    public function showRegisterForm() {
        // Vérifier si l'utilisateur est déjà connecté
        if (isset($_SESSION['user_id'])) {
            header('Location: index.php');
            exit;
        }
        
        $pageTitle = "Inscription";
        $error = isset($_SESSION['register_error']) ? $_SESSION['register_error'] : null;
        unset($_SESSION['register_error']);
        
        // Récupérer les anciennes valeurs en cas d'erreur
        $old = isset($_SESSION['register_old']) ? $_SESSION['register_old'] : [];
        unset($_SESSION['register_old']);
        
        // Récupérer les catégories pour le menu
        $categories = $this->categoryModel->getAll();
        
        // Inclure la vue
        ob_start();
        include 'views/auth/register.php';
        $content = ob_get_clean();
        
        include 'views/layouts/default.php';
    }
    
    /**
     * Traite la soumission du formulaire d'inscription
     */
    public function register() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?page=register');
            exit;
        }
        
        // Récupérer les données du formulaire
        $username = isset($_POST['username']) ? trim($_POST['username']) : '';
        $email = isset($_POST['email']) ? trim($_POST['email']) : '';
        $password = isset($_POST['password']) ? $_POST['password'] : '';
        $password_confirm = isset($_POST['password_confirm']) ? $_POST['password_confirm'] : '';
        $nom = isset($_POST['nom']) ? trim($_POST['nom']) : '';
        $prenom = isset($_POST['prenom']) ? trim($_POST['prenom']) : '';
        
        // Sauvegarder les données pour les réafficher en cas d'erreur
        $_SESSION['register_old'] = [
            'username' => $username,
            'email' => $email,
            'nom' => $nom,
            'prenom' => $prenom
        ];
        
        // Validation
        if (empty($username) || empty($email) || empty($password) || empty($nom) || empty($prenom)) {
            $_SESSION['register_error'] = "Tous les champs sont obligatoires.";
            header('Location: index.php?page=register');
            exit;
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['register_error'] = "L'adresse email n'est pas valide.";
            header('Location: index.php?page=register');
            exit;
        }
        
        if ($password !== $password_confirm) {
            $_SESSION['register_error'] = "Les mots de passe ne correspondent pas.";
            header('Location: index.php?page=register');
            exit;
        }
        
        if (strlen($password) < 8) {
            $_SESSION['register_error'] = "Le mot de passe doit contenir au moins 8 caractères.";
            header('Location: index.php?page=register');
            exit;
        }
        
        // Vérifier si le nom d'utilisateur ou l'email existe déjà
        if ($this->userDao->usernameExists($username)) {
            $_SESSION['register_error'] = "Ce nom d'utilisateur est déjà utilisé.";
            header('Location: index.php?page=register');
            exit;
        }
        
        if ($this->userDao->emailExists($email)) {
            $_SESSION['register_error'] = "Cette adresse email est déjà utilisée.";
            header('Location: index.php?page=register');
            exit;
        }
        
        // Créer l'utilisateur
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $user = new User(null, $username, $email, $hashedPassword, $nom, $prenom, 'visiteur', 1);
        
        if ($this->userDao->create($user)) {
            // Inscription réussie, connecter l'utilisateur
            $_SESSION['user_id'] = $user->getId();
            $_SESSION['username'] = $user->getUsername();
            $_SESSION['role'] = $user->getRole();
            $_SESSION['nom_complet'] = $user->getNomComplet();
            
            // Nettoyer les données temporaires
            unset($_SESSION['register_old']);
            
            // Rediriger vers la page d'accueil
            header('Location: index.php');
            exit;
        } else {
            $_SESSION['register_error'] = "Une erreur est survenue lors de l'inscription.";
            header('Location: index.php?page=register');
            exit;
        }
    }
    
    /**
     * Déconnecte l'utilisateur
     */
    public function logout() {
        // Détruire toutes les variables de session
        $_SESSION = [];
        
        // Si un cookie de session est utilisé, le détruire
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        
        // Détruire la session
        session_destroy();
        
        // Rediriger vers la page d'accueil
        header('Location: index.php');
        exit;
    }
    
    /**
     * Vérifie si l'utilisateur est connecté
     */
    public static function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }
    
    /**
     * Vérifie si l'utilisateur est un éditeur ou un administrateur
     */
    public static function isEditor() {
        return isset($_SESSION['role']) && ($_SESSION['role'] === 'editeur' || $_SESSION['role'] === 'administrateur');
    }
    
    /**
     * Vérifie si l'utilisateur est un administrateur
     */
    public static function isAdmin() {
        return isset($_SESSION['role']) && $_SESSION['role'] === 'administrateur';
    }
    
    /**
     * Redirige si l'utilisateur n'est pas connecté
     */
    public static function requireLogin() {
        if (!self::isLoggedIn()) {
            $_SESSION['login_error'] = "Vous devez être connecté pour accéder à cette page.";
            header('Location: index.php?page=login');
            exit;
        }
    }
    
    /**
     * Redirige si l'utilisateur n'est pas éditeur
     */
    public static function requireEditor() {
        self::requireLogin();
        if (!self::isEditor()) {
            header('Location: index.php');
            exit;
        }
    }
    
    /**
     * Redirige si l'utilisateur n'est pas administrateur
     */
    public static function requireAdmin() {
        self::requireLogin();
        if (!self::isAdmin()) {
            header('Location: index.php');
            exit;
        }
    }
}
?> 