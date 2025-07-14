<?php
/**
 * Exemple d'utilisation du service SOAP de gestion des utilisateurs
 */

// Activer l'affichage des erreurs pour le débogage
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// URL du service SOAP
$wsdlUrl = 'http://localhost/dic2_news/api/soap_users.php?wsdl';

try {
    // Créer le client SOAP
    $client = new SoapClient($wsdlUrl, [
        'trace' => 1,        // Activer le suivi des requêtes/réponses pour le débogage
        'exceptions' => true, // Activer les exceptions
        'cache_wsdl' => WSDL_CACHE_NONE // Désactiver le cache WSDL pendant le développement
    ]);
    
    echo "<h1>Exemple d'utilisation du service SOAP de gestion des utilisateurs</h1>";
    
    // 1. Authentification d'un utilisateur
    echo "<h2>1. Authentification d'un utilisateur</h2>";
    try {
        $result = $client->authenticate('admin', 'password');
        echo "<pre>Utilisateur authentifié : \n";
        print_r($result);
        echo "</pre>";
    } catch (SoapFault $e) {
        echo "<div style='color: red;'>Erreur d'authentification : " . $e->getMessage() . "</div>";
    }
    
    // Pour les exemples suivants, nous avons besoin d'un jeton d'authentification
    // Normalement, ce jeton serait généré par un administrateur depuis l'interface d'administration
    $token = "f98dc1fe18c2ca9c11d7758bc97e476b345da7a5c2cff65040ffc3f1e7acb931"; // Remplacer par un vrai jeton
    
    // 2. Lister tous les utilisateurs
    echo "<h2>2. Lister tous les utilisateurs</h2>";
    try {
        $users = $client->listUsers($token);
        echo "<pre>Liste des utilisateurs : \n";
        print_r($users);
        echo "</pre>";
    } catch (SoapFault $e) {
        echo "<div style='color: red;'>Erreur lors de la récupération des utilisateurs : " . $e->getMessage() . "</div>";
    }
    
    // 3. Récupérer un utilisateur spécifique
    echo "<h2>3. Récupérer un utilisateur spécifique</h2>";
    try {
        $userId = 1; // ID de l'utilisateur à récupérer
        $user = $client->getUser($token, $userId);
        echo "<pre>Détails de l'utilisateur (ID: {$userId}) : \n";
        print_r($user);
        echo "</pre>";
    } catch (SoapFault $e) {
        echo "<div style='color: red;'>Erreur lors de la récupération de l'utilisateur : " . $e->getMessage() . "</div>";
    }
    
    // 4. Créer un nouvel utilisateur
    echo "<h2>4. Créer un nouvel utilisateur</h2>";
    try {
        $newUser = [
            'username' => 'nouvel_utilisateur',
            'email' => 'nouvel.utilisateur@example.com',
            'password' => 'mot_de_passe_securise',
            'nom' => 'Utilisateur',
            'prenom' => 'Nouveau',
            'role' => 'visiteur'
        ];
        
        $newUserId = $client->createUser(
            $token,
            $newUser['username'],
            $newUser['email'],
            $newUser['password'],
            $newUser['nom'],
            $newUser['prenom'],
            $newUser['role']
        );
        
        echo "<div style='color: green;'>Nouvel utilisateur créé avec l'ID : {$newUserId}</div>";
    } catch (SoapFault $e) {
        echo "<div style='color: red;'>Erreur lors de la création de l'utilisateur : " . $e->getMessage() . "</div>";
    }
    
    // 5. Mettre à jour un utilisateur
    echo "<h2>5. Mettre à jour un utilisateur</h2>";
    try {
        $userToUpdate = [
            'id' => 2, // ID de l'utilisateur à mettre à jour
            'username' => 'utilisateur_modifie',
            'email' => 'utilisateur.modifie@example.com',
            'nom' => 'Utilisateur',
            'prenom' => 'Modifié',
            'role' => 'editeur',
            'password' => 'nouveau_mot_de_passe' // Optionnel
        ];
        
        $updateResult = $client->updateUser(
            $token,
            $userToUpdate['id'],
            $userToUpdate['username'],
            $userToUpdate['email'],
            $userToUpdate['nom'],
            $userToUpdate['prenom'],
            $userToUpdate['role'],
            $userToUpdate['password']
        );
        
        if ($updateResult) {
            echo "<div style='color: green;'>Utilisateur mis à jour avec succès</div>";
        } else {
            echo "<div style='color: orange;'>Aucune modification effectuée</div>";
        }
    } catch (SoapFault $e) {
        echo "<div style='color: red;'>Erreur lors de la mise à jour de l'utilisateur : " . $e->getMessage() . "</div>";
    }
    
    // 6. Supprimer un utilisateur
    echo "<h2>6. Supprimer un utilisateur</h2>";
    try {
        $userIdToDelete = 3; // ID de l'utilisateur à supprimer
        
        $deleteResult = $client->deleteUser($token, $userIdToDelete);
        
        if ($deleteResult) {
            echo "<div style='color: green;'>Utilisateur supprimé avec succès</div>";
        } else {
            echo "<div style='color: orange;'>Aucune suppression effectuée</div>";
        }
    } catch (SoapFault $e) {
        echo "<div style='color: red;'>Erreur lors de la suppression de l'utilisateur : " . $e->getMessage() . "</div>";
    }
    
    // Afficher les requêtes et réponses SOAP pour le débogage
    echo "<h2>Dernière requête SOAP</h2>";
    echo "<pre>" . htmlspecialchars($client->__getLastRequest()) . "</pre>";
    
    echo "<h2>Dernière réponse SOAP</h2>";
    echo "<pre>" . htmlspecialchars($client->__getLastResponse()) . "</pre>";
    
} catch (Exception $e) {
    echo "<div style='color: red;'>Erreur : " . $e->getMessage() . "</div>";
}
?> 