<div class="container mt-4 mb-5 pb-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-warning">
                    <h3 class="mb-0"><i class="bi bi-pencil-square"></i> Modifier le message</h3>
                </div>
                <div class="card-body">
                    <form method="POST" action="?action=mettreAJour" id="modifierMessageForm">
                        <input type="hidden" name="message_id" value="<?php echo $message['message_id']; ?>">
                        
                        <div class="mb-3">
                            <label for="titre" class="form-label">Titre</label>
                            <input type="text" class="form-control" id="titre" name="titre" 
                                   value="<?php echo htmlspecialchars($message['titre']); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="contenu" class="form-label">Contenu</label>
                            <textarea class="form-control" id="contenu" name="contenu" 
                                      rows="6" required><?php echo htmlspecialchars($message['contenu']); ?></textarea>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-warning">
                                <i class="bi bi-check-circle"></i> Enregistrer les modifications
                            </button>
                            <a href="?action=detail&id=<?php echo $message['message_id']; ?>" 
                               class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Annuler
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
