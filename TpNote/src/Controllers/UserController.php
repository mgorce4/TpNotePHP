<?php

namespace App\Controllers;

use App\Models\User;

class UserController {
    private $userModel;

    public function __construct(){
        $this->userModel = new User();
    }

    /**
     * Afficher la page d'inscription
     */
    public function inscription(){
        $erreur = $_SESSION['erreur_inscription'] ?? null;
        unset($_SESSION['erreur_inscription']);
        require_once(__DIR__ . '/../Views/header.php');
        require_once(__DIR__ . '/../Views/User/inscription.php');
        require_once(__DIR__ . '/../Views/footer.php');
    }

    /**
     * Traiter l'inscription
     */
    public function enregistrer(){
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nom = $_POST['nom'] ?? '';
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            $confirm_password = $_POST['confirm_password'] ?? '';

            // Validation
            if (empty($nom) || empty($email) || empty($password)) {
                $_SESSION['erreur_inscription'] = 'Tous les champs sont obligatoires.';
                header('Location: ?action=inscription');
                exit;
            }

            if ($password !== $confirm_password) {
                $_SESSION['erreur_inscription'] = 'Les mots de passe ne correspondent pas.';
                header('Location: ?action=inscription');
                exit;
            }

            // Créer l'utilisateur
            $resultat = $this->userModel->creer($nom, $email, $password);

            if ($resultat) {
                header('Location: ?action=connexion&success=1');
                exit;
            } else {
                $_SESSION['erreur_inscription'] = 'Cet email est déjà utilisé.';
                header('Location: ?action=inscription');
                exit;
            }
        }
    }

    /**
     * Afficher la page de connexion
     */
    public function connexion(){
        if (isset($_SESSION['user_id'])) {
            header('Location: ?action=messages');
            exit;
        }
        $erreur = $_SESSION['erreur_connexion'] ?? null;
        unset($_SESSION['erreur_connexion']);
        require_once(__DIR__ . '/../Views/header.php');
        require_once(__DIR__ . '/../Views/User/connexion.php');
        require_once(__DIR__ . '/../Views/footer.php');
    }

    /**
     * Traiter la connexion
     */
    public function login(){
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';

            if (empty($email) || empty($password)) {
                $_SESSION['erreur_connexion'] = 'Tous les champs sont obligatoires.';
                header('Location: ?action=connexion');
                exit;
            }

            $user = $this->userModel->verifierConnexion($email, $password);

            if ($user) {
                $_SESSION['user_id'] = $user['utilisateur_id'];
                $_SESSION['user_nom'] = $user['nom'];
                $_SESSION['user_email'] = $user['email'];
                header('Location: ?action=messages');
                exit;
            } else {
                $_SESSION['erreur_connexion'] = 'Email ou mot de passe incorrect.';
                header('Location: ?action=connexion');
                exit;
            }
        }
    }

    /**
     * Déconnexion
     */
    public function deconnexion(){
        session_destroy();
        header('Location: ?action=connexion');
        exit;
    }

    /**
     * Afficher le profil (bonus)
     */
    public function profil(){
        if (!isset($_SESSION['user_id'])) {
            header('Location: ?action=connexion');
            exit;
        }

        $user = $this->userModel->trouverParId($_SESSION['user_id']);
        require_once(__DIR__ . '/../Views/User/profil.php');
    }

    /**
     * Modifier le profil (bonus)
     */
    public function modifierProfil(){
        if (!isset($_SESSION['user_id'])) {
            header('Location: ?action=connexion');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nom = $_POST['nom'] ?? '';
            $email = $_POST['email'] ?? '';

            if (!empty($nom) && !empty($email)) {
                $resultat = $this->userModel->modifier($_SESSION['user_id'], $nom, $email);

                if ($resultat) {
                    $_SESSION['user_nom'] = $nom;
                    $_SESSION['user_email'] = $email;
                    header('Location: ?action=profil&success=1');
                    exit;
                }
            }
        }
    }
}
