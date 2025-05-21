<?php
class ArticleDao {
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    public function getAll() {
        $query = $this->db->query("
            SELECT a.*, c.libelle as categorie_nom 
            FROM Article a 
            JOIN Categorie c ON a.categorie = c.id 
            ORDER BY a.dateCreation DESC
        ");
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getById($id) {
        $query = $this->db->prepare("
            SELECT a.*, c.libelle as categorie_nom 
            FROM Article a 
            JOIN Categorie c ON a.categorie = c.id 
            WHERE a.id = :id
        ");
        $query->bindParam(":id", $id, PDO::PARAM_INT);
        $query->execute();
        return $query->fetch(PDO::FETCH_ASSOC);
    }
    
    public function getByCategory($categoryId) {
        $query = $this->db->prepare("
            SELECT a.*, c.libelle as categorie_nom 
            FROM Article a 
            JOIN Categorie c ON a.categorie = c.id 
            WHERE a.categorie = :categoryId 
            ORDER BY a.dateCreation DESC
        ");
        $query->bindParam(":categoryId", $categoryId, PDO::PARAM_INT);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>