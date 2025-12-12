<?php

namespace App\Models;

use PDO;

class User {
    private $conn;

    public function __construct(){
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    /**
     * Créer un nouvel utilisateur (inscription)
     */
    public function creer($nom, $email, $password){
        // Vérifier si l'email existe déjà
        if ($this->trouverParEmail($email)) {
            return false; // Email déjà utilisé
        }

        $query = "INSERT INTO utilisateurs (nom, email, password) 
                  VALUES (:nom, :email, :password)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':email', $email);
        // Hash du mot de passe pour la sécurité
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt->bindParam(':password', $password_hash);
        $stmt->execute();
        
        return $this->conn->lastInsertId();
    }

    /**
     * Trouver un utilisateur par email
     */
    public function trouverParEmail($email){
        $query = "SELECT * FROM utilisateurs WHERE email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Récupérer tous les utilisateurs
     */
    public function obtenirTous(){
        $query = "SELECT utilisateur_id, nom, email, date_inscription 
                  FROM utilisateurs 
                  ORDER BY date_inscription DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupérer un utilisateur par ID
     */
    public function trouverParId($id){
        $query = "SELECT utilisateur_id, nom, email, date_inscription 
                  FROM utilisateurs 
                  WHERE utilisateur_id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Vérifier les identifiants de connexion
     */
    public function verifierConnexion($email, $password){
        $query = "SELECT * FROM utilisateurs WHERE email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return false;
    }

    /**
     * Modifier les informations d'un utilisateur
     */
    public function modifier($id, $nom, $email){
        $query = "UPDATE utilisateurs SET nom = :nom, email = :email WHERE utilisateur_id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':email', $email);
        return $stmt->execute();
    }
    /**
     * Rechercher des utilisateurs
     */
    public function rechercher($terme){
        $terme = '%' . $terme . '%';
        $query = "SELECT utilisateur_id, nom, email 
                  FROM utilisateurs 
                  WHERE nom LIKE :terme OR email LIKE :terme
                  LIMIT 10";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':terme', $terme);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }}
