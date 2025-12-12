# Réseau Social d'Entreprise - TP Final

## Fonctionnalités
- Création et gestion de profils utilisateurs
- Publication de messages et commentaires
- Système de notifications
- Recherche avancée
- Intégration avec des services tiers
  
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
CREATE TABLE utilisateurs(  
    utilisateur_id int NOT NULL PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    date_inscription DATETIME DEFAULT CURRENT_TIMESTAMP
) ;

CREATE TABLE messages(
    message_id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    titre VARCHAR(255) NOT NULL,
    contenu TEXT NOT NULL,
    utilisateur_id INT,
    date_publication DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(utilisateur_id)
);

CREATE TABLE commentaires(
    commentaire_id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    contenu TEXT NOT NULL,
    utilisateur_id INT,
    post_id INT,
    date_commentaire DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(utilisateur_id),
    FOREIGN KEY (post_id) REFERENCES messages(message_id)
);

CREATE TABLE notifications(
    notification_id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    utilisateur_id INT,
    message VARCHAR(255) NOT NULL,
    date_notification DATETIME DEFAULT CURRENT_TIMESTAMP,
    lu BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(utilisateur_id)
);

CREATE TABLE reactions(
    reaction_id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    type_reaction VARCHAR(50) NOT NULL,
    utilisateur_id INT,
    post_id INT,
    date_reaction DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(utilisateur_id),
    FOREIGN KEY (post_id) REFERENCES messages(message_id)
);

CREATE TABLE votes(
    vote_id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    utilisateur_id INT,
    post_id INT,
    valeur_vote INT CHECK (valeur_vote IN (1, -1)),
    date_vote DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(utilisateur_id),
    FOREIGN KEY (post_id) REFERENCES messages(message_id)
);
```

