<?php
/**
 * API REST - Articles regroupés par catégories
 * Permet de récupérer les articles regroupés par catégories au format XML ou JSON
 */

// Activer la mise en tampon de sortie pour contrôler ce qui est envoyé au navigateur
ob_start();

// Rediriger vers le fichier principal avec le bon endpoint
require_once 'articles.php'; 