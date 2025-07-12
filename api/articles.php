<?php
/**
 * API REST pour les articles
 * Permet de récupérer les articles au format XML ou JSON
 * Sécurisé par authentification token
 */

// Activer la mise en tampon de sortie pour contrôler ce qui est envoyé au navigateur
ob_start();

// Headers pour CORS et content negotiation
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Accept, Authorization');

// Gérer les requêtes OPTIONS (CORS preflight)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Inclure les fichiers nécessaires
require_once '../config/database.php';
require_once '../models/dao/ArticleDao.php';
require_once '../models/dao/CategorieDao.php';
require_once '../models/domaine/Article.php';
require_once '../models/domaine/Category.php';
require_once '../models/dao/AuthTokenDao.php';
require_once '../models/dao/UserDao.php';
require_once '../models/domaine/AuthToken.php';
require_once '../models/domaine/User.php';

// Connexion à la base de données
$db = getDbConnection();
$articleDao = new ArticleDao($db);
$categorieDao = new CategorieDao($db);
$tokenDao = new AuthTokenDao($db);
$userDao = new UserDao($db);

// Fonction pour extraire le token du header Authorization
function getBearerToken() {
    $headers = getallheaders();
    if (isset($headers['Authorization'])) {
        if (preg_match('/Bearer\s(\S+)/', $headers['Authorization'], $matches)) {
            return $matches[1];
        }
    }
    return null;
}

// Fonction pour vérifier l'authentification
function authenticateToken($token) {
    global $tokenDao, $userDao;
    
    if (!$token) {
        return null;
    }
    
    $tokenObj = $tokenDao->getByToken($token);
    if (!$tokenObj || !$tokenObj->isValid()) {
        return null;
    }
    
    // Mettre à jour la dernière utilisation
    $tokenDao->updateLastUsed($tokenObj->getId());
    
    // Récupérer l'utilisateur
    $user = $userDao->getById($tokenObj->getUserId());
    return $user;
}

// Authentification
$token = getBearerToken();
$user = authenticateToken($token);

// Déterminer le format de sortie (XML ou JSON)
$format = 'json'; // Format par défaut
if (isset($_GET['format'])) {
    if (strtolower($_GET['format']) === 'xml') {
        $format = 'xml';
    } else if (strtolower($_GET['format']) === 'json') {
        $format = 'json';
    }
} else {
    // Si format n'est pas spécifié, on utilise JSON par défaut
    // On ignore l'en-tête Accept pour éviter les confusions
    $format = 'json';
}

// Fonction pour envoyer une réponse au format approprié
function sendResponse($data, $format) {
    if ($format === 'xml') {
        header('Content-Type: application/xml; charset=UTF-8');
        echo generateXML($data);
    } else {
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
}

// Vérification de l'authentification
if (!$user) {
    http_response_code(401);
    sendResponse([
        'error' => 'Token d\'authentification invalide ou manquant',
        'message' => 'Veuillez fournir un token valide dans le header Authorization: Bearer <token>'
    ], $format);
    exit();
}

// Fonction pour générer du XML à partir d'un tableau
function generateXML($data) {
    // S'assurer qu'aucun contenu n'a été envoyé avant
    if (ob_get_length() > 0) {
        ob_clean(); // Nettoyer le buffer de sortie
    }
    
    // Créer un document XML avec un élément racine
    $xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><response></response>');
    
    // Fonction récursive pour convertir un tableau en XML
    $arrayToXML = function($data, &$xml) use (&$arrayToXML) {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                if (is_numeric($key)) {
                    // Pour les tableaux numériques avec des clés spéciales
                    if (isset($data['articles'])) {
                        $key = 'article';
                    } elseif (isset($data['categories'])) {
                        $key = 'category';
                    } else {
                        $key = 'item';
                    }
                    $subNode = $xml->addChild($key);
                    $arrayToXML($value, $subNode);
                } else {
                    // Pour les tableaux associatifs
                    $subNode = $xml->addChild($key);
                    $arrayToXML($value, $subNode);
                }
            } else {
                // Pour les valeurs simples
                if (is_numeric($key)) {
                    $xml->addChild('item', htmlspecialchars((string)$value));
                } else {
                    // Gérer les valeurs nulles
                    if ($value === null) {
                        $xml->addChild($key);
                    } else {
                        $xml->addChild($key, htmlspecialchars((string)$value));
                    }
                }
            }
        }
    };
    
    // Convertir le tableau en XML
    $arrayToXML($data, $xml);
    
    // Formater le XML pour qu'il soit lisible
    $dom = new DOMDocument('1.0', 'UTF-8');
    $dom->preserveWhiteSpace = false;
    $dom->formatOutput = true;
    $dom->loadXML($xml->asXML());
    
    return $dom->saveXML();
}

