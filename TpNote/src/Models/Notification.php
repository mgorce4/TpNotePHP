<?php

require_once __DIR__ . '/Database.php';

class Notification {
    private $conn;

    public function __construct(){
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    /**
     * Créer une notification
     */
    public function creer($utilisateur_id, $type, $message, $lien = null){
        $query = "INSERT INTO notifications (utilisateur_id, type, message, lien) 
                  VALUES (:user_id, :type, :message, :lien)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $utilisateur_id, PDO::PARAM_INT);
        $stmt->bindParam(':type', $type);
        $stmt->bindParam(':message', $message);
        $stmt->bindParam(':lien', $lien);
        $stmt->execute();
        return $this->conn->lastInsertId();
    }

    /**
     * Obtenir les notifications non lues
     */
    public function obtenirNonLues($utilisateur_id){
        $query = "SELECT * FROM notifications 
                  WHERE utilisateur_id = :user_id AND lu = FALSE 
                  ORDER BY date_notification DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $utilisateur_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Compter les notifications non lues
     */
    public function compterNonLues($utilisateur_id){
        $query = "SELECT COUNT(*) as total FROM notifications 
                  WHERE utilisateur_id = :user_id AND lu = FALSE";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $utilisateur_id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }

    /**
     * Marquer comme lu
     */
    public function marquerCommeLu($notification_id){
        $query = "UPDATE notifications SET lu = TRUE WHERE notification_id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $notification_id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Marquer toutes comme lues
     */
    public function marquerToutesLues($utilisateur_id){
        $query = "UPDATE notifications SET lu = TRUE WHERE utilisateur_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $utilisateur_id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Obtenir toutes les notifications (avec pagination)
     */
    public function obtenirTout($utilisateur_id, $limit = 20){
        $query = "SELECT * FROM notifications 
                  WHERE utilisateur_id = :user_id 
                  ORDER BY date_notification DESC 
                  LIMIT :limit";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $utilisateur_id, PDO::PARAM_INT);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
