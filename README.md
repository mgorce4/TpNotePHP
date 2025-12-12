# Réseau Social d'Entreprise - TP Final

## 🎯 Objectif
Application web de réseau social d'entreprise permettant aux employés de publier des messages et d'interagir via des commentaires.

## 📋 Fonctionnalités

### A. Gestion des utilisateurs ✅
- ✅ **Inscription** : Formulaire avec nom, email et mot de passe hashé (password_hash)
- ✅ **Connexion** : Vérification avec password_verify()
- ✅ **Profil** (bonus) : Modification des informations personnelles
- ✅ **Déconnexion** : Destruction de session

### B. Gestion des messages ✅
- ✅ **Créer un message** : Titre et contenu
- ✅ **Liste des messages** : Affichage de tous les messages
- ✅ **Détail d'un message** : Affichage avec commentaires
- ✅ **Supprimer un message** : Avec confirmation JavaScript

### C. Gestion des commentaires ✅
- ✅ **Ajouter un commentaire** : Sur un message
- ✅ **Supprimer un commentaire** : Avec confirmation
- ✅ **Affichage** : Liste ordonnée chronologiquement

## 🗂️ Structure MVC

```
TpNote/
├── index.php                 # Point d'entrée avec routing
├── src/
│   ├── Models/
│   │   ├── Database.php      # Connexion BDD
│   │   ├── User.php          # Modèle utilisateur
│   │   ├── Post.php          # Modèle message
│   │   └── Comment.php       # Modèle commentaire
│   ├── Controllers/
│   │   ├── UserController.php
│   │   ├── PostController.php
│   │   └── CommentController.php
│   └── Views/
│       ├── header.php        # En-tête avec navigation
│       ├── footer.php        # Pied de page
│       ├── app.js            # JavaScript
│       ├── User/
│       │   ├── inscription.php
│       │   ├── connexion.php
│       │   └── profil.php
│       └── Post/
│           ├── liste.php
│           ├── create.php
│           └── detail.php
```

## 🗄️ Base de données

### Tables créées :

```sql
CREATE TABLE utilisateurs(  
    utilisateur_id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    date_inscription DATETIME DEFAULT CURRENT_TIMESTAMP
);

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
```

## 🚀 Installation et démarrage

### 1. Créer la base de données

Connectez-vous à phpMyAdmin : http://localhost:8899

Exécutez les 3 requêtes SQL ci-dessus pour créer les tables.

### 2. Démarrer Docker

```powershell
cd c:\Users\Max\Desktop\others\2025-2026\php\TpNote
docker-compose up -d
```

### 3. Accéder à l'application

Ouvrez votre navigateur : **http://localhost**

## 🎮 Utilisation

1. **Première visite** : Créez un compte via "Inscription"
2. **Connexion** : Utilisez votre email et mot de passe
3. **Publier** : Cliquez sur "Nouveau message" pour publier
4. **Interagir** : Cliquez sur un message pour voir les détails et commenter
5. **Profil** : Menu utilisateur > Profil pour modifier vos informations

## ✨ Fonctionnalités JavaScript

- ✅ Validation des formulaires (mots de passe, longueur)
- ✅ Confirmation avant suppression
- ✅ Effets hover sur les cartes de messages
- ✅ Auto-resize des textareas
- ✅ Messages d'erreur clairs

## 🔐 Sécurité

- ✅ Mots de passe hashés avec `password_hash()`
- ✅ Vérification avec `password_verify()`
- ✅ Protection SQL injection (requêtes préparées PDO)
- ✅ Protection XSS (htmlspecialchars)
- ✅ Gestion de sessions sécurisée
- ✅ Validation des entrées utilisateur

## 🎨 Technologies utilisées

- **Backend** : PHP 8.2, PDO
- **Frontend** : Bootstrap 5.3, Bootstrap Icons
- **Base de données** : MySQL 8.0
- **Architecture** : MVC
- **JavaScript** : Vanilla JS (validation, interactivité)

## 📝 Routes disponibles

| Action | URL | Description |
|--------|-----|-------------|
| Inscription | `?action=inscription` | Formulaire d'inscription |
| Connexion | `?action=connexion` | Formulaire de connexion |
| Messages | `?action=messages` | Liste des messages (accueil) |
| Nouveau message | `?action=creer` | Créer un message |
| Détail | `?action=detail&id=X` | Voir message et commentaires |
| Profil | `?action=profil` | Modifier son profil |
| Déconnexion | `?action=deconnexion` | Se déconnecter |

## ✅ Critères d'évaluation respectés

- ✅ Architecture MVC complète
- ✅ Base de données avec relations (FK)
- ✅ Hash des mots de passe (password_hash/verify)
- ✅ Gestion de sessions
- ✅ CRUD complet (Create, Read, Update, Delete)
- ✅ Interface Bootstrap responsive
- ✅ JavaScript pour validation et UX
- ✅ Sécurité (PDO, htmlspecialchars)
- ✅ Code commenté et structuré

---

**Auteur** : Réalisé selon les consignes du TP Final  
**Date** : Décembre 2025  
**Framework** : Inspiré de l'architecture Lacosina
