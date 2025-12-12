<div class="container mt-4 mb-5 pb-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h3 class="mb-0"><i class="bi bi-person-circle"></i> Mon Profil</h3>
                </div>
                <div class="card-body">
                    <?php if (isset($_GET['success'])): ?>
                        <div class="alert alert-success">Profil modifié avec succès !</div>
                    <?php endif; ?>
                    
                    <form action="?action=modifierProfil" method="POST">
                        <div class="mb-3">
                            <label for="nom" class="form-label">Nom complet</label>
                            <input type="text" class="form-control" id="nom" name="nom" 
                                   value="<?php echo htmlspecialchars($user['nom']); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="<?php echo htmlspecialchars($user['email']); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Date d'inscription</label>
                            <input type="text" class="form-control" 
                                   value="<?php echo date('d/m/Y H:i', strtotime($user['date_inscription'])); ?>" 
                                   disabled>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Enregistrer les modifications
                            </button>
                            <a href="?action=messages" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Retour aux messages
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
