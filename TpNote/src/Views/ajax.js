/**
 * Gestion des réactions (J'aime)
 */
function toggleReaction(messageId, button) {
    fetch('api.php?action=reaction', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'message_id=' + messageId
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const icon = button.querySelector('i');
            const countSpan = button.querySelector('.reaction-count');
            
            if (data.liked) {
                icon.classList.remove('bi-heart');
                icon.classList.add('bi-heart-fill');
                button.classList.add('text-danger');
            } else {
                icon.classList.remove('bi-heart-fill');
                icon.classList.add('bi-heart');
                button.classList.remove('text-danger');
            }
            
            countSpan.textContent = data.count;
        }
    })
    .catch(error => console.error('Erreur:', error));
}

/**
 * Gestion des votes (upvote/downvote)
 */
function vote(commentaireId, type, element) {
    fetch('api.php?action=vote', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'commentaire_id=' + commentaireId + '&type=' + type
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const container = element.closest('.vote-buttons');
            const scoreSpan = container.querySelector('.vote-score');
            const upBtn = container.querySelector('.upvote-btn');
            const downBtn = container.querySelector('.downvote-btn');
            
            // Mettre à jour le score
            scoreSpan.textContent = data.score.score;
            
            // Mettre à jour l'état des boutons
            upBtn.classList.remove('active', 'text-success');
            downBtn.classList.remove('active', 'text-danger');
            
            if (data.userVote === 'up') {
                upBtn.classList.add('active', 'text-success');
            } else if (data.userVote === 'down') {
                downBtn.classList.add('active', 'text-danger');
            }
        }
    })
    .catch(error => console.error('Erreur:', error));
}

/**
 * Ajouter un commentaire via Ajax
 */
function ajouterCommentaireAjax(postId) {
    const textarea = document.getElementById('contenuCommentaire');
    const contenu = textarea.value.trim();
    
    if (!contenu) {
        alert('Veuillez saisir un commentaire');
        return;
    }
    
    fetch('api.php?action=ajouterCommentaire', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'post_id=' + postId + '&contenu=' + encodeURIComponent(contenu)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Créer l'élément HTML du nouveau commentaire
            const commentDiv = document.createElement('div');
            commentDiv.className = 'card mb-2';
            commentDiv.innerHTML = `
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <h6 class="mb-1">${data.commentaire.nom}</h6>
                            <p class="mb-2 comment-content" data-comment-id="${data.commentaire.commentaire_id}">
                                ${data.commentaire.contenu}
                            </p>
                            <small class="text-muted">
                                <i class="bi bi-clock"></i> À l'instant
                            </small>
                        </div>
                        <div class="vote-buttons ms-3">
                            <button class="btn btn-sm btn-outline-success upvote-btn" 
                                    onclick="vote(${data.commentaire.commentaire_id}, 'up', this)">
                                <i class="bi bi-arrow-up"></i>
                            </button>
                            <span class="vote-score mx-2">0</span>
                            <button class="btn btn-sm btn-outline-danger downvote-btn" 
                                    onclick="vote(${data.commentaire.commentaire_id}, 'down', this)">
                                <i class="bi bi-arrow-down"></i>
                            </button>
                        </div>
                    </div>
                </div>
            `;
            
            // Ajouter le commentaire à la liste
            const commentsList = document.getElementById('commentaires-liste');
            
            // Supprimer le message "Aucun commentaire" s'il existe
            const noCommentMsg = commentsList.querySelector('.text-muted');
            if (noCommentMsg && noCommentMsg.textContent.includes('Aucun commentaire')) {
                noCommentMsg.remove();
            }
            
            // Ajouter le nouveau commentaire au début
            commentsList.insertBefore(commentDiv, commentsList.firstChild);
            
            // Mettre à jour le compteur de commentaires
            const commentCount = document.getElementById('commentCount');
            if (commentCount) {
                const currentCount = parseInt(commentCount.textContent) || 0;
                commentCount.textContent = currentCount + 1;
            }
            
            // Réinitialiser le formulaire
            textarea.value = '';
            
            // Afficher une notification de succès
            showNotificationBanner('Commentaire ajouté avec succès', 'success');
        } else {
            alert(data.message || 'Erreur lors de l\'ajout du commentaire');
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        alert('Erreur lors de l\'ajout du commentaire');
    });
}

/**
 * Recherche en temps réel
 */
