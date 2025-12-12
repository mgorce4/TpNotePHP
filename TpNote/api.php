<?php

// Gestion des erreurs pour retourner du JSON
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Capturer toutes les erreurs et les convertir en JSON
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erreur serveur: ' . $errstr,
        'file' => basename($errfile),
        'line' => $errline
    ]);
    exit;
});

session_start();

// Autoloader Composer
require_once __DIR__ . '/vendor/autoload.php';

use App\Models\Reaction;
use App\Models\Vote;
use App\Models\Notification;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;

header('Content-Type: application/json');

class ApiController {
    
    /**
     * Gérer les réactions
     */
    public function toggleReaction(){
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Non connecté']);
            return;
        }

        $message_id = $_POST['message_id'] ?? null;
        
        if (!$message_id) {
            echo json_encode(['success' => false, 'message' => 'Message ID manquant']);
            return;
        }

        $reaction = new Reaction();
        $ajoutee = $reaction->toggle($_SESSION['user_id'], $message_id, 'like');
        $total = $reaction->compter($message_id);
        
        // Créer une notification pour l'auteur du message
        if ($ajoutee) {
            $post = new Post();
            $message = $post->trouverParId($message_id);
            
            if ($message && $message['utilisateur_id'] != $_SESSION['user_id']) {
                $notification = new Notification();
                $notification->creer(
                    $message['utilisateur_id'],
                    'reaction',
                    $_SESSION['user_nom'] . ' a aimé votre publication',
                    '?action=detail&id=' . $message_id
                );
            }
        }
        
