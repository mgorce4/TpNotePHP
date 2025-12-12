<?php

namespace App\Models;

use PDO;

class Comment {
    private $conn;

    public function __construct(){
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    /**
     * Créer un nouveau commentaire
     */
    public function creer($contenu, $utilisateur_id, $post_id){
        $query = "INSERT INTO commentaires (contenu, utilisateur_id, post_id) 
                  VALUES (:contenu, :utilisateur_id, :post_id)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':contenu', $contenu);
        $stmt->bindParam(':utilisateur_id', $utilisateur_id, PDO::PARAM_INT);
        $stmt->bindParam(':post_id', $post_id, PDO::PARAM_INT);
        $stmt->execute();
        
        return $this->conn->lastInsertId();
    }

    /**
     * Récupérer tous les commentaires d'un message
     */
    public function obtenirParMessage($post_id){
        $query = "SELECT c.*, u.nom as auteur_nom 
                  FROM commentaires c 
                  LEFT JOIN utilisateurs u ON c.utilisateur_id = u.utilisateur_id 
                  WHERE c.post_id = :post_id 
                  ORDER BY c.date_commentaire ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':post_id', $post_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Supprimer un commentaire
     */
    public function supprimer($id){
        $query = "DELETE FROM commentaires WHERE commentaire_id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    /**
     * Trouver un commentaire par ID
     */
    public function trouverParId($id){
        $query = "SELECT c.*, u.nom as auteur_nom 
                  FROM commentaires c 
                  LEFT JOIN utilisateurs u ON c.utilisateur_id = u.utilisateur_id 
                  WHERE c.commentaire_id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Modifier un commentaire
     */
    public function modifier($id, $contenu){
        $query = "UPDATE commentaires SET contenu = :contenu WHERE commentaire_id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':contenu', $contenu);
        return $stmt->execute();
    }
}
