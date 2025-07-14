<?php
// Démarrer la session
session_start();

// Inclure les fichiers nécessaires
require_once '../config/database.php';
require_once '../models/dao/UserDao.php';
require_once '../models/dao/AuthTokenDao.php';
require_once '../models/domaine/User.php';
require_once '../models/domaine/AuthToken.php';

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

// Configurer le serveur SOAP
$options = [
    'uri' => 'http://localhost/dic2_news/api/soap_users.php',
    'soap_version' => SOAP_1_2,
    'encoding' => 'UTF-8',
    'exceptions' => true
];

// Vérifier si c'est une requête WSDL
if (isset($_GET['wsdl'])) {
    // Générer le WSDL
    $wsdlGenerator = new WSDLGenerator();
    $wsdlGenerator->generate('UserService', $options['uri']);
    exit;
}

// Créer le serveur SOAP
$server = new SoapServer(null, $options);
$server->setClass('UserService');

// Gérer la requête
$server->handle();

/**
 * Classe simple pour générer un WSDL
 */
class WSDLGenerator {
    public function generate($className, $namespace) {
        // En-têtes pour le XML
        header('Content-Type: text/xml; charset=utf-8');
        
        // Générer le WSDL complet en une seule fois pour éviter les problèmes de formatage XML
        $wsdl = '<?xml version="1.0" encoding="UTF-8"?>
<definitions name="' . $className . '" 
            targetNamespace="' . $namespace . '" 
            xmlns="http://schemas.xmlsoap.org/wsdl/" 
            xmlns:tns="' . $namespace . '" 
            xmlns:soap="http://schemas.xmlsoap.org/wsdl/soap/" 
            xmlns:xsd="http://www.w3.org/2001/XMLSchema" 
            xmlns:soapenc="http://schemas.xmlsoap.org/soap/encoding/" 
            xmlns:wsdl="http://schemas.xmlsoap.org/wsdl/" 
            xmlns:soap12="http://schemas.xmlsoap.org/wsdl/soap12/">
    <types>
        <xsd:schema targetNamespace="' . $namespace . '">
            <xsd:complexType name="User">
                <xsd:all>
                    <xsd:element name="id" type="xsd:int"/>
                    <xsd:element name="username" type="xsd:string"/>
                    <xsd:element name="email" type="xsd:string"/>
                    <xsd:element name="nom" type="xsd:string"/>
                    <xsd:element name="prenom" type="xsd:string"/>
                    <xsd:element name="role" type="xsd:string"/>
                    <xsd:element name="dateCreation" type="xsd:string" minOccurs="0"/>
                    <xsd:element name="dateModification" type="xsd:string" minOccurs="0"/>
                    <xsd:element name="actif" type="xsd:boolean" minOccurs="0"/>
                    <xsd:element name="derniereConnexion" type="xsd:string" minOccurs="0"/>
                </xsd:all>
            </xsd:complexType>
            <xsd:complexType name="ArrayOfUsers">
                <xsd:complexContent>
                    <xsd:restriction base="soapenc:Array">
                        <xsd:attribute ref="soapenc:arrayType" wsdl:arrayType="tns:User[]"/>
                    </xsd:restriction>
                </xsd:complexContent>
            </xsd:complexType>
        </xsd:schema>
    </types>
    
    <message name="listUsersRequest">
        <part name="token" type="xsd:string"/>
    </message>
    <message name="listUsersResponse">
        <part name="return" type="tns:ArrayOfUsers"/>
    </message>
    
    <message name="getUserRequest">
        <part name="token" type="xsd:string"/>
        <part name="userId" type="xsd:int"/>
    </message>
    <message name="getUserResponse">
        <part name="return" type="tns:User"/>
    </message>
    
    <message name="createUserRequest">
        <part name="token" type="xsd:string"/>
        <part name="username" type="xsd:string"/>
        <part name="email" type="xsd:string"/>
        <part name="password" type="xsd:string"/>
        <part name="nom" type="xsd:string"/>
        <part name="prenom" type="xsd:string"/>
        <part name="role" type="xsd:string"/>
    </message>
    <message name="createUserResponse">
        <part name="return" type="xsd:int"/>
    </message>
    
    <message name="updateUserRequest">
        <part name="token" type="xsd:string"/>
        <part name="userId" type="xsd:int"/>
        <part name="username" type="xsd:string"/>
        <part name="email" type="xsd:string"/>
        <part name="nom" type="xsd:string"/>
        <part name="prenom" type="xsd:string"/>
        <part name="role" type="xsd:string"/>
        <part name="password" type="xsd:string"/>
    </message>
    <message name="updateUserResponse">
        <part name="return" type="xsd:boolean"/>
    </message>
    
    <message name="deleteUserRequest">
        <part name="token" type="xsd:string"/>
        <part name="userId" type="xsd:int"/>
    </message>
    <message name="deleteUserResponse">
        <part name="return" type="xsd:boolean"/>
    </message>
    
    <message name="authenticateRequest">
        <part name="username" type="xsd:string"/>
        <part name="password" type="xsd:string"/>
    </message>
    <message name="authenticateResponse">
        <part name="return" type="tns:User"/>
    </message>
    
    <portType name="' . $className . 'PortType">
        <operation name="listUsers">
            <input message="tns:listUsersRequest"/>
            <output message="tns:listUsersResponse"/>
        </operation>
        <operation name="getUser">
            <input message="tns:getUserRequest"/>
            <output message="tns:getUserResponse"/>
        </operation>
        <operation name="createUser">
            <input message="tns:createUserRequest"/>
            <output message="tns:createUserResponse"/>
        </operation>
        <operation name="updateUser">
            <input message="tns:updateUserRequest"/>
            <output message="tns:updateUserResponse"/>
        </operation>
        <operation name="deleteUser">
            <input message="tns:deleteUserRequest"/>
            <output message="tns:deleteUserResponse"/>
        </operation>
        <operation name="authenticate">
            <input message="tns:authenticateRequest"/>
            <output message="tns:authenticateResponse"/>
        </operation>
    </portType>
    
    <binding name="' . $className . 'Binding" type="tns:' . $className . 'PortType">
        <soap12:binding style="rpc" transport="http://schemas.xmlsoap.org/soap/http"/>
        <operation name="listUsers">
            <soap12:operation soapAction="' . $namespace . '#listUsers"/>
            <input><soap12:body use="encoded" namespace="' . $namespace . '" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"/></input>
            <output><soap12:body use="encoded" namespace="' . $namespace . '" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"/></output>
        </operation>
        <operation name="getUser">
            <soap12:operation soapAction="' . $namespace . '#getUser"/>
            <input><soap12:body use="encoded" namespace="' . $namespace . '" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"/></input>
            <output><soap12:body use="encoded" namespace="' . $namespace . '" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"/></output>
        </operation>
        <operation name="createUser">
            <soap12:operation soapAction="' . $namespace . '#createUser"/>
            <input><soap12:body use="encoded" namespace="' . $namespace . '" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"/></input>
            <output><soap12:body use="encoded" namespace="' . $namespace . '" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"/></output>
        </operation>
        <operation name="updateUser">
            <soap12:operation soapAction="' . $namespace . '#updateUser"/>
            <input><soap12:body use="encoded" namespace="' . $namespace . '" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"/></input>
            <output><soap12:body use="encoded" namespace="' . $namespace . '" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"/></output>
        </operation>
        <operation name="deleteUser">
            <soap12:operation soapAction="' . $namespace . '#deleteUser"/>
            <input><soap12:body use="encoded" namespace="' . $namespace . '" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"/></input>
            <output><soap12:body use="encoded" namespace="' . $namespace . '" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"/></output>
        </operation>
        <operation name="authenticate">
            <soap12:operation soapAction="' . $namespace . '#authenticate"/>
            <input><soap12:body use="encoded" namespace="' . $namespace . '" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"/></input>
            <output><soap12:body use="encoded" namespace="' . $namespace . '" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"/></output>
        </operation>
    </binding>
    
    <service name="' . $className . 'Service">
        <port name="' . $className . 'Port" binding="tns:' . $className . 'Binding">
            <soap12:address location="' . $namespace . '"/>
        </port>
    </service>
</definitions>';
        
        // Afficher le WSDL
        echo $wsdl;
    }
} 