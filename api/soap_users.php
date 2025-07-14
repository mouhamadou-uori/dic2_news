<?php
// Désactiver l'affichage des erreurs pour garantir une sortie SOAP propre
ini_set('display_errors', 0);
error_reporting(0);

// Démarrer le tampon de sortie pour capturer toute sortie inattendue
ob_start();

// Démarrer la session
session_start();

// Inclure les fichiers nécessaires
require_once '../config/database.php';
require_once '../models/dao/UserDao.php';
require_once '../models/dao/AuthTokenDao.php';
require_once '../models/domaine/User.php';
require_once '../models/domaine/AuthToken.php';
require_once dirname(__DIR__) . '/vendor/autoload.php'; // Inclure l'autoloader de Composer

// Fonction pour vérifier la validité du token
function validateToken($token) {
    $db = getDbConnection();
    $authTokenDao = new AuthTokenDao($db);
    
    // Vérifier si le token existe et est valide
    $tokenObj = $authTokenDao->getByToken($token);
    if (!$tokenObj || !$tokenObj->isValid()) {
        throw new SoapFault('INVALID_TOKEN', 'Token d\'authentification invalide ou expiré');
    }
    
    // Mettre à jour la date de dernière utilisation du token
    $authTokenDao->updateLastUsed($tokenObj->getId());
    
    return true;
}

// Classe du service SOAP
class UserService {
    private $userDao;
    
    public function __construct() {
        $db = getDbConnection();
        $this->userDao = new UserDao($db);
    }
    
    /**
     * Liste tous les utilisateurs
     * 
     * @param string $token Jeton d'authentification
     * @return array Liste des utilisateurs
     */
    public function listUsers($token) {
        try {
            // Vérifier la validité du token
            validateToken($token);
            
            // Récupérer tous les utilisateurs
            $users = $this->userDao->getAll();
            
            // Formater les données pour le retour
            $result = [];
            foreach ($users as $userData) {
                // Exclure le mot de passe pour des raisons de sécurité
                unset($userData['password']);
                $result[] = $userData;
            }
            
            return $result;
        } catch (SoapFault $e) {
            throw $e;
        } catch (Exception $e) {
            throw new SoapFault('SERVER_ERROR', $e->getMessage());
        }
    }
    
    /**
     * Récupère les informations d'un utilisateur par son ID
     * 
     * @param string $token Jeton d'authentification
     * @param int $userId ID de l'utilisateur
     * @return array Informations de l'utilisateur
     */
    public function getUser($token, $userId) {
        try {
            // Vérifier la validité du token
            validateToken($token);
            
            // Récupérer l'utilisateur
            $user = $this->userDao->getById($userId);
            
            if (!$user) {
                throw new SoapFault('NOT_FOUND', 'Utilisateur non trouvé');
            }
            
            // Convertir l'objet en tableau et exclure le mot de passe
            $userData = [
                'id' => $user->getId(),
                'username' => $user->getUsername(),
                'email' => $user->getEmail(),
                'nom' => $user->getNom(),
                'prenom' => $user->getPrenom(),
                'role' => $user->getRole(),
                'dateCreation' => $user->getDateCreation(),
                'dateModification' => $user->getDateModification(),
                'actif' => $user->isActif(),
                'derniereConnexion' => $user->getDerniereConnexion()
            ];
            
            return $userData;
        } catch (SoapFault $e) {
            throw $e;
        } catch (Exception $e) {
            throw new SoapFault('SERVER_ERROR', $e->getMessage());
        }
    }
    