        echo json_encode([
            'success' => true,
            'liked' => $ajoutee,
            'count' => $total
        ]);
    }

    /**
     * Gérer les votes
     */
    public function vote(){
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Non connecté']);
            return;
        }

        $commentaire_id = $_POST['commentaire_id'] ?? null;
        $type = $_POST['type'] ?? null;
        
        if (!$commentaire_id || !in_array($type, ['up', 'down'])) {
            echo json_encode(['success' => false, 'message' => 'Données invalides']);
            return;
        }

        $vote = new Vote();
        $vote->voter($_SESSION['user_id'], $commentaire_id, $type);
        $score = $vote->calculerScore($commentaire_id);
        $userVote = $vote->obtenirVote($_SESSION['user_id'], $commentaire_id);
        
        // Créer une notification pour l'auteur du commentaire
        $comment = new Comment();
        $commentData = $comment->trouverParId($commentaire_id);
        
        if ($commentData && $commentData['utilisateur_id'] != $_SESSION['user_id']) {
            $notification = new Notification();
            $message = $type === 'up' ? 'a voté pour' : 'a voté contre';
            $notification->creer(
                $commentData['utilisateur_id'],
                'vote',
                $_SESSION['user_nom'] . ' ' . $message . ' votre commentaire',
                '?action=detail&id=' . $commentData['post_id']
            );
        }
        
        echo json_encode([
            'success' => true,
            'score' => $score,
            'userVote' => $userVote
        ]);
    }

    /**
     * Ajouter un commentaire via Ajax
     */
    public function ajouterCommentaire(){
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Non connecté']);
            return;
        }

        $post_id = $_POST['post_id'] ?? null;
        $contenu = trim($_POST['contenu'] ?? '');
        
        if (!$post_id || empty($contenu)) {
            echo json_encode(['success' => false, 'message' => 'Données invalides']);
            return;
        }

        $comment = new Comment();
        $commentaire_id = $comment->creer($contenu, $_SESSION['user_id'], $post_id);
        
        if ($commentaire_id) {
            // Créer une notification pour l'auteur du message
            $post = new Post();
            $message = $post->trouverParId($post_id);
            
            if ($message && $message['utilisateur_id'] != $_SESSION['user_id']) {
                $notification = new Notification();
                $notification->creer(
                    $message['utilisateur_id'],
                    'commentaire',
                    $_SESSION['user_nom'] . ' a commenté votre publication',
                    '?action=detail&id=' . $post_id
                );
            }
            
            echo json_encode([
                'success' => true,
                'commentaire' => [
                    'commentaire_id' => $commentaire_id,
                    'contenu' => htmlspecialchars($contenu),
                    'nom' => $_SESSION['user_nom'],
                    'date_commentaire' => date('Y-m-d H:i:s'),
                    'utilisateur_id' => $_SESSION['user_id']
                ]
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'ajout']);
        }
    }

    /**
     * Recherche en temps réel
     */
    public function recherche(){
        $q = trim($_GET['q'] ?? '');
        
        if (empty($q)) {
            echo json_encode(['success' => false, 'results' => []]);
            return;
        }

        $post = new Post();
        $user = new User();
        $comment = new Comment();
        
        // Rechercher dans les messages
        $messages = $post->rechercher($q);
        
        // Rechercher dans les utilisateurs
        $utilisateurs = $user->rechercher($q);
        
        echo json_encode([
            'success' => true,
            'results' => [
                'messages' => $messages,
                'utilisateurs' => $utilisateurs
            ]
        ]);
    }

    /**
     * Obtenir les notifications
     */
    public function obtenirNotifications(){
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Non connecté']);
            return;
        }

        $notification = new Notification();
        $notifications = $notification->obtenirNonLues($_SESSION['user_id']);
        $count = $notification->compterNonLues($_SESSION['user_id']);
        
        echo json_encode([
            'success' => true,
            'notifications' => $notifications,
            'count' => $count
        ]);
    }

    /**
     * Marquer les notifications comme lues
     */
    public function marquerNotificationsLues(){
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Non connecté']);
            return;
        }

        $notification = new Notification();
        
        if (isset($_POST['notification_id'])) {
            $notification->marquerCommeLu($_POST['notification_id']);
        } else {
            $notification->marquerToutesLues($_SESSION['user_id']);
        }
        
        echo json_encode(['success' => true]);
    }

    /**
     * Modifier un commentaire (édition inline)
     */
    public function modifierCommentaire(){
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Non connecté']);
            return;
        }

        $commentaire_id = $_POST['commentaire_id'] ?? null;
        $contenu = trim($_POST['contenu'] ?? '');
        
        if (!$commentaire_id || empty($contenu)) {
            echo json_encode(['success' => false, 'message' => 'Données invalides']);
            return;
        }

        $comment = new Comment();
        $commentData = $comment->trouverParId($commentaire_id);
        
        // Vérifier que l'utilisateur est l'auteur
        if ($commentData && $commentData['utilisateur_id'] == $_SESSION['user_id']) {
            $comment->modifier($commentaire_id, $contenu);
            echo json_encode([
                'success' => true,
                'contenu' => htmlspecialchars($contenu)
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Non autorisé']);
        }
    }

    /**
     * Modifier un message (édition inline)
     */
    public function modifierMessage(){
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Non connecté']);
            return;
        }

        $message_id = $_POST['message_id'] ?? null;
        $titre = trim($_POST['titre'] ?? '');
        $contenu = trim($_POST['contenu'] ?? '');
        
        if (!$message_id || empty($titre) || empty($contenu)) {
            echo json_encode(['success' => false, 'message' => 'Données invalides']);
            return;
        }

        $post = new Post();
        $messageData = $post->trouverParId($message_id);
        
        // Vérifier que l'utilisateur est l'auteur
        if ($messageData && $messageData['utilisateur_id'] == $_SESSION['user_id']) {
            $post->modifier($message_id, $titre, $contenu);
            echo json_encode([
                'success' => true,
                'titre' => htmlspecialchars($titre),
                'contenu' => htmlspecialchars($contenu)
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Non autorisé']);
        }
    }
}

// Routeur simple pour l'API
try {
    $controller = new ApiController();
    $action = $_GET['action'] ?? '';

    switch ($action) {
        case 'reaction':
            $controller->toggleReaction();
            break;
        case 'vote':
            $controller->vote();
            break;
        case 'ajouterCommentaire':
            $controller->ajouterCommentaire();
            break;
        case 'recherche':
            $controller->recherche();
            break;
        case 'notifications':
            $controller->obtenirNotifications();
            break;
        case 'marquerLu':
            $controller->marquerNotificationsLues();
            break;
        case 'modifierCommentaire':
            $controller->modifierCommentaire();
            break;
        case 'modifierMessage':
            $controller->modifierMessage();
            break;
        default:
            echo json_encode(['success' => false, 'message' => 'Action inconnue']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erreur: ' . $e->getMessage(),
        'file' => basename($e->getFile()),
        'line' => $e->getLine()
    ]);
}
