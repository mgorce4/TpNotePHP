# Installation des Nouvelles Fonctionnalités

## Étape 1: Exécuter le script SQL

1. Accédez à phpMyAdmin: http://localhost:8899
2. Sélectionnez la base de données `TpNote`
3. Cliquez sur l'onglet "SQL"
4. Copiez et collez le contenu du fichier `nouvelles-tables.sql`
5. Cliquez sur "Exécuter"

Cela créera les tables suivantes:
- `reactions` - Pour les "J'aime" sur les messages
- `votes` - Pour les votes (upvote/downvote) sur les commentaires
- `notifications` - Pour les notifications en temps réel

## Étape 2: Tester les Fonctionnalités

### 1. Réactions (J'aime)
- Sur la page des messages, cliquez sur le bouton ❤️
- Le compteur s'incrémente sans recharger la page
- Cliquez à nouveau pour retirer votre réaction

### 2. Votes sur Commentaires
- Sur la page de détail d'un message, ajoutez un commentaire
- Utilisez les boutons ↑ (upvote) et ↓ (downvote) 
- Le score se met à jour instantanément

### 3. Commentaires en Ajax
- Sur la page de détail, ajoutez un commentaire
- Il apparaît immédiatement sans recharger la page

### 4. Édition Inline
- Cliquez sur "Modifier" sous votre propre commentaire
- Le texte devient éditable
- Modifiez et cliquez "Enregistrer" ou "Annuler"

### 5. Recherche en Temps Réel
- Dans la barre de recherche du header (en haut)
- Tapez au moins 2 caractères
- Les résultats apparaissent instantanément (messages et utilisateurs)

### 6. Notifications
- L'icône 🔔 dans le header affiche un badge rouge avec le nombre de notifications
- Les notifications sont actualisées toutes les 10 secondes
- Cliquez sur une notification pour la marquer comme lue

### 7. Mode Sombre
- Cliquez sur le bouton flottant en bas à droite (icône 🌙)
- Le thème bascule entre mode clair et mode sombre
- La préférence est sauvegardée dans le navigateur

### 8. Modification de Messages
- Sur vos propres messages, cliquez sur le bouton "✏️ Modifier"
- Modifiez le titre et/ou le contenu
- Enregistrez les modifications

## Vérification de l'Installation

Si vous voyez cette erreur:
```
Table 'TpNote.reactions' doesn't exist
```

C'est que vous n'avez pas encore exécuté le script SQL. Retournez à l'Étape 1.

## Fonctionnalités Implémentées

✅ Modification de messages (Partie B)
✅ Ajout de commentaires en Ajax (Partie C.1)
✅ Système de réactions/J'aime (Partie C.2)
✅ Votes sur commentaires (Partie C.3)
✅ Recherche en temps réel (Partie C.4)
✅ Édition inline des commentaires (Partie C.5)
✅ Notifications avec polling 10s (Partie D.1)
✅ Mode sombre (Partie D.2)

## Architecture Technique

### Nouveaux Fichiers
- `api.php` - Endpoints API pour les requêtes Ajax
- `src/Models/Reaction.php` - Gestion des réactions
- `src/Models/Vote.php` - Gestion des votes
- `src/Models/Notification.php` - Gestion des notifications
- `src/Views/ajax.js` - Fonctions JavaScript Ajax
- `src/Views/styles.css` - Styles CSS avec variables pour le mode sombre
- `src/Views/Post/modifier.php` - Formulaire de modification

### Modifications
- `src/Controllers/PostController.php` - Ajout des méthodes modifier() et mettreAJour()
- `src/Models/Post.php` - Ajout de la méthode rechercher()
- `src/Models/User.php` - Ajout de la méthode rechercher()
- `src/Models/Comment.php` - Ajout des méthodes trouverParId() et modifier()
- `src/Views/header.php` - Ajout de la recherche, notifications et mode sombre
- `src/Views/Post/liste.php` - Boutons de réaction et modification
- `src/Views/Post/detail.php` - Ajax pour commentaires, votes et édition inline
- `index.php` - Gestion de l'authentification avant l'inclusion du header

## Notes Importantes

1. **Sécurité**: Toutes les entrées utilisateur sont échappées avec `htmlspecialchars()`
2. **Authentification**: Les vérifications sont centralisées dans `index.php`
3. **Performance**: Des index sont créés sur les clés étrangères pour optimiser les requêtes
4. **UX**: Toutes les actions Ajax incluent un feedback visuel
5. **Responsive**: L'interface s'adapte aux mobiles grâce à Bootstrap 5

## Dépannage

**Problème**: "Cannot modify header information"
**Solution**: Les redirections sont maintenant gérées avant l'inclusion du header dans `index.php`

**Problème**: Les notifications ne se chargent pas
**Solution**: Vérifiez que vous êtes bien connecté et que la table `notifications` existe

**Problème**: Le mode sombre ne persiste pas
**Solution**: Vérifiez que localStorage est activé dans votre navigateur

**Problème**: Les réactions ne fonctionnent pas
**Solution**: Vérifiez la console JavaScript (F12) pour les erreurs et que le fichier `ajax.js` est bien chargé
