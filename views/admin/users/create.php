<div class="admin-container">
    <div class="admin-header">
        <h1>Créer un nouvel utilisateur</h1>
        <div class="admin-actions">
            <a href="index.php?page=admin&action=listUsers" class="btn-admin">Retour à la liste</a>
        </div>
    </div>
    
    <?php if (isset($error)): ?>
        <div class="alert alert-error">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>
    
    <form action="index.php?page=admin&action=createUser" method="post" class="admin-form">
        <div class="form-group">
            <label for="username">Nom d'utilisateur *</label>
            <input type="text" id="username" name="username" value="<?php echo isset($old['username']) ? htmlspecialchars($old['username']) : ''; ?>" required>
        </div>
        
        <div class="form-group">
            <label for="email">Email *</label>
            <input type="email" id="email" name="email" value="<?php echo isset($old['email']) ? htmlspecialchars($old['email']) : ''; ?>" required>
        </div>
        
        <div class="form-group">
            <label for="password">Mot de passe *</label>
            <input type="password" id="password" name="password" required>
        </div>
        
        <div class="form-group">
            <label for="password_confirm">Confirmer le mot de passe *</label>
            <input type="password" id="password_confirm" name="password_confirm" required>
        </div>
        
        <div class="form-group">
            <label for="nom">Nom *</label>
            <input type="text" id="nom" name="nom" value="<?php echo isset($old['nom']) ? htmlspecialchars($old['nom']) : ''; ?>" required>
        </div>
        
        <div class="form-group">
            <label for="prenom">Prénom *</label>
            <input type="text" id="prenom" name="prenom" value="<?php echo isset($old['prenom']) ? htmlspecialchars($old['prenom']) : ''; ?>" required>
        </div>
        
        <div class="form-group">
            <label for="role">Rôle *</label>
            <select id="role" name="role" required>
                <option value="">Sélectionner un rôle</option>
                <option value="visiteur" <?php echo (isset($old['role']) && $old['role'] == 'visiteur') ? 'selected' : ''; ?>>Visiteur</option>
                <option value="editeur" <?php echo (isset($old['role']) && $old['role'] == 'editeur') ? 'selected' : ''; ?>>Éditeur</option>
                <option value="administrateur" <?php echo (isset($old['role']) && $old['role'] == 'administrateur') ? 'selected' : ''; ?>>Administrateur</option>
            </select>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn-admin btn-submit">Créer l'utilisateur</button>
            <a href="index.php?page=admin&action=listUsers" class="btn-admin btn-cancel">Annuler</a>
        </div>
    </form>
</div> 