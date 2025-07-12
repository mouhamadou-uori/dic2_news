<div class="admin-container">
    <div class="admin-header">
        <h1>Modifier la catégorie</h1>
        <div class="admin-actions">
            <a href="index.php?page=admin&action=listCategories" class="btn-admin">Retour à la liste</a>
        </div>
    </div>
    
    <?php if (isset($error)): ?>
        <div class="alert alert-error">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>
    
    <form action="index.php?page=admin&action=updateCategory&id=<?php echo $category->getId(); ?>" method="post" class="admin-form">
        <div class="form-group">
            <label for="libelle">Libellé *</label>
            <input type="text" id="libelle" name="libelle" value="<?php echo htmlspecialchars($category->getLibelle()); ?>" required>
        </div>
        
        <div class="form-group">
            <label for="description">Description</label>
            <textarea id="description" name="description" rows="5"><?php echo $category->getDescription() ? htmlspecialchars($category->getDescription()) : ''; ?></textarea>
        </div>
        
        <div class="form-group">
            <label>Informations complémentaires</label>
            <div class="info-box">
                <?php if ($category->getCreatedBy()): ?>
                    <p><strong>Créée par:</strong> ID <?php echo $category->getCreatedBy(); ?></p>
                <?php endif; ?>
                <?php if ($category->getDateCreation()): ?>
                    <p><strong>Date de création:</strong> <?php echo date('d/m/Y H:i', strtotime($category->getDateCreation())); ?></p>
                <?php endif; ?>
                <?php if ($category->getDateModification()): ?>
                    <p><strong>Dernière modification:</strong> <?php echo date('d/m/Y H:i', strtotime($category->getDateModification())); ?></p>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn-admin btn-submit">Enregistrer les modifications</button>
            <a href="index.php?page=admin&action=listCategories" class="btn-admin btn-cancel">Annuler</a>
        </div>
    </form>
</div> 