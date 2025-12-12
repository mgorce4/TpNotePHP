// Application Réseau Social d'Entreprise - JavaScript

document.addEventListener('DOMContentLoaded', function() {
    
    // ========================================
    // 1. Validation formulaire d'inscription
    // ========================================
    const formInscription = document.getElementById('formInscription');
    
    if (formInscription) {
        formInscription.addEventListener('submit', function(event) {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            
            if (password !== confirmPassword) {
                event.preventDefault();
                alert('❌ Les mots de passe ne correspondent pas !');
                return false;
            }
            
            if (password.length < 6) {
                event.preventDefault();
                alert('❌ Le mot de passe doit contenir au moins 6 caractères !');
                return false;
            }
        });
    }
    
    // ========================================
    // 2. Validation formulaire de message
    // ========================================
    const formMessage = document.getElementById('formMessage');
    
    if (formMessage) {
        formMessage.addEventListener('submit', function(event) {
            const contenu = document.getElementById('contenu').value;
            
            if (contenu.length < 10) {
                event.preventDefault();
                alert('❌ Le contenu doit contenir au moins 10 caractères !');
                return false;
            }
        });
    }
    
    // ========================================
    // 3. Validation formulaire de commentaire
    // ========================================
    const formCommentaire = document.getElementById('formCommentaire');
    
    if (formCommentaire) {
        formCommentaire.addEventListener('submit', function(event) {
            const contenu = document.getElementById('contenu').value;
            
            if (contenu.trim().length < 3) {
                event.preventDefault();
                alert('❌ Le commentaire doit contenir au moins 3 caractères !');
                return false;
            }
        });
    }
    
    // ========================================
    // 4. Confirmation de suppression message
    // ========================================
    const btnSupprimerMessage = document.querySelectorAll('.btn-supprimer-message');
    
    btnSupprimerMessage.forEach(function(btn) {
        btn.addEventListener('click', function(event) {
            const confirmation = confirm('⚠️ Êtes-vous sûr de vouloir supprimer ce message ?\n\nCette action supprimera également tous les commentaires associés.');
            
            if (!confirmation) {
                event.preventDefault();
                return false;
            }
        });
    });
    
    // ========================================
    // 5. Confirmation de suppression commentaire
    // ========================================
    const btnSupprimerCommentaire = document.querySelectorAll('.btn-supprimer-commentaire');
    
    btnSupprimerCommentaire.forEach(function(btn) {
        btn.addEventListener('click', function(event) {
            const confirmation = confirm('⚠️ Êtes-vous sûr de vouloir supprimer ce commentaire ?');
            
            if (!confirmation) {
                event.preventDefault();
                return false;
            }
        });
    });
    
    // ========================================
    // 6. Effet hover sur les cards de messages
    // ========================================
    const messageCards = document.querySelectorAll('.message-card');
    
    messageCards.forEach(function(card) {
        card.style.transition = 'all 0.3s ease';
        
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-3px)';
            this.style.boxShadow = '0 4px 8px rgba(0,0,0,0.2)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
            this.style.boxShadow = '';
        });
    });
    
    // ========================================
    // 7. Auto-resize textarea
    // ========================================
    const textareas = document.querySelectorAll('textarea');
    
    textareas.forEach(function(textarea) {
        textarea.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + 'px';
        });
    });
    
});
