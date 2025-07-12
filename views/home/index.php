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
        <div class="articles-grid">
            <?php foreach ($articles as $article): ?>
                <article class="article-summary">
                    <h3><?php echo htmlspecialchars($article->getTitre()); ?></h3>
                    <div class="meta">
                        <span class="date">Publié le <?php echo date('d/m/Y à H:i', strtotime($article->getDateCreation())); ?></span>
                        <span class="categorie">Catégorie: <?php echo htmlspecialchars($article->getCategorieName()); ?></span>
                    </div>
                    <div class="contenu-summary">
                        <p><?php echo nl2br(htmlspecialchars($article->getSummary())); ?></p>
                    </div>
                    <a href="index.php?page=article&action=view&id=<?php echo $article->getId(); ?>" class="lire-plus">Lire l'article complet</a>
                </article>
            <?php endforeach; ?>
        </div>
        
        <!-- Pagination -->
        <?php if ($pagination['totalPages'] > 1): ?>
            <div class="pagination">
                <div class="pagination-info">
                    <p>Page <?php echo $pagination['currentPage']; ?> sur <?php echo $pagination['totalPages']; ?> 
                    (<?php echo $pagination['totalArticles']; ?> articles au total)</p>
                </div>
                
                <div class="pagination-controls">
                    <?php
                    $currentParams = $_GET;
                    unset($currentParams['page']); // Remove page parameter to rebuild URL
                    $baseUrl = "index.php?" . http_build_query($currentParams);
                    $separator = empty($currentParams) ? "page=" : "&page=";
                    ?>
                    
                    <?php if ($pagination['hasPrevious']): ?>
                        <a href="<?php echo $baseUrl . $separator . ($pagination['currentPage'] - 1); ?>" class="btn-pagination btn-previous">
                            <span>Précédent</span>
                        </a>
                    <?php else: ?>
                        <span class="btn-pagination btn-previous disabled">
                            <span>Précédent</span>
                        </span>
                    <?php endif; ?>
                    
                    <span class="pagination-current">
                        <span>Page <?php echo $pagination['currentPage']; ?></span>
                    </span>
                    
                    <?php if ($pagination['hasNext']): ?>
                        <a href="<?php echo $baseUrl . $separator . ($pagination['currentPage'] + 1); ?>" class="btn-pagination btn-next">
                            <span>Suivant</span>
                        </a>
                    <?php else: ?>
                        <span class="btn-pagination btn-next disabled">
                            <span>Suivant</span>
                        </span>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
        
    <?php else: ?>
        <p class="no-articles">Aucun article disponible pour le moment.</p>
    <?php endif; ?>
</section>
<?php 
$pageTitle = "MGLSI News - Actualités";
$content = ob_get_clean(); 
require_once 'views/layouts/default.php';
?>
