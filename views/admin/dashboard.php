<div class="admin-container">
    <div class="admin-header">
        <h1>Tableau de bord d'administration</h1>
        
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
    </div>
    
    <div class="admin-menu">
        <div class="admin-menu-item">
            <h2>Gestion des articles</h2>
            <p>Créer, modifier et supprimer des articles</p>
            <div class="admin-buttons">
                <a href="index.php?page=admin&action=listArticles" class="btn-admin">Liste des articles</a>
                <a href="index.php?page=admin&action=createArticleForm" class="btn-admin">Nouvel article</a>
            </div>
        </div>
        
        <div class="admin-menu-item">
            <h2>Gestion des catégories</h2>
            <p>Créer, modifier et supprimer des catégories</p>
            <div class="admin-buttons">
                <a href="index.php?page=admin&action=listCategories" class="btn-admin">Liste des catégories</a>
                <a href="index.php?page=admin&action=createCategoryForm" class="btn-admin">Nouvelle catégorie</a>
            </div>
        </div>
    </div>
</div> 