<?php

// Démarrer la session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Import des contrôleurs
require_once(__DIR__ . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Controllers' . DIRECTORY_SEPARATOR . 'UserController.php');
require_once(__DIR__ . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Controllers' . DIRECTORY_SEPARATOR . 'PostController.php');
require_once(__DIR__ . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Controllers' . DIRECTORY_SEPARATOR . 'CommentController.php');

// Routage
$action = isset($_GET['action']) ? $_GET['action'] : 'connexion';

// Initialisation des contrôleurs
$userController = new UserController();
$postController = new PostController();
$commentController = new CommentController();

// Gérer les actions qui nécessitent des redirections AVANT d'inclure le header
if ($action === 'enregistrer' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $userController->enregistrer();
    exit;
}

if ($action === 'login' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $userController->login();
    exit;
}

if ($action === 'deconnexion') {
    $userController->deconnexion();
    exit;
}

if ($action === 'enregistrerMessage' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $postController->enregistrer();
    exit;
}

if ($action === 'supprimerMessage') {
    $postController->supprimer();
    exit;
}

if ($action === 'ajouterCommentaire' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $commentController->ajouter();
    exit;
}

if ($action === 'supprimerCommentaire') {
    $commentController->supprimer();
    exit;
}

if ($action === 'modifierProfil' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $userController->modifierProfil();
    exit;
}

if ($action === 'mettreAJour' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $postController->mettreAJour();
    exit;
}

// Vérifier l'authentification pour les pages protégées AVANT d'inclure le header
$actionsProtegees = ['messages', 'creer', 'detail', 'profil', 'modifier'];
if (in_array($action, $actionsProtegees) && !isset($_SESSION['user_id'])) {
    header('Location: ?action=connexion');
    exit;
}

// Header - inclus APRÈS les actions qui redirigent
require_once __DIR__ . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Views' . DIRECTORY_SEPARATOR . 'header.php';

switch ($action) {
    case 'connexion':
        $userController->connexion();
        break;

    case 'inscription':
        $userController->inscription();
        break;

    case 'profil':
        $userController->profil();
        break;

    case 'messages':
        $postController->index();
        break;

    case 'creer':
        $postController->creer();
        break;

    case 'detail':
        $postController->detail();
        break;

    case 'modifier':
        $postController->modifier();
        break;

    default:
        // Par défaut, rediriger vers la page de connexion
        if (isset($_SESSION['user_id'])) {
            $postController->index();
        } else {
            $userController->connexion();
        }
        break;
}

// Footer
require_once __DIR__ . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Views' . DIRECTORY_SEPARATOR . 'footer.php';