let searchTimeout;
function rechercheEnTempsReel() {
    const searchInput = document.getElementById('searchInput');
    const searchResults = document.getElementById('searchResults');
    const query = searchInput.value.trim();
    
    clearTimeout(searchTimeout);
    
    if (query.length < 2) {
        searchResults.innerHTML = '';
        searchResults.style.display = 'none';
        return;
    }
    
    searchTimeout = setTimeout(() => {
        fetch('api.php?action=recherche&q=' + encodeURIComponent(query))
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                let html = '';
                
                // Afficher les messages
                if (data.results.messages && data.results.messages.length > 0) {
                    html += '<div class="search-category"><strong>Messages</strong></div>';
                    data.results.messages.forEach(msg => {
                        html += `
                            <a href="?action=detail&id=${msg.message_id}" class="search-result-item">
                                <i class="bi bi-chat-text"></i>
                                <div>
                                    <strong>${msg.titre}</strong>
                                    <small class="d-block text-muted">par ${msg.auteur_nom}</small>
                                </div>
                            </a>
                        `;
                    });
                }
                
                // Afficher les utilisateurs
                if (data.results.utilisateurs && data.results.utilisateurs.length > 0) {
                    html += '<div class="search-category"><strong>Utilisateurs</strong></div>';
                    data.results.utilisateurs.forEach(user => {
                        html += `
                            <a href="?action=profil&id=${user.utilisateur_id}" class="search-result-item">
                                <i class="bi bi-person"></i>
                                <div>
                                    <strong>${user.nom}</strong>
                                    <small class="d-block text-muted">${user.email}</small>
                                </div>
                            </a>
                        `;
                    });
                }
                
                if (html === '') {
                    html = '<div class="search-no-results">Aucun résultat trouvé</div>';
                }
                
                searchResults.innerHTML = html;
                searchResults.style.display = 'block';
            }
        })
        .catch(error => console.error('Erreur:', error));
    }, 300);
}

// Fermer les résultats de recherche si on clique ailleurs
document.addEventListener('click', function(e) {
    const searchResults = document.getElementById('searchResults');
    const searchInput = document.getElementById('searchInput');
    
    if (searchResults && !e.target.closest('.search-container')) {
        searchResults.style.display = 'none';
    }
});

/**
 * Charger les notifications
 */
function chargerNotifications() {
    fetch('api.php?action=notifications')
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const badge = document.getElementById('notificationBadge');
            const dropdown = document.getElementById('notificationDropdown');
            
            // Mettre à jour le badge
            if (data.count > 0) {
                badge.textContent = data.count;
                badge.style.display = 'inline';
            } else {
                badge.style.display = 'none';
            }
            
            // Mettre à jour la liste des notifications
            if (data.notifications.length > 0) {
                let html = '';
                data.notifications.forEach(notif => {
                    html += `
                        <a href="${notif.lien}" class="dropdown-item notification-item" 
                           onclick="marquerCommeLue(${notif.notification_id})">
                            <div class="d-flex align-items-start">
                                <i class="bi bi-${getNotificationIcon(notif.type)} me-2"></i>
                                <div>
                                    <div>${notif.message}</div>
                                    <small class="text-muted">${formatDate(notif.date_notification)}</small>
                                </div>
                            </div>
                        </a>
                    `;
                });
                html += `
                    <div class="dropdown-divider"></div>
                    <a href="#" class="dropdown-item text-center" onclick="marquerToutesLues(); return false;">
                        Tout marquer comme lu
                    </a>
                `;
                dropdown.innerHTML = html;
            } else {
                dropdown.innerHTML = '<div class="dropdown-item text-muted">Aucune notification</div>';
            }
        }
    })
    .catch(error => console.error('Erreur:', error));
}

/**
 * Marquer une notification comme lue
 */
function marquerCommeLue(notificationId) {
    fetch('api.php?action=marquerLu', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'notification_id=' + notificationId
    })
    .then(() => chargerNotifications())
    .catch(error => console.error('Erreur:', error));
}

/**
 * Marquer toutes les notifications comme lues
 */
function marquerToutesLues() {
    fetch('api.php?action=marquerLu', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: ''
    })
    .then(() => chargerNotifications())
    .catch(error => console.error('Erreur:', error));
}

/**
 * Édition inline des commentaires
 */
