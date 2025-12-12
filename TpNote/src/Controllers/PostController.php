<?php

require_once(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Models' . DIRECTORY_SEPARATOR . 'Post.php');
require_once(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Models' . DIRECTORY_SEPARATOR . 'Comment.php');
require_once(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Models' . DIRECTORY_SEPARATOR . 'Reaction.php');
require_once(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Models' . DIRECTORY_SEPARATOR . 'Vote.php');

class PostController {
    private $postModel;
    private $commentModel;
    private $reactionModel;
    private $voteModel;

    public function __construct(){
        $this->postModel = new Post();
        $this->commentModel = new Comment();
        $this->reactionModel = new Reaction();
        $this->voteModel = new Vote();
    }

    /**
     * Afficher tous les messages
     */
    public function index(){
        $messages = $this->postModel->obtenirTous();
        
        // Compter les commentaires et réactions pour chaque message
        foreach ($messages as $key => $message) {
            $messages[$key]['nb_commentaires'] = $this->postModel->compterCommentaires($message['message_id']);
            
            // Gérer l'absence de tables reactions/votes (si pas encore créées)
            try {
                $messages[$key]['nb_reactions'] = $this->reactionModel->compter($message['message_id']);
                $messages[$key]['user_reacted'] = isset($_SESSION['user_id']) ? 
                    $this->reactionModel->aReagi($_SESSION['user_id'], $message['message_id']) : false;
            } catch (Exception $e) {
                $messages[$key]['nb_reactions'] = 0;
                $messages[$key]['user_reacted'] = false;
            }
        }

        require_once(__DIR__ . '/../Views/Post/liste.php');
    }

    /**
     * Afficher le formulaire de création
     */
    public function creer(){
        require_once(__DIR__ . '/../Views/Post/create.php');
    }

    /**
     * Enregistrer un nouveau message
     */
    public function enregistrer(){
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $titre = $_POST['titre'] ?? '';
            $contenu = $_POST['contenu'] ?? '';

            if (!empty($titre) && !empty($contenu)) {
                $resultat = $this->postModel->creer($titre, $contenu, $_SESSION['user_id']);

                if ($resultat) {
                    header('Location: ?action=messages');
                    exit;
                }
            }
        }
    }

    /**
     * Afficher le détail d'un message
     */
    public function detail(){
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

        if ($id > 0) {
            $message = $this->postModel->trouverParId($id);
            $commentaires = $this->commentModel->obtenirParMessage($id);

            // Ajouter les données de réactions pour le message (avec gestion d'erreur)
            try {
                $message['nb_reactions'] = $this->reactionModel->compter($id);
                $message['user_reacted'] = isset($_SESSION['user_id']) ? 
                    $this->reactionModel->aReagi($_SESSION['user_id'], $id) : false;
            } catch (Exception $e) {
                $message['nb_reactions'] = 0;
                $message['user_reacted'] = false;
            }

            // Ajouter les données de votes pour chaque commentaire (avec gestion d'erreur)
            foreach ($commentaires as &$commentaire) {
                try {
                    $scoreData = $this->voteModel->calculerScore($commentaire['commentaire_id']);
                    $commentaire['vote_score'] = $scoreData['score'];
                    $commentaire['user_vote'] = isset($_SESSION['user_id']) ? 
                        $this->voteModel->obtenirVote($_SESSION['user_id'], $commentaire['commentaire_id']) : null;
                } catch (Exception $e) {
                    $commentaire['vote_score'] = 0;
                    $commentaire['user_vote'] = null;
                }
            }

            if ($message) {
                require_once(__DIR__ . '/../Views/Post/detail.php');
                return;
            }
        }

        header('Location: ?action=messages');
        exit;
    }

    /**
     * Supprimer un message
     */
    public function supprimer(){
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

        if ($id > 0) {
            $this->postModel->supprimer($id);
        }

        header('Location: ?action=messages');
        exit;
    }

    /**
     * Afficher le formulaire de modification
     */
    public function modifier(){
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

        if ($id > 0) {
            $message = $this->postModel->trouverParId($id);

            // Vérifier que c'est l'auteur du message
            if ($message && $message['utilisateur_id'] == $_SESSION['user_id']) {
                require_once(__DIR__ . '/../Views/Post/modifier.php');
                return;
            }
        }

        header('Location: ?action=messages');
        exit;
    }

    /**
     * Enregistrer les modifications d'un message
     */
    public function mettreAJour(){
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['message_id'] ?? 0;
            $titre = $_POST['titre'] ?? '';
            $contenu = $_POST['contenu'] ?? '';

            if ($id > 0 && !empty($titre) && !empty($contenu)) {
                $message = $this->postModel->trouverParId($id);
                
                // Vérifier que c'est l'auteur
                if ($message && $message['utilisateur_id'] == $_SESSION['user_id']) {
                    $this->postModel->modifier($id, $titre, $contenu);
                    header('Location: ?action=detail&id=' . $id);
                    exit;
                }
            }
        }

        header('Location: ?action=messages');
        exit;
    }
}
