<div class="container mt-4 mb-5 pb-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="?action=messages">Accueil</a></li>
                    <li class="breadcrumb-item active">Détail du message</li>
                </ol>
            </nav>

            <!-- Message principal -->
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0"><?php echo htmlspecialchars($message['titre']); ?></h4>
                    <div>
                        <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $message['utilisateur_id']): ?>
                            <a href="?action=modifier&id=<?php echo $message['message_id']; ?>" 
                               class="btn btn-sm btn-warning me-2">
                                <i class="bi bi-pencil"></i> Modifier
                            </a>
                            <a href="?action=supprimerMessage&id=<?php echo $message['message_id']; ?>" 
                               class="btn btn-sm btn-danger btn-supprimer-message">
                                <i class="bi bi-trash"></i> Supprimer
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="card-body">
                    <h6 class="card-subtitle mb-3 text-muted">
                        <i class="bi bi-person-circle"></i> Publié par <strong><?php echo htmlspecialchars($message['auteur_nom']); ?></strong> 
                        le <?php echo date('d/m/Y à H:i', strtotime($message['date_publication'])); ?>
                    </h6>
                    <hr>
                    <p class="card-text" style="white-space: pre-wrap;"><?php echo htmlspecialchars($message['contenu']); ?></p>
                    <div class="mt-3">
                        <button class="btn btn-sm btn-outline-danger reaction-btn <?php echo $message['user_reacted'] ? 'text-danger' : ''; ?>" 
                                onclick="toggleReaction(<?php echo $message['message_id']; ?>, this)">
                            <i class="bi <?php echo $message['user_reacted'] ? 'bi-heart-fill' : 'bi-heart'; ?>"></i>
                            J'aime <span class="reaction-count"><?php echo $message['nb_reactions']; ?></span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Commentaires -->
            <div class="card shadow">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-chat-left-text"></i> Commentaires (<span id="commentCount"><?php echo count($commentaires); ?></span>)
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Formulaire d'ajout de commentaire avec Ajax -->
                    <div class="mb-4">
                        <label for="contenuCommentaire" class="form-label">Ajouter un commentaire</label>
                        <textarea class="form-control" id="contenuCommentaire" rows="3" 
                                  placeholder="Votre commentaire..." required></textarea>
                        <button type="button" class="btn btn-primary mt-2" 
                                onclick="ajouterCommentaireAjax(<?php echo $message['message_id']; ?>)">
                            <i class="bi bi-send"></i> Commenter
                        </button>
                    </div>

                    <hr>

                    <!-- Liste des commentaires -->
                    <div id="commentaires-liste">
                        <?php if (empty($commentaires)): ?>
                            <p class="text-muted">Aucun commentaire pour le moment. Soyez le premier à commenter !</p>
                        <?php else: ?>
                            <?php foreach ($commentaires as $commentaire): ?>
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1">
                                                    <i class="bi bi-person-fill"></i> <?php echo htmlspecialchars($commentaire['auteur_nom']); ?>
                                                </h6>
                                                <p class="mt-2 mb-2 comment-content" data-comment-id="<?php echo $commentaire['commentaire_id']; ?>">
                                                    <?php echo nl2br(htmlspecialchars($commentaire['contenu'])); ?>
                                                </p>
                                                <small class="text-muted">
                                                    <i class="bi bi-clock"></i> <?php echo date('d/m/Y à H:i', strtotime($commentaire['date_commentaire'])); ?>
                                                </small>
                                                <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $commentaire['utilisateur_id']): ?>
                                                    <button class="btn btn-sm btn-link text-muted" 
                                                            onclick="activerEditionInline(<?php echo $commentaire['commentaire_id']; ?>)">
                                                        <i class="bi bi-pencil"></i> Modifier
                                                    </button>
                                                    <a href="?action=supprimerCommentaire&id=<?php echo $commentaire['commentaire_id']; ?>&post_id=<?php echo $message['message_id']; ?>" 
                                                       class="btn btn-sm btn-link text-danger btn-supprimer-commentaire">
                                                        <i class="bi bi-trash"></i> Supprimer
                                                    </a>
                                                <?php endif; ?>
                                            </div>
                                            <div class="vote-buttons ms-3">
                                                <button class="btn btn-sm btn-outline-success upvote-btn <?php echo $commentaire['user_vote'] === 'up' ? 'active text-success' : ''; ?>" 
                                                        onclick="vote(<?php echo $commentaire['commentaire_id']; ?>, 'up', this)">
                                                    <i class="bi bi-arrow-up"></i>
                                                </button>
                                                <span class="vote-score mx-2"><?php echo $commentaire['vote_score']; ?></span>
                                                <button class="btn btn-sm btn-outline-danger downvote-btn <?php echo $commentaire['user_vote'] === 'down' ? 'active text-danger' : ''; ?>" 
                                                        onclick="vote(<?php echo $commentaire['commentaire_id']; ?>, 'down', this)">
                                                    <i class="bi bi-arrow-down"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="mt-3">
                <a href="?action=messages" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Retour à la liste
                </a>
            </div>
        </div>
    </div>
</div>
