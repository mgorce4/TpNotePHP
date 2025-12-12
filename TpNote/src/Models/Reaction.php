<?php

namespace App\Models;

use PDO;

class Reaction {
    private $conn;

    public function __construct(){
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    /**
     * Ajouter ou retirer une réaction
     */
    public function toggle($utilisateur_id, $message_id, $type = 'like'){
        // Vérifier si la réaction existe déjà
        $query = "SELECT * FROM reactions WHERE utilisateur_id = :user_id AND message_id = :message_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $utilisateur_id, PDO::PARAM_INT);
        $stmt->bindParam(':message_id', $message_id, PDO::PARAM_INT);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            // Supprimer la réaction
            $query = "DELETE FROM reactions WHERE utilisateur_id = :user_id AND message_id = :message_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':user_id', $utilisateur_id, PDO::PARAM_INT);
            $stmt->bindParam(':message_id', $message_id, PDO::PARAM_INT);
            $stmt->execute();
            return false; // Réaction retirée
        } else {
            // Ajouter la réaction
            $query = "INSERT INTO reactions (type, utilisateur_id, message_id) VALUES (:type, :user_id, :message_id)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':type', $type);
            $stmt->bindParam(':user_id', $utilisateur_id, PDO::PARAM_INT);
            $stmt->bindParam(':message_id', $message_id, PDO::PARAM_INT);
            $stmt->execute();
            return true; // Réaction ajoutée
        }
    }

    /**
     * Compter les réactions d'un message
     */
    public function compter($message_id){
        $query = "SELECT COUNT(*) as total FROM reactions WHERE message_id = :message_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':message_id', $message_id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }

    /**
     * Vérifier si l'utilisateur a déjà réagi
     */
    public function aReagi($utilisateur_id, $message_id){
        $query = "SELECT * FROM reactions WHERE utilisateur_id = :user_id AND message_id = :message_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $utilisateur_id, PDO::PARAM_INT);
        $stmt->bindParam(':message_id', $message_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }
}
