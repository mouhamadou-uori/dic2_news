# Projet de Site d'Actualités avec Services Web

## 1. Introduction

Ce projet consiste en la conception et la réalisation d'un site web d'actualités dynamique. L'application offre non seulement une interface de consultation pour les visiteurs, mais également un système de gestion de contenu complet pour les éditeurs et une administration des utilisateurs pour les administrateurs. 

L'architecture a été pensée pour être modulaire et évolutive, notamment grâce à l'exposition de services web (SOAP et REST) qui permettent à des applications tierces d'interagir avec les fonctionnalités clés du système.

## 2. Fonctionnalités Principales

Le site s'articule autour de trois profils utilisateurs avec des droits d'accès distincts.

### Pour tous les utilisateurs (Visiteurs)
- **Consultation des articles** : La page d'accueil affiche les derniers articles publiés avec un extrait.
- **Pagination** : Un système de navigation permet de parcourir l'ensemble des articles.
- **Filtrage par catégorie** : Les articles peuvent être consultés par catégorie pour affiner la recherche.
- **Lecture détaillée** : Chaque article dispose d'une page dédiée pour un affichage complet.

### Pour les Éditeurs (après authentification)
- **Gestion des articles** : CRUD complet (Créer, Lire, Mettre à jour, Supprimer) sur les articles.
- **Gestion des catégories** : CRUD complet sur les catégories d'articles.

### Pour les Administrateurs
- **Droits des Éditeurs** : Les administrateurs héritent de toutes les fonctionnalités des éditeurs.
- **Gestion des utilisateurs** : CRUD complet sur les comptes utilisateurs (ajout, modification, suppression).
- **Gestion des accès API** : Génération et révocation des jetons d'authentification pour sécuriser l'accès aux services web.

## 3. Architecture Technique

Le projet est développé en **PHP** sans framework majeur (PHP "vanilla"), en s'appuyant sur une architecture modulaire et une approche orientée objet pour la gestion des données (DAO, Domain).

- **Serveur** : WAMP (Windows, Apache, MySQL, PHP).
- **Base de données** : MySQL, avec des interactions gérées via l'objet **PDO** de PHP pour la sécurité et la portabilité.
- **Dépendances** : La bibliothèque `laminas/laminas-soap` est utilisée via Composer pour la génération dynamique et la gestion du WSDL du service SOAP.

### Structure du Projet

Le code est organisé de manière logique pour séparer les préoccupations :

- `/api` : Contient les points d'entrée des services web (SOAP et REST).
- `/config` : Fichiers de configuration, notamment pour la connexion à la base de données.
- `/models` : Cœur de la logique métier.
  - `/dao` : Data Access Objects, classes responsables des interactions avec la base de données.
  - `/domaine` : Objets du domaine, représentant les entités comme `User`, `Article`, etc.
- `/vendor` : Dépendances gérées par Composer.
- `/views` ou `/templates` : Fichiers HTML/PHP pour l'affichage des pages (non inclus dans notre périmètre de travail actuel).
- `/assets` : Fichiers CSS, JavaScript et images.

## 4. Documentation des Services Web

Les services web sont le moyen d'interagir avec l'application de manière programmatique.

### Service Web SOAP

Ce service est dédié à la gestion des utilisateurs et à l'authentification. Il est sécurisé par un jeton.

- **URL du WSDL** : `http://localhost/dic2_news/api/soap_users.php?wsdl`
- **Opérations disponibles** :
  - `authenticate(string $username, string $password)`: Authentifie un utilisateur et retourne ses informations.
  - `listUsers(string $token)`: Retourne la liste de tous les utilisateurs.
  - `getUser(string $token, int $id)`: Récupère un utilisateur par son ID.
  - `createUser(string $token, ...)`: Crée un nouvel utilisateur.
  - `updateUser(string $token, int $id, ...)`: Met à jour un utilisateur existant.
  - `deleteUser(string $token, int $id)`: Supprime un utilisateur.

### Service Web REST

Ce service permet de consulter les données relatives aux articles. Le format de réponse (JSON ou XML) peut être spécifié dans l'en-tête `Accept` de la requête HTTP (`application/json` ou `application/xml`).

- **Points d'accès (Endpoints)** :
  - `GET /api/articles`: Récupère la liste de tous les articles.
  - `GET /api/articles/categories`: Récupère les articles regroupés par catégorie.
  - `GET /api/articles/category/{id}`: Récupère les articles d'une catégorie spécifique.

## 5. Guide d'Installation

1.  **Prérequis** : Un environnement de développement web comme WAMP, LAMP ou MAMP.
2.  **Clonage** : Clonez ou téléchargez les fichiers du projet dans le répertoire racine de votre serveur (ex: `c:\wamp64\www\`).
3.  **Base de données** : Importez le fichier `.sql` (à fournir) dans votre base de données via un outil comme phpMyAdmin.
4.  **Configuration** : Modifiez le fichier `config/database.php` avec vos identifiants de base de données.
5.  **Dépendances** : Exécutez `composer install` à la racine du projet pour télécharger les bibliothèques nécessaires (comme `laminas-soap`).
6.  **Test** : Lancez votre serveur et accédez à `http://localhost/dic2_news/`.

## 6. Application Client (Java/Python)

Une application cliente (non fournie dans ce dépôt) peut être développée pour illustrer l'utilisation des services web. Son fonctionnement serait le suivant :

1.  L'application demande un login et un mot de passe.
2.  Elle appelle le service SOAP `authenticate`.
3.  Si l'authentification réussit et que l'utilisateur est administrateur, l'application obtient un jeton d'accès.
4.  Elle utilise ensuite ce jeton pour appeler les autres opérations du service SOAP (lister, créer, etc.) et offrir une interface de gestion des utilisateurs.

Ce projet constitue une base solide pour un système de gestion de contenu moderne, découplant l'interface utilisateur de la logique métier grâce à une API de services web bien définie.
