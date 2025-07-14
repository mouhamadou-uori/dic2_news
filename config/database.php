<?php
define("DB_HOST", "localhost");
define("DB_NAME", "dic2_news");
// define("DB_NAME", "mglsi_news");
define("DB_USER", "wally");
define("DB_PASS", "passer");

function getDbConnection() {
    try {
        $connexion = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
        $connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $connexion;
    } catch (PDOException $e) {
        die("Erreur de connexion : " . $e->getMessage());
    }
}
