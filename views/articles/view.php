<?php ob_start(); ?>
<section class="article-detail">
    <article>
        <h2><?php echo htmlspecialchars($article->getTitre()); ?></h2>
        <div class="meta">
            <span class="date">Publié le <?php echo date('d/m/Y à H:i', strtotime($article->getDateCreation())); ?></span>
            <span class="categorie">Catégorie: <?php echo htmlspecialchars($article->getCategorieName()); ?></span>
        </div>
        <div class="contenu">
            <p><?php echo nl2br(htmlspecialchars($article->getContenu())); ?></p>
        </div>
        <a href="index.php" class="retour">Retour aux actualités</a>
    </article>
</section>
<?php 
$pageTitle = htmlspecialchars($article->getTitre()) . " - MGLSI News";
$content = ob_get_clean(); 
require_once 'views/layouts/default.php';
?>
