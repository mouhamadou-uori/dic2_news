<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle : "Administration - MGLSI News"; ?></title>
    <link rel="stylesheet" href="public/css/style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="public/css/admin.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="container">
        <header>
            <div class="header-content">
                <div class="header-title">
                    <h1>MGLSI News - Administration</h1>
                </div>
                <div class="auth-buttons">
                    <div class="user-info">
                        <span class="welcome-text">Bienvenue, <?php echo htmlspecialchars($_SESSION['nom_complet']); ?></span>
                        <a href="index.php" class="btn-auth">Retour au site</a>
                        <a href="index.php?page=auth&action=logout" class="btn-auth">Déconnexion</a>
                    </div>
                </div>
            </div>
            <nav class="admin-nav">
                <ul>
                    <li><a href="index.php?page=admin" <?php echo !isset($_GET['action']) ? 'class="active"' : ''; ?>>Tableau de bord</a></li>
                    <li><a href="index.php?page=admin&action=listArticles" <?php echo (isset($_GET['action']) && $_GET['action'] == 'listArticles') ? 'class="active"' : ''; ?>>Articles</a></li>
                    <li><a href="index.php?page=admin&action=listCategories" <?php echo (isset($_GET['action']) && $_GET['action'] == 'listCategories') ? 'class="active"' : ''; ?>>Catégories</a></li>
                    <?php if ($_SESSION['role'] === 'administrateur'): ?>
                        <li><a href="index.php?page=admin&action=listUsers" <?php echo (isset($_GET['action']) && $_GET['action'] == 'listUsers') ? 'class="active"' : ''; ?>>Utilisateurs</a></li>
                        <li><a href="index.php?page=admin&action=listTokens" <?php echo (isset($_GET['action']) && $_GET['action'] == 'listTokens') ? 'class="active"' : ''; ?>>Jetons d'authentification</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </header>
        
        <main>
            <?php echo $content; ?>
        </main>
        
        <footer>
            <p>&copy; <?php echo date('Y'); ?> MGLSI News - Administration</p>
        </footer>
    </div>

    <script src="public/js/script.js"></script>
</body>
</html> 