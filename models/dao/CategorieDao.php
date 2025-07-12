<?php
require_once __DIR__ . '/../../models/domaine/Category.php';

class CategorieDao {
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    public function getAll() {
        $query = $this->db->query("SELECT * FROM Categorie WHERE actif = 1 ORDER BY libelle");
        $categoriesData = $query->fetchAll(PDO::FETCH_ASSOC);
        $categories = [];
        
        foreach ($categoriesData as $categoryData) {
            $categories[] = Category::fromArray($categoryData);
        }
        
        return $categories;
    }
    
    public function getById($id) {
        $query = $this->db->prepare("SELECT * FROM Categorie WHERE id = :id");
        $query->bindParam(":id", $id, PDO::PARAM_INT);
        $query->execute();
        $categoryData = $query->fetch(PDO::FETCH_ASSOC);
        
        if (!$categoryData) {
            return null;
        }
        
        return Category::fromArray($categoryData);
    }
    
    public function create($libelle, $slug, $description, $created_by) {
        $query = "INSERT INTO Categorie (libelle, slug, description, created_by) 
                  VALUES (:libelle, :slug, :description, :created_by)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':libelle', $libelle, PDO::PARAM_STR);
        $stmt->bindParam(':slug', $slug, PDO::PARAM_STR);
        $stmt->bindParam(':description', $description, PDO::PARAM_STR);
        $stmt->bindParam(':created_by', $created_by, PDO::PARAM_INT);
        return $stmt->execute();
    }
    
    public function update($id, $libelle, $slug, $description) {
        $query = "UPDATE Categorie 
                  SET libelle = :libelle, 
                      slug = :slug, 
                      description = :description,
                      dateModification = CURRENT_TIMESTAMP
                  WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':libelle', $libelle, PDO::PARAM_STR);
        $stmt->bindParam(':slug', $slug, PDO::PARAM_STR);
        $stmt->bindParam(':description', $description, PDO::PARAM_STR);
        return $stmt->execute();
    }
    
    public function delete($id) {
        // Vérifier si des articles sont associés à cette catégorie
        $query = "SELECT COUNT(*) FROM Article WHERE categorie = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $count = (int)$stmt->fetchColumn();
        
        if ($count > 0) {
            // Il y a des articles associés, on désactive la catégorie au lieu de la supprimer
            $query = "UPDATE Categorie SET actif = 0 WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } else {
            // Pas d'articles associés, on peut supprimer la catégorie
            $query = "DELETE FROM Categorie WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        }
    }
}
?>