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
    
    public function getAllPaginated($limit, $offset, $categoryId = null) {
        if ($categoryId) {
            $query = $this->db->prepare("
                SELECT a.*, c.libelle as categorie_nom 
                FROM Article a 
                JOIN Categorie c ON a.categorie = c.id 
                WHERE a.categorie = :categoryId 
                ORDER BY a.dateCreation DESC
                LIMIT :limit OFFSET :offset
            ");
            $query->bindParam(":categoryId", $categoryId, PDO::PARAM_INT);
        } else {
            $query = $this->db->prepare("
                SELECT a.*, c.libelle as categorie_nom 
                FROM Article a 
                JOIN Categorie c ON a.categorie = c.id 
                ORDER BY a.dateCreation DESC
                LIMIT :limit OFFSET :offset
            ");
        }
        
        $query->bindParam(":limit", $limit, PDO::PARAM_INT);
        $query->bindParam(":offset", $offset, PDO::PARAM_INT);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getTotalCount($categoryId = null) {
        if ($categoryId) {
            $query = $this->db->prepare("
                SELECT COUNT(*) as total 
                FROM Article a 
                WHERE a.categorie = :categoryId
            ");
            $query->bindParam(":categoryId", $categoryId, PDO::PARAM_INT);
        } else {
            $query = $this->db->query("SELECT COUNT(*) as total FROM Article");
        }
        
        $query->execute();
        $result = $query->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }
}
?>