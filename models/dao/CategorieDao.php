<?php
class CategorieDao {
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    public function getAll() {
        $query = $this->db->query("SELECT * FROM Categorie ORDER BY libelle");
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getById($id) {
        $query = $this->db->prepare("SELECT * FROM Categorie WHERE id = :id");
        $query->bindParam(":id", $id, PDO::PARAM_INT);
        $query->execute();
        return $query->fetch(PDO::FETCH_ASSOC);
    }
}
?>