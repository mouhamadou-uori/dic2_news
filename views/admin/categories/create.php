<div class="admin-container">
    <div class="admin-header">
        <h1>Créer une nouvelle catégorie</h1>
        <div class="admin-actions">
            <a href="index.php?page=admin&action=listCategories" class="btn-admin">Retour à la liste</a>
        </div>
    </div>
    
    <?php if (isset($error)): ?>
        <div class="alert alert-error">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>
    
    <form action="index.php?page=admin&action=createCategory" method="post" class="admin-form">
        <div class="form-group">
            <label for="libelle">Libellé *</label>
            <input type="text" id="libelle" name="libelle" value="<?php echo isset($old['libelle']) ? htmlspecialchars($old['libelle']) : ''; ?>" required>
        </div>
        
        <div class="form-group">
            <label for="description">Description</label>
            <textarea id="description" name="description" rows="5"><?php echo isset($old['description']) ? htmlspecialchars($old['description']) : ''; ?></textarea>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn-admin btn-submit">Créer la catégorie</button>
            <a href="index.php?page=admin&action=listCategories" class="btn-admin btn-cancel">Annuler</a>
        </div>
    </form>
</div> 