    /**
     * Crée un nouvel utilisateur
     * 
     * @param string $token Jeton d'authentification
     * @param string $username Nom d'utilisateur
     * @param string $email Adresse email
     * @param string $password Mot de passe
     * @param string $nom Nom de famille
     * @param string $prenom Prénom
     * @param string $role Rôle (visiteur, editeur, administrateur)
     * @return int ID de l'utilisateur créé
     */
    public function createUser($token, $username, $email, $password, $nom, $prenom, $role = 'visiteur') {
        try {
            // Vérifier la validité du token
            validateToken($token);
            
            // Valider les données
            if (empty($username) || empty($email) || empty($password) || empty($nom) || empty($prenom)) {
                throw new SoapFault('INVALID_PARAMS', 'Tous les champs obligatoires doivent être remplis');
            }
            
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new SoapFault('INVALID_PARAMS', 'L\'adresse email n\'est pas valide');
            }
            
            // Vérifier si le nom d'utilisateur ou l'email existe déjà
            if ($this->userDao->usernameExists($username)) {
                throw new SoapFault('DUPLICATE_USERNAME', 'Ce nom d\'utilisateur existe déjà');
            }
            
            if ($this->userDao->emailExists($email)) {
                throw new SoapFault('DUPLICATE_EMAIL', 'Cette adresse email existe déjà');
            }
            
            // Valider le rôle
            $validRoles = ['visiteur', 'editeur', 'administrateur'];
            if (!in_array($role, $validRoles)) {
                throw new SoapFault('INVALID_ROLE', 'Rôle invalide. Les valeurs acceptées sont: ' . implode(', ', $validRoles));
            }
            
            // Créer l'utilisateur
            $result = $this->userDao->create($username, $email, $password, $nom, $prenom, $role);
            
            if (!$result) {
                throw new SoapFault('CREATE_ERROR', 'Erreur lors de la création de l\'utilisateur');
            }
            
            // Récupérer l'ID du nouvel utilisateur
            $user = $this->userDao->getByUsername($username);
            return $user->getId();
        } catch (SoapFault $e) {
            throw $e;
        } catch (Exception $e) {
            throw new SoapFault('SERVER_ERROR', $e->getMessage());
        }
    }
    
    /**
     * Met à jour un utilisateur existant
     * 
     * @param string $token Jeton d'authentification
     * @param int $userId ID de l'utilisateur à mettre à jour
     * @param string $username Nom d'utilisateur
     * @param string $email Adresse email
     * @param string $nom Nom de famille
     * @param string $prenom Prénom
     * @param string $role Rôle (visiteur, editeur, administrateur)
     * @param string $password Nouveau mot de passe (optionnel)
     * @return boolean Résultat de l'opération
     */
    public function updateUser($token, $userId, $username, $email, $nom, $prenom, $role, $password = null) {
        try {
            // Vérifier la validité du token
            validateToken($token);
            
            // Vérifier si l'utilisateur existe
            $user = $this->userDao->getById($userId);
            if (!$user) {
                throw new SoapFault('NOT_FOUND', 'Utilisateur non trouvé');
            }
            
            // Valider les données
            if (empty($username) || empty($email) || empty($nom) || empty($prenom)) {
                throw new SoapFault('INVALID_PARAMS', 'Tous les champs obligatoires doivent être remplis');
            }
            
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new SoapFault('INVALID_PARAMS', 'L\'adresse email n\'est pas valide');
            }
            
            // Vérifier si le nom d'utilisateur ou l'email existe déjà (en excluant l'utilisateur actuel)
            if ($this->userDao->usernameExists($username, $userId)) {
                throw new SoapFault('DUPLICATE_USERNAME', 'Ce nom d\'utilisateur existe déjà');
            }
            
            if ($this->userDao->emailExists($email, $userId)) {
                throw new SoapFault('DUPLICATE_EMAIL', 'Cette adresse email existe déjà');
            }
            
            // Valider le rôle
            $validRoles = ['visiteur', 'editeur', 'administrateur'];
            if (!in_array($role, $validRoles)) {
                throw new SoapFault('INVALID_ROLE', 'Rôle invalide. Les valeurs acceptées sont: ' . implode(', ', $validRoles));
            }
            
            // Mettre à jour l'utilisateur
            $result = $this->userDao->update($userId, $username, $email, $nom, $prenom, $role, $password);
            
            if (!$result) {
                throw new SoapFault('UPDATE_ERROR', 'Erreur lors de la mise à jour de l\'utilisateur');
            }
            
            return true;
        } catch (SoapFault $e) {
            throw $e;
        } catch (Exception $e) {
            throw new SoapFault('SERVER_ERROR', $e->getMessage());
        }
    }
    
    /**
     * Supprime un utilisateur
     * 
     * @param string $token Jeton d'authentification
     * @param int $userId ID de l'utilisateur à supprimer
     * @return boolean Résultat de l'opération
     */
    public function deleteUser($token, $userId) {
        try {
            // Vérifier la validité du token
            validateToken($token);
            
            // Vérifier si l'utilisateur existe
            $user = $this->userDao->getById($userId);
            if (!$user) {
                throw new SoapFault('NOT_FOUND', 'Utilisateur non trouvé');
            }
            
            // Supprimer l'utilisateur
            $result = $this->userDao->delete($userId);
            
            if (!$result) {
                throw new SoapFault('DELETE_ERROR', 'Erreur lors de la suppression de l\'utilisateur');
            }
            
            return true;
        } catch (SoapFault $e) {
            throw $e;
        } catch (Exception $e) {
            throw new SoapFault('SERVER_ERROR', $e->getMessage());
        }
    }
    
    /**
     * Authentifie un utilisateur
     * 
     * @param string $username Nom d'utilisateur ou email
     * @param string $password Mot de passe
     * @return array Informations de l'utilisateur authentifié
     */
    public function authenticate($username, $password) {
        try {
            // Valider les données
            if (empty($username) || empty($password)) {
                throw new SoapFault('INVALID_PARAMS', 'Le nom d\'utilisateur et le mot de passe sont requis');
            }
            
            // Récupérer l'utilisateur par nom d'utilisateur ou email
            $user = $this->userDao->getByUsername($username);
            if (!$user) {
                // Essayer avec l'email
                $user = $this->userDao->getByEmail($username);
            }
            
            if (!$user || !password_verify($password, $user->getPassword())) {
                throw new SoapFault('AUTH_FAILED', 'Identifiants incorrects');
            }
            
            // Vérifier si le compte est actif
            if (!$user->isActif()) {
                throw new SoapFault('ACCOUNT_DISABLED', 'Ce compte a été désactivé');
            }
            
            // Mettre à jour la dernière connexion
            $this->userDao->updateLastLogin($user->getId());
            
            // Préparer les données de retour (sans le mot de passe)
            $userData = [
                'id' => $user->getId(),
                'username' => $user->getUsername(),
                'email' => $user->getEmail(),
                'nom' => $user->getNom(),
                'prenom' => $user->getPrenom(),
                'role' => $user->getRole(),
                'nom_complet' => $user->getNomComplet()
            ];
            
            return $userData;
        } catch (SoapFault $e) {
            throw $e;
        } catch (Exception $e) {
            throw new SoapFault('SERVER_ERROR', $e->getMessage());
        }
    }
}

