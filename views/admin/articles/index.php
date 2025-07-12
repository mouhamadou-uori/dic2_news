<div class="admin-container">
    <div class="admin-header">
        <h1>Gestion des articles</h1>
        <div class="admin-actions">
            <a href="index.php?page=admin&action=createArticleForm" class="btn-admin">Nouvel article</a>
            <a href="index.php?page=admin" class="btn-admin">Retour au tableau de bord</a>
        </div>
    </div>
    
    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success">
            <?php echo htmlspecialchars($_SESSION['success_message']); ?>
            <?php unset($_SESSION['success_message']); ?>
        </div>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-error">
            <?php echo htmlspecialchars($_SESSION['error_message']); ?>
            <?php unset($_SESSION['error_message']); ?>
        </div>
    <?php endif; ?>
    
    <?php if (count($articles) > 0): ?>
        <div class="admin-table-container">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Titre</th>
                        <th>Catégorie</th>
                        <th>Auteur</th>
                        <th>Statut</th>
                        <th>Date de création</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($articles as $article): ?>
                        <tr>
                            <td><?php echo $article->getId(); ?></td>
                            <td><?php echo htmlspecialchars($article->getTitre()); ?></td>
                            <td><?php echo htmlspecialchars($article->getCategorieName()); ?></td>
                            <td><?php echo htmlspecialchars($article->getAuteurNom()); ?></td>
                            <td>
                                <span class="status-badge status-<?php echo $article->getStatut(); ?>">
                                    <?php 
                                    switch ($article->getStatut()) {
                                        case 'publie':
                                            echo 'Publié';
                                            break;
                                        case 'brouillon':
                                            echo 'Brouillon';
                                            break;
                                        case 'archive':
                                            echo 'Archivé';
                                            break;
                                        default:
                                            echo $article->getStatut();
                                    }
                                    ?>
                                </span>
                            </td>
                            <td><?php echo date('d/m/Y H:i', strtotime($article->getDateCreation())); ?></td>
                            <td class="actions">
                                <a href="index.php?page=article&action=view&id=<?php echo $article->getId(); ?>" class="btn-action btn-view" title="Voir l'article">
                                    <span>Voir</span>
                                </a>
                                <a href="index.php?page=admin&action=editArticleForm&id=<?php echo $article->getId(); ?>" class="btn-action btn-edit" title="Modifier l'article">
                                    <span>Modifier</span>
                                </a>
                                <a href="javascript:void(0);" onclick="confirmDelete(<?php echo $article->getId(); ?>, '<?php echo addslashes(htmlspecialchars($article->getTitre())); ?>')" class="btn-action btn-delete" title="Supprimer l'article">
                                    <span>Supprimer</span>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
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
                    $currentParams['action'] = 'listArticles';
                    unset($currentParams['page_num']); // Remove page parameter to rebuild URL
                    $baseUrl = "index.php?" . http_build_query($currentParams);
                    $separator = strpos($baseUrl, '?') !== false ? '&' : '?';
                    ?>
                    
                    <?php if ($pagination['hasPrevious']): ?>
                        <a href="<?php echo $baseUrl . $separator . 'page_num=' . ($pagination['currentPage'] - 1); ?>" class="btn-pagination btn-previous">
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
                        <a href="<?php echo $baseUrl . $separator . 'page_num=' . ($pagination['currentPage'] + 1); ?>" class="btn-pagination btn-next">
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
        <div class="no-items">
            <p>Aucun article disponible pour le moment.</p>
            <a href="index.php?page=admin&action=createArticleForm" class="btn-admin">Créer un article</a>
        </div>
    <?php endif; ?>
</div>

<script>
function confirmDelete(id, title) {
    if (confirm("Êtes-vous sûr de vouloir supprimer l'article \"" + title + "\" ?")) {
        window.location.href = "index.php?page=admin&action=deleteArticle&id=" + id;
    }
}
</script> 