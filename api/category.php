<?php
/**
 * API REST - Articles d'une catégorie spécifique
 * Permet de récupérer les articles d'une catégorie au format XML ou JSON
 * Paramètre: category (ID de la catégorie)
 * Exemple: /api/category.php?category=1&format=xml
 */

// Activer la mise en tampon de sortie pour contrôler ce qui est envoyé au navigateur
ob_start();

// Rediriger vers le fichier principal avec le bon endpoint
require_once 'articles.php'; 