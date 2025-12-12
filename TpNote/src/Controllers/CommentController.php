<?php

namespace App\Controllers;

use App\Models\Comment;

class CommentController {
    private $commentModel;

    public function __construct(){
        $this->commentModel = new Comment();
    }

    /**
     * Ajouter un commentaire
     */
    public function ajouter(){
        if (!isset($_SESSION['user_id'])) {
            header('Location: ?action=connexion');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $contenu = $_POST['contenu'] ?? '';
            $post_id = $_POST['post_id'] ?? 0;

            if (!empty($contenu) && $post_id > 0) {
                $this->commentModel->creer($contenu, $_SESSION['user_id'], $post_id);
                header('Location: ?action=detail&id=' . $post_id);
                exit;
            }
        }

        header('Location: ?action=messages');
        exit;
    }

    /**
     * Supprimer un commentaire
     */
    public function supprimer(){
        if (!isset($_SESSION['user_id'])) {
            header('Location: ?action=connexion');
            exit;
        }

        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $post_id = isset($_GET['post_id']) ? (int)$_GET['post_id'] : 0;

        if ($id > 0) {
            $this->commentModel->supprimer($id);
        }

        if ($post_id > 0) {
            header('Location: ?action=detail&id=' . $post_id);
        } else {
            header('Location: ?action=messages');
        }
        exit;
    }
}