// Définir l'URI du service de manière dynamique
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'];
$scriptName = $_SERVER['SCRIPT_NAME'];
$uri = "$protocol://$host$scriptName";

// Gérer la requête WSDL
if (isset($_GET['wsdl'])) {
    // Nettoyer le tampon de sortie pour s'assurer qu'aucun contenu parasite n'est envoyé
    ob_end_clean();

    // Utiliser laminas-soap pour générer le WSDL automatiquement
    $autodiscover = new Laminas\Soap\AutoDiscover();
    $autodiscover->setClass('UserService')
                 ->setUri($uri);
    
    header('Content-Type: application/wsdl+xml');
    echo $autodiscover->toXml();
    exit;
}

// Gérer les requêtes SOAP normales
$options = [
    'uri' => $uri,
    'soap_version' => SOAP_1_2,
    'encoding' => 'UTF-8',
    'exceptions' => true
];

// Le WSDL est l'URL du service lui-même avec '?wsdl' en paramètre
$server = new SoapServer($uri . '?wsdl', $options);
$server->setClass('UserService');

try {
    $server->handle();
} catch (SoapFault $f) {
    error_log($f->getMessage());
    $server->fault($f->faultcode, $f->faultstring);
}

// Envoyer le contenu du tampon et le désactiver
ob_end_flush();
