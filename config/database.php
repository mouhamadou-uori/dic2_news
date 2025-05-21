<?php
define("DB_HOST", "localhost");
define("DB_NAME", "dic_news");
define("DB_USER", "mglsi_user");
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
?>
