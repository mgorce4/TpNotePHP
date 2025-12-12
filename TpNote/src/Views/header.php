<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="src/Views/styles.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <title>Réseau Social d'Entreprise</title>
</head>
<body style="padding-bottom: 150px;">
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="?action=messages">
                <i class="bi bi-chat-dots-fill"></i> Réseau Social
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <?php if (isset($_SESSION['user_id'])): ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo (isset($_GET['action']) && $_GET['action'] === 'messages') ? 'active' : ''; ?>" 
                           href="?action=messages">
                            <i class="bi bi-house"></i> Accueil
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo (isset($_GET['action']) && $_GET['action'] === 'creer') ? 'active' : ''; ?>" 
                           href="?action=creer">
                            <i class="bi bi-plus-circle"></i> Nouveau message
                        </a>
                    </li>
                    <?php endif; ?>
                </ul>
                
                <?php if (isset($_SESSION['user_id'])): ?>
                <!-- Barre de recherche -->
                <div class="search-container me-3">
                    <input type="text" id="searchInput" class="form-control" 
                           placeholder="Rechercher..." onkeyup="rechercheEnTempsReel()">
                    <div id="searchResults"></div>
                </div>
                
                <!-- Notifications -->
                <div class="dropdown me-3">
                    <button class="btn btn-link position-relative" type="button" 
                            id="notificationButton" data-bs-toggle="dropdown">
                        <i class="bi bi-bell-fill" style="font-size: 1.5rem;"></i>
                        <span id="notificationBadge" class="badge" style="display: none;">0</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" id="notificationDropdown">
                        <li><div class="dropdown-item text-muted">Aucune notification</div></li>
                    </ul>
                </div>
                <?php endif; ?>
                
                <ul class="navbar-nav">
                    <?php if (isset($_SESSION['user_id'])): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle"></i> <?php echo htmlspecialchars($_SESSION['user_nom']); ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="?action=profil"><i class="bi bi-person"></i> Profil</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="?action=deconnexion"><i class="bi bi-box-arrow-right"></i> Déconnexion</a></li>
                        </ul>
                    </li>
                    <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="?action=connexion">Connexion</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="?action=inscription">Inscription</a>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Bouton de mode sombre -->
    <button id="darkModeToggle" onclick="toggleDarkMode()" title="Changer de thème">
        <i class="bi bi-moon-fill"></i>
    </button>

    <div class="container-fluid mt-4">