function activerEditionInline(commentId) {
    const contentElement = document.querySelector(`.comment-content[data-comment-id="${commentId}"]`);
    const originalContent = contentElement.textContent.trim();
    
    contentElement.contentEditable = true;
    contentElement.classList.add('editing');
    contentElement.focus();
    
    // Créer les boutons de validation/annulation
    const actionsDiv = document.createElement('div');
    actionsDiv.className = 'edit-actions mt-2';
    actionsDiv.innerHTML = `
        <button class="btn btn-sm btn-success" onclick="sauvegarderCommentaire(${commentId}, this)">
            <i class="bi bi-check"></i> Enregistrer
        </button>
        <button class="btn btn-sm btn-secondary" onclick="annulerEdition(${commentId}, '${originalContent.replace(/'/g, "\\'")}', this)">
            <i class="bi bi-x"></i> Annuler
        </button>
    `;
    contentElement.after(actionsDiv);
}

/**
 * Sauvegarder un commentaire édité
 */
function sauvegarderCommentaire(commentId, button) {
    const contentElement = document.querySelector(`.comment-content[data-comment-id="${commentId}"]`);
    const newContent = contentElement.textContent.trim();
    
    if (!newContent) {
        alert('Le commentaire ne peut pas être vide');
        return;
    }
    
    fetch('api.php?action=modifierCommentaire', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'commentaire_id=' + commentId + '&contenu=' + encodeURIComponent(newContent)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            contentElement.contentEditable = false;
            contentElement.classList.remove('editing');
            button.parentElement.remove();
            showNotificationBanner('Commentaire modifié avec succès', 'success');
        } else {
            alert(data.message || 'Erreur lors de la modification');
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        alert('Erreur lors de la modification');
    });
}

/**
 * Annuler l'édition
 */
function annulerEdition(commentId, originalContent, button) {
    const contentElement = document.querySelector(`.comment-content[data-comment-id="${commentId}"]`);
    contentElement.textContent = originalContent;
    contentElement.contentEditable = false;
    contentElement.classList.remove('editing');
    button.parentElement.remove();
}

/**
 * Mode sombre/clair
 */
function toggleDarkMode() {
    const body = document.body;
    const isDark = body.classList.toggle('dark-mode');
    
    // Sauvegarder la préférence
    localStorage.setItem('darkMode', isDark ? 'enabled' : 'disabled');
    
    // Mettre à jour l'icône
    const icon = document.querySelector('#darkModeToggle i');
    if (isDark) {
        icon.classList.remove('bi-moon-fill');
        icon.classList.add('bi-sun-fill');
    } else {
        icon.classList.remove('bi-sun-fill');
        icon.classList.add('bi-moon-fill');
    }
}

// Charger la préférence de mode sombre au chargement
document.addEventListener('DOMContentLoaded', function() {
    const darkMode = localStorage.getItem('darkMode');
    if (darkMode === 'enabled') {
        document.body.classList.add('dark-mode');
        const icon = document.querySelector('#darkModeToggle i');
        if (icon) {
            icon.classList.remove('bi-moon-fill');
            icon.classList.add('bi-sun-fill');
        }
    }
    
    // Charger les notifications toutes les 10 secondes
    if (document.getElementById('notificationBadge')) {
        chargerNotifications();
        setInterval(chargerNotifications, 10000);
    }
});

/**
 * Fonctions utilitaires
 */
function getNotificationIcon(type) {
    const icons = {
        'reaction': 'heart-fill',
        'commentaire': 'chat-fill',
        'vote': 'arrow-up-circle-fill'
    };
    return icons[type] || 'bell-fill';
}

function formatDate(dateString) {
    const date = new Date(dateString);
    const now = new Date();
    const diff = now - date;
    
    const minutes = Math.floor(diff / 60000);
    const hours = Math.floor(diff / 3600000);
    const days = Math.floor(diff / 86400000);
    
    if (minutes < 1) return 'À l\'instant';
    if (minutes < 60) return `Il y a ${minutes} min`;
    if (hours < 24) return `Il y a ${hours}h`;
    if (days < 7) return `Il y a ${days}j`;
    
    return date.toLocaleDateString('fr-FR');
}

function showNotificationBanner(message, type = 'info') {
    const banner = document.createElement('div');
    banner.className = `alert alert-${type} alert-dismissible fade show notification-banner`;
    banner.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(banner);
    
    setTimeout(() => {
        banner.remove();
    }, 3000);
}
