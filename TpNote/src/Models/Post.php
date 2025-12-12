<?php

namespace App\Models;

use PDO;

class Post {
    private $conn;

    public function __construct(){
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    /**
     * Créer un nouveau message
     */
    public function creer($titre, $contenu, $utilisateur_id){
        $query = "INSERT INTO messages (titre, contenu, utilisateur_id) 
                  VALUES (:titre, :contenu, :utilisateur_id)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':titre', $titre);
        $stmt->bindParam(':contenu', $contenu);
        $stmt->bindParam(':utilisateur_id', $utilisateur_id, PDO::PARAM_INT);
        $stmt->execute();
        
        return $this->conn->lastInsertId();
    }

    /**
     * Récupérer tous les messages avec les informations de l'utilisateur
     */
    public function obtenirTous(){
        $query = "SELECT m.*, u.nom as auteur_nom 
                  FROM messages m 
                  LEFT JOIN utilisateurs u ON m.utilisateur_id = u.utilisateur_id 
                  ORDER BY m.date_publication DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupérer un message par ID
     */
    public function trouverParId($id){
        $query = "SELECT m.*, u.nom as auteur_nom, u.email as auteur_email
                  FROM messages m 
                  LEFT JOIN utilisateurs u ON m.utilisateur_id = u.utilisateur_id 
                  WHERE m.message_id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Modifier un message
     */
    public function modifier($id, $titre, $contenu){
        $query = "UPDATE messages SET titre = :titre, contenu = :contenu WHERE message_id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':titre', $titre);
        $stmt->bindParam(':contenu', $contenu);
        return $stmt->execute();
    }

    /**
     * Supprimer un message
     */
    public function supprimer($id){
        // D'abord supprimer les commentaires associés
        $query = "DELETE FROM commentaires WHERE post_id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        // Ensuite supprimer le message
        $query = "DELETE FROM messages WHERE message_id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    /**
     * Compter les commentaires d'un message
     */
    public function compterCommentaires($message_id){
        $query = "SELECT COUNT(*) as total FROM commentaires WHERE post_id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $message_id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }
    /**
     * Rechercher dans les messages
     */
    public function rechercher($terme){
        $terme = '%' . $terme . '%';
        $query = "SELECT m.*, u.nom as auteur_nom 
                  FROM messages m 
                  LEFT JOIN utilisateurs u ON m.utilisateur_id = u.utilisateur_id 
                  WHERE m.titre LIKE :terme OR m.contenu LIKE :terme
                  ORDER BY m.date_publication DESC
                  LIMIT 10";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':terme', $terme);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }}
