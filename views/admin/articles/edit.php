<div class="admin-container">
    <div class="admin-header">
        <h1>Modifier l'article</h1>
        <div class="admin-actions">
            <a href="index.php?page=admin&action=listArticles" class="btn-admin">Retour à la liste</a>
        </div>
    </div>
    
    <?php if (isset($error)): ?>
        <div class="alert alert-error">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>
    
    <form action="index.php?page=admin&action=updateArticle&id=<?php echo $article->getId(); ?>" method="post" class="admin-form">
        <div class="form-group">
            <label for="titre">Titre *</label>
            <input type="text" id="titre" name="titre" value="<?php echo htmlspecialchars($article->getTitre()); ?>" required>
        </div>
        
        <div class="form-group">
            <label for="categorie">Catégorie *</label>
            <select id="categorie" name="categorie" required>
                <option value="">Sélectionner une catégorie</option>
                <?php foreach ($categories as $categorie): ?>
                    <option value="<?php echo $categorie->getId(); ?>" <?php echo ($article->getCategorie() == $categorie->getId()) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($categorie->getLibelle()); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="form-group">
            <label for="contenu">Contenu *</label>
            <textarea id="contenu" name="contenu" rows="15" required><?php echo htmlspecialchars($article->getContenu()); ?></textarea>
        </div>
        
        <div class="form-group">
            <label for="statut">Statut</label>
            <select id="statut" name="statut">
                <option value="brouillon" <?php echo ($article->getStatut() == 'brouillon') ? 'selected' : ''; ?>>Brouillon</option>
                <option value="publie" <?php echo ($article->getStatut() == 'publie') ? 'selected' : ''; ?>>Publié</option>
                <option value="archive" <?php echo ($article->getStatut() == 'archive') ? 'selected' : ''; ?>>Archivé</option>
            </select>
        </div>
        
        <div class="form-group">
            <label>Informations complémentaires</label>
            <div class="info-box">
                <p><strong>Auteur:</strong> <?php echo htmlspecialchars($article->getAuteurNom()); ?></p>
                <p><strong>Date de création:</strong> <?php echo date('d/m/Y H:i', strtotime($article->getDateCreation())); ?></p>
                <?php if ($article->getDateModification()): ?>
                    <p><strong>Dernière modification:</strong> <?php echo date('d/m/Y H:i', strtotime($article->getDateModification())); ?></p>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn-admin btn-submit">Enregistrer les modifications</button>
            <a href="index.php?page=admin&action=listArticles" class="btn-admin btn-cancel">Annuler</a>
        </div>
    </form>
</div> 