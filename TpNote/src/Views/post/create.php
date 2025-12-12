<div class="container mt-4 mb-5 pb-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h3 class="mb-0"><i class="bi bi-pencil-square"></i> Publier un nouveau message</h3>
                </div>
                <div class="card-body">
                    <form action="?action=enregistrerMessage" method="POST" id="formMessage">
                        <div class="mb-3">
                            <label for="titre" class="form-label">Titre <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="titre" name="titre" 
                                   placeholder="Ex: Réunion d'équipe..." required>
                        </div>

                        <div class="mb-3">
                            <label for="contenu" class="form-label">Contenu <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="contenu" name="contenu" rows="6" 
                                      placeholder="Partagez vos idées..." required></textarea>
                            <div class="form-text">Minimum 10 caractères</div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-send"></i> Publier
                            </button>
                            <a href="?action=messages" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Annuler
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
