<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle : "MGLSI News"; ?></title>
    <link rel="stylesheet" href="public/css/style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>MGLSI News</h1>
            <p>Votre source d'actualités quotidiennes</p>
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