// Router pour les différentes routes de l'API
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$endpoint = basename($path);

// Extraire les paramètres de l'URL
$categoryId = isset($_GET['category']) ? (int)$_GET['category'] : null;

// Traiter les différentes routes
switch ($endpoint) {
    case 'articles.php':
        // Route 1: Récupérer tous les articles
        $articles = $articleDao->getAll();
        $result = ['articles' => []];
        
        foreach ($articles as $article) {
            $result['articles'][] = [
                'id' => $article['id'],
                'titre' => $article['titre'],
                'slug' => $article['slug'],
                'contenu' => $article['contenu'],
                'dateCreation' => $article['dateCreation'],
                'categorie' => [
                    'id' => $article['categorie'],
                    'nom' => $article['categorie_nom']
                ]
            ];
        }
        
        sendResponse($result, $format);
        break;
        
    case 'categories.php':
        // Route 2: Récupérer les articles regroupés par catégories
        $categories = $categorieDao->getAll();
        $result = ['categories' => []];
        
        foreach ($categories as $category) {
            $categoryArticles = $articleDao->getByCategory($category->getId());
            $articlesData = [];
            
            foreach ($categoryArticles as $article) {
                $articlesData[] = [
                    'id' => $article['id'],
                    'titre' => $article['titre'],
                    'slug' => $article['slug'],
                    'contenu' => substr($article['contenu'], 0, 200) . '...',
                    'dateCreation' => $article['dateCreation']
                ];
            }
            
            $result['categories'][] = [
                'id' => $category->getId(),
                'nom' => $category->getLibelle(),
                'slug' => $category->getSlug(),
                'description' => $category->getDescription(),
                'articles' => $articlesData
            ];
        }
        
        sendResponse($result, $format);
        break;
        
    case 'category.php':
        // Route 3: Récupérer les articles d'une catégorie spécifique
        if (!$categoryId) {
            sendResponse(['error' => 'ID de catégorie requis'], $format);
            http_response_code(400);
            exit();
        }
        
        $category = $categorieDao->getById($categoryId);
        if (!$category) {
            sendResponse(['error' => 'Catégorie non trouvée'], $format);
            http_response_code(404);
            exit();
        }
        
        $articles = $articleDao->getByCategory($categoryId);
        $articlesData = [];
        
        foreach ($articles as $article) {
            $articlesData[] = [
                'id' => $article['id'],
                'titre' => $article['titre'],
                'slug' => $article['slug'],
                'contenu' => $article['contenu'],
                'dateCreation' => $article['dateCreation']
            ];
        }
        
        $result = [
            'category' => [
                'id' => $category->getId(),
                'nom' => $category->getLibelle(),
                'slug' => $category->getSlug(),
                'description' => $category->getDescription()
            ],
            'articles' => $articlesData
        ];
        
        sendResponse($result, $format);
        break;
        
    default:
        // Route non trouvée
        http_response_code(404);
        sendResponse(['error' => 'Endpoint non trouvé'], $format);
        break;
}