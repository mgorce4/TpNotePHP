<div class="container mt-4 mb-5 pb-5">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="bi bi-chat-left-dots"></i> Fil d'actualité</h2>
                <a href="?action=creer" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Nouveau message
                </a>
            </div>

            <?php if (empty($messages)): ?>
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i> Aucun message pour le moment. Soyez le premier à publier !
                </div>
            <?php else: ?>
                <?php foreach ($messages as $message): ?>
                    <div class="card mb-3 shadow-sm message-card" data-id="<?php echo $message['message_id']; ?>">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <h5 class="card-title"><?php echo htmlspecialchars($message['titre']); ?></h5>
                                <div>
                                    <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $message['utilisateur_id']): ?>
                                        <a href="?action=modifier&id=<?php echo $message['message_id']; ?>" 
                                           class="btn btn-sm btn-warning me-2">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <a href="?action=supprimerMessage&id=<?php echo $message['message_id']; ?>" 
                                           class="btn btn-sm btn-danger btn-supprimer-message">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <h6 class="card-subtitle mb-2 text-muted">
                                <i class="bi bi-person"></i> <?php echo htmlspecialchars($message['auteur_nom']); ?> 
                                - <?php echo date('d/m/Y H:i', strtotime($message['date_publication'])); ?>
                            </h6>
                            <p class="card-text"><?php echo nl2br(htmlspecialchars($message['contenu'])); ?></p>
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <div class="d-flex gap-3">
                                    <!-- Bouton de réaction -->
                                    <button class="btn btn-sm btn-outline-danger reaction-btn <?php echo $message['user_reacted'] ? 'text-danger' : ''; ?>" 
                                            onclick="toggleReaction(<?php echo $message['message_id']; ?>, this)">
                                        <i class="bi <?php echo $message['user_reacted'] ? 'bi-heart-fill' : 'bi-heart'; ?>"></i>
                                        <span class="reaction-count"><?php echo $message['nb_reactions']; ?></span>
                                    </button>
                                    <span class="text-muted">
                                        <i class="bi bi-chat"></i> <?php echo $message['nb_commentaires']; ?> commentaire(s)
                                    </span>
                                </div>
                                <a href="?action=detail&id=<?php echo $message['message_id']; ?>" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i> Voir les détails
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>
