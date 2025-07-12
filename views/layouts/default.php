<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle : "MGLSI News"; ?></title>
    <link rel="stylesheet" href="public/css/style.css?v=<?php echo time(); ?>">
</head>
<body>
    <div class="container">
        <header>
            <div class="header-content">
                <div class="header-title">
                    <h1>MGLSI News</h1>
                    <p>Votre source d'actualités quotidiennes</p>
                </div>
                <div class="auth-buttons">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <div class="user-info">
                            <span class="welcome-text">Bienvenue, <?php echo htmlspecialchars($_SESSION['nom_complet']); ?></span>
                            <?php if ($_SESSION['role'] === 'editeur' || $_SESSION['role'] === 'administrateur'): ?>
                                <a href="index.php?page=admin" class="btn-auth btn-admin">Administration</a>
                            <?php endif; ?>
                            <a href="index.php?page=logout" class="btn-auth btn-logout">Déconnexion</a>
                        </div>
                    <?php else: ?>
                        <a href="index.php?page=login" class="btn-auth btn-login">Connexion</a>
                        <a href="index.php?page=register" class="btn-auth btn-register">Inscription</a>
                    <?php endif; ?>
                </div>
            </div>
        </header>
        
        <nav>
            <ul>
                <li><a href="index.php" class="<?php echo !isset($_GET['categorie']) ? 'active' : ''; ?>">Toutes les actualités</a></li>
                <?php foreach ($categories as $categorie): ?>
                <li>
                    <a href="index.php?categorie=<?php echo $categorie->getId(); ?>" 
                       class="<?php echo isset($_GET['categorie']) && $_GET['categorie'] == $categorie->getId() ? 'active' : ''; ?>">
                        <?php echo htmlspecialchars($categorie->getLibelle()); ?>
                    </a>
                </li>
                <?php endforeach; ?>
            </ul>
        </nav>
        
        <main>
            <?php echo $content; ?>
        </main>
        
        <footer>
            <p>&copy; <?php echo date('Y'); ?> MGLSI News - Tous droits réservés</p>
        </footer>
    </div>

    <script src="public/js/script.js"></script>
</body>
</html>
