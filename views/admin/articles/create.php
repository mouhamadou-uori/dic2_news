<div class="admin-container">
    <div class="admin-header">
        <h1>Créer un nouvel article</h1>
        <div class="admin-actions">
            <a href="index.php?page=admin&action=listArticles" class="btn-admin">Retour à la liste</a>
        </div>
    </div>
    
    <?php if (isset($error)): ?>
        <div class="alert alert-error">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>
    
    <form action="index.php?page=admin&action=createArticle" method="post" class="admin-form">
        <div class="form-group">
            <label for="titre">Titre *</label>
            <input type="text" id="titre" name="titre" value="<?php echo isset($old['titre']) ? htmlspecialchars($old['titre']) : ''; ?>" required>
        </div>
        
        <div class="form-group">
            <label for="categorie">Catégorie *</label>
            <select id="categorie" name="categorie" required>
                <option value="">Sélectionner une catégorie</option>
                <?php foreach ($categories as $categorie): ?>
                    <option value="<?php echo $categorie->getId(); ?>" <?php echo (isset($old['categorie']) && $old['categorie'] == $categorie->getId()) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($categorie->getLibelle()); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="form-group">
            <label for="contenu">Contenu *</label>
            <textarea id="contenu" name="contenu" rows="15" required><?php echo isset($old['contenu']) ? htmlspecialchars($old['contenu']) : ''; ?></textarea>
        </div>
        
        <div class="form-group">
            <label for="statut">Statut</label>
            <select id="statut" name="statut">
                <option value="brouillon" <?php echo (!isset($old['statut']) || $old['statut'] == 'brouillon') ? 'selected' : ''; ?>>Brouillon</option>
                <option value="publie" <?php echo (isset($old['statut']) && $old['statut'] == 'publie') ? 'selected' : ''; ?>>Publié</option>
                <option value="archive" <?php echo (isset($old['statut']) && $old['statut'] == 'archive') ? 'selected' : ''; ?>>Archivé</option>
            </select>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn-admin btn-submit">Créer l'article</button>
            <a href="index.php?page=admin&action=listArticles" class="btn-admin btn-cancel">Annuler</a>
        </div>
    </form>
</div> 