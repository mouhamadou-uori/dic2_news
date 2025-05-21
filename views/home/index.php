<?php ob_start(); ?>
<section class="articles">
    <h2>
        <?php 
        if (isset($_GET['categorie'])) {
            foreach ($categories as $cat) {
                if ($cat->getId() == $_GET['categorie']) {
                    echo "Actualités : " . htmlspecialchars($cat->getLibelle());
                    break;
                }
            }
        } else {
            echo "Dernières actualités";
        }
        ?>
    </h2>
    
    <?php if (count($articles) > 0): ?>
        <?php foreach ($articles as $article): ?>
            <article>
                <h3><?php echo htmlspecialchars($article->getTitre()); ?></h3>
                <div class="meta">
                    <span class="date">Publié le <?php echo date('d/m/Y à H:i', strtotime($article->getDateCreation())); ?></span>
                    <span class="categorie">Catégorie: <?php echo htmlspecialchars($article->getCategorieName()); ?></span>
                </div>
                <div class="contenu">
                    <p><?php echo nl2br(htmlspecialchars($article->getContenu())); ?></p>
                </div>
                <a href="index.php?controller=article&action=view&id=<?php echo $article->getId(); ?>" class="lire-plus">Lire l'article complet</a>
            </article>
        <?php endforeach; ?>
    <?php else: ?>
        <p class="no-articles">Aucun article disponible pour le moment.</p>
    <?php endif; ?>
</section>
<?php 
$pageTitle = "MGLSI News - Actualités";
$content = ob_get_clean(); 
require_once 'views/layouts/default.php';
?>
