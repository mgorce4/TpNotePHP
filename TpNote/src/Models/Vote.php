<?php

require_once __DIR__ . '/Database.php';

class Vote {
    private $conn;

    public function __construct(){
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    /**
     * Voter (upvote ou downvote)
     */
    public function voter($utilisateur_id, $commentaire_id, $type){
        // Vérifier si l'utilisateur a déjà voté
        $query = "SELECT * FROM votes WHERE utilisateur_id = :user_id AND commentaire_id = :comment_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $utilisateur_id, PDO::PARAM_INT);
        $stmt->bindParam(':comment_id', $commentaire_id, PDO::PARAM_INT);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $vote = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Si même type, retirer le vote
            if ($vote['type'] === $type) {
                $query = "DELETE FROM votes WHERE utilisateur_id = :user_id AND commentaire_id = :comment_id";
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(':user_id', $utilisateur_id, PDO::PARAM_INT);
                $stmt->bindParam(':comment_id', $commentaire_id, PDO::PARAM_INT);
                $stmt->execute();
            } else {
                // Sinon, changer le type de vote
                $query = "UPDATE votes SET type = :type WHERE utilisateur_id = :user_id AND commentaire_id = :comment_id";
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(':type', $type);
                $stmt->bindParam(':user_id', $utilisateur_id, PDO::PARAM_INT);
                $stmt->bindParam(':comment_id', $commentaire_id, PDO::PARAM_INT);
                $stmt->execute();
            }
        } else {
            // Ajouter le vote
            $query = "INSERT INTO votes (type, utilisateur_id, commentaire_id) VALUES (:type, :user_id, :comment_id)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':type', $type);
            $stmt->bindParam(':user_id', $utilisateur_id, PDO::PARAM_INT);
            $stmt->bindParam(':comment_id', $commentaire_id, PDO::PARAM_INT);
            $stmt->execute();
        }
    }

    /**
     * Calculer le score d'un commentaire
     */
    public function calculerScore($commentaire_id){
        $query = "SELECT 
                    SUM(CASE WHEN type = 'up' THEN 1 ELSE 0 END) as upvotes,
                    SUM(CASE WHEN type = 'down' THEN 1 ELSE 0 END) as downvotes
                  FROM votes WHERE commentaire_id = :comment_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':comment_id', $commentaire_id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $upvotes = $result['upvotes'] ?? 0;
        $downvotes = $result['downvotes'] ?? 0;
        
        return [
            'score' => $upvotes - $downvotes,
            'upvotes' => $upvotes,
            'downvotes' => $downvotes
        ];
    }

    /**
     * Obtenir le vote de l'utilisateur
     */
    public function obtenirVote($utilisateur_id, $commentaire_id){
        $query = "SELECT type FROM votes WHERE utilisateur_id = :user_id AND commentaire_id = :comment_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $utilisateur_id, PDO::PARAM_INT);
        $stmt->bindParam(':comment_id', $commentaire_id, PDO::PARAM_INT);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['type'];
        }
        return null;
    }
}
