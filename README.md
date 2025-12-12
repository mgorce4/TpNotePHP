# Réseau Social d'Entreprise - TP Final

## Fonctionnalités
- Création et gestion de profils utilisateurs
- Publication de messages et commentaires
- Système de notifications
- Recherche avancée
- Gestion des réactions (likes, upvotes, downvotes)
- mode sombre et clair
  
## Comment lancer ce projet 
1- Etre dans le grand dossier du projet "TpNote"
```bash
docker-compose up -d --build
```
2- Accéder à l'application via votre navigateur à l'adresse suivante : 
```
http://localhost:8000
```
3- Pour arrêter les conteneurs, utilisez la commande suivante :
```bash
docker-compose down
```
## Script SQL

```sql
CREATE TABLE utilisateurs (
    utilisateur_id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    date_inscription TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ;

CREATE TABLE messages (
    message_id INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(255) NOT NULL,
    contenu TEXT NOT NULL,
    utilisateur_id INT NOT NULL,
    date_publication TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(utilisateur_id) ON DELETE CASCADE
);

CREATE TABLE commentaires (
    commentaire_id INT AUTO_INCREMENT PRIMARY KEY,
    contenu TEXT NOT NULL,
    utilisateur_id INT NOT NULL,
    post_id INT NOT NULL,
    date_commentaire TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(utilisateur_id) ON DELETE CASCADE,
    FOREIGN KEY (post_id) REFERENCES messages(message_id) ON DELETE CASCADE
) ;

CREATE TABLE reactions (
    reaction_id INT AUTO_INCREMENT PRIMARY KEY,
    type VARCHAR(20) DEFAULT 'like',
    utilisateur_id INT NOT NULL,
    message_id INT NOT NULL,
    date_reaction TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(utilisateur_id) ON DELETE CASCADE,
    FOREIGN KEY (message_id) REFERENCES messages(message_id) ON DELETE CASCADE,
    UNIQUE KEY unique_reaction (utilisateur_id, message_id)
);

CREATE TABLE votes (
    vote_id INT AUTO_INCREMENT PRIMARY KEY,
    type ENUM('up', 'down') NOT NULL,
    utilisateur_id INT NOT NULL,
    commentaire_id INT NOT NULL,
    date_vote TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(utilisateur_id) ON DELETE CASCADE,
    FOREIGN KEY (commentaire_id) REFERENCES commentaires(commentaire_id) ON DELETE CASCADE,
    UNIQUE KEY unique_vote (utilisateur_id, commentaire_id)
);

CREATE TABLE notifications (
    notification_id INT AUTO_INCREMENT PRIMARY KEY,
    utilisateur_id INT NOT NULL,
    type VARCHAR(50) NOT NULL,
    message TEXT NOT NULL,
    lien VARCHAR(255),
    lu BOOLEAN DEFAULT FALSE,
    date_notification TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(utilisateur_id) ON DELETE CASCADE
);

CREATE INDEX idx_messages_utilisateur ON messages(utilisateur_id);
CREATE INDEX idx_commentaires_post ON commentaires(post_id);
CREATE INDEX idx_commentaires_utilisateur ON commentaires(utilisateur_id);
CREATE INDEX idx_reactions_message ON reactions(message_id);
CREATE INDEX idx_votes_commentaire ON votes(commentaire_id);
CREATE INDEX idx_notifications_utilisateur ON notifications(utilisateur_id);
CREATE INDEX idx_notifications_non_lues ON notifications(utilisateur_id, lu);
```

