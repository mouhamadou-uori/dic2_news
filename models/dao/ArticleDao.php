<?php
class ArticleDao {
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    public function getAll() {
        $query = "SELECT a.*, c.libelle as categorie_nom 
                  FROM Article a 
                  LEFT JOIN Categorie c ON a.categorie = c.id 
                  WHERE a.statut = 'publie'
                  ORDER BY a.dateCreation DESC";
        $stmt = $this->db->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getAllForAdmin($limit = 10, $offset = 0) {
        $query = "SELECT a.*, c.libelle as categorie_nom, u.username as auteur_nom 
                  FROM Article a 
                  LEFT JOIN Categorie c ON a.categorie = c.id 
                  LEFT JOIN User u ON a.auteur_id = u.id 
                  ORDER BY a.dateCreation DESC
                  LIMIT :limit OFFSET :offset";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getById($id) {
        $query = "SELECT a.*, c.libelle as categorie_nom, u.username as auteur_nom 
                  FROM Article a 
                  LEFT JOIN Categorie c ON a.categorie = c.id 
                  LEFT JOIN User u ON a.auteur_id = u.id 
                  WHERE a.id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function getByCategory($categoryId) {
        $query = "SELECT a.*, c.libelle as categorie_nom 
                  FROM Article a 
                  LEFT JOIN Categorie c ON a.categorie = c.id 
                  WHERE a.categorie = :categoryId AND a.statut = 'publie'
                  ORDER BY a.dateCreation DESC";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':categoryId', $categoryId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getAllPaginated($limit, $offset, $categoryId = null) {
        $params = [];
        
        if ($categoryId) {
            $query = "SELECT a.*, c.libelle as categorie_nom 
                      FROM Article a 
                      LEFT JOIN Categorie c ON a.categorie = c.id 
                      WHERE a.categorie = :categoryId AND a.statut = 'publie'
                      ORDER BY a.dateCreation DESC
                      LIMIT :limit OFFSET :offset";
            $params[':categoryId'] = $categoryId;
        } else {
            $query = "SELECT a.*, c.libelle as categorie_nom 
                      FROM Article a 
                      LEFT JOIN Categorie c ON a.categorie = c.id 
                      WHERE a.statut = 'publie'
                      ORDER BY a.dateCreation DESC
                      LIMIT :limit OFFSET :offset";
        }
        
        $stmt = $this->db->prepare($query);
        
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value, PDO::PARAM_INT);
        }
        
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getTotalCount($categoryId = null) {
        if ($categoryId) {
            $query = "SELECT COUNT(*) FROM Article WHERE categorie = :categoryId AND statut = 'publie'";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':categoryId', $categoryId, PDO::PARAM_INT);
        } else {
            $query = "SELECT COUNT(*) FROM Article WHERE statut = 'publie'";
            $stmt = $this->db->prepare($query);
        }
        
        $stmt->execute();
        return (int)$stmt->fetchColumn();
    }
    
    public function create($titre, $slug, $contenu, $categorie, $auteur_id, $statut = 'brouillon') {
        $query = "INSERT INTO Article (titre, slug, contenu, categorie, auteur_id, statut) 
                  VALUES (:titre, :slug, :contenu, :categorie, :auteur_id, :statut)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':titre', $titre, PDO::PARAM_STR);
        $stmt->bindParam(':slug', $slug, PDO::PARAM_STR);
        $stmt->bindParam(':contenu', $contenu, PDO::PARAM_STR);
        $stmt->bindParam(':categorie', $categorie, PDO::PARAM_INT);
        $stmt->bindParam(':auteur_id', $auteur_id, PDO::PARAM_INT);
        $stmt->bindParam(':statut', $statut, PDO::PARAM_STR);
        return $stmt->execute();
    }
    
    public function update($id, $titre, $slug, $contenu, $categorie, $statut) {
        $query = "UPDATE Article 
                  SET titre = :titre, 
                      slug = :slug, 
                      contenu = :contenu, 
                      categorie = :categorie, 
                      statut = :statut,
                      dateModification = CURRENT_TIMESTAMP
                  WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':titre', $titre, PDO::PARAM_STR);
        $stmt->bindParam(':slug', $slug, PDO::PARAM_STR);
        $stmt->bindParam(':contenu', $contenu, PDO::PARAM_STR);
        $stmt->bindParam(':categorie', $categorie, PDO::PARAM_INT);
        $stmt->bindParam(':statut', $statut, PDO::PARAM_STR);
        return $stmt->execute();
    }
    
    public function delete($id) {
        $query = "DELETE FROM Article WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
?>