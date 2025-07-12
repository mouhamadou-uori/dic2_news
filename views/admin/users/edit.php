<div class="admin-container">
    <div class="admin-header">
        <h1>Modifier l'utilisateur</h1>
        <div class="admin-actions">
            <a href="index.php?page=admin&action=listUsers" class="btn-admin">Retour à la liste</a>
        </div>
    </div>
    
    <?php if (isset($error)): ?>
        <div class="alert alert-error">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>
    
    <form action="index.php?page=admin&action=updateUser&id=<?php echo $user->getId(); ?>" method="post" class="admin-form">
        <div class="form-group">
            <label for="username">Nom d'utilisateur *</label>
            <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user->getUsername()); ?>" required>
        </div>
        
        <div class="form-group">
            <label for="email">Email *</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user->getEmail()); ?>" required>
        </div>
        
        <div class="form-group">
            <label for="password">Nouveau mot de passe</label>
            <input type="password" id="password" name="password">
            <small>Laissez vide pour conserver le mot de passe actuel</small>
        </div>
        
        <div class="form-group">
            <label for="password_confirm">Confirmer le nouveau mot de passe</label>
            <input type="password" id="password_confirm" name="password_confirm">
        </div>
        
        <div class="form-group">
            <label for="nom">Nom *</label>
            <input type="text" id="nom" name="nom" value="<?php echo htmlspecialchars($user->getNom()); ?>" required>
        </div>
        
        <div class="form-group">
            <label for="prenom">Prénom *</label>
            <input type="text" id="prenom" name="prenom" value="<?php echo htmlspecialchars($user->getPrenom()); ?>" required>
        </div>
        
        <div class="form-group">
            <label for="role">Rôle *</label>
            <select id="role" name="role" required <?php echo ($user->getId() == $_SESSION['user_id']) ? 'disabled' : ''; ?>>
                <option value="visiteur" <?php echo ($user->getRole() == 'visiteur') ? 'selected' : ''; ?>>Visiteur</option>
                <option value="editeur" <?php echo ($user->getRole() == 'editeur') ? 'selected' : ''; ?>>Éditeur</option>
                <option value="administrateur" <?php echo ($user->getRole() == 'administrateur') ? 'selected' : ''; ?>>Administrateur</option>
            </select>
            <?php if ($user->getId() == $_SESSION['user_id']): ?>
                <input type="hidden" name="role" value="<?php echo htmlspecialchars($user->getRole()); ?>">
                <small>Vous ne pouvez pas modifier votre propre rôle</small>
            <?php endif; ?>
        </div>
        
        <div class="form-group">
            <label>Informations complémentaires</label>
            <div class="info-box">
                <p><strong>Date de création:</strong> <?php echo date('d/m/Y H:i', strtotime($user->getDateCreation())); ?></p>
                <?php if ($user->getDateModification()): ?>
                    <p><strong>Dernière modification:</strong> <?php echo date('d/m/Y H:i', strtotime($user->getDateModification())); ?></p>
                <?php endif; ?>
                <?php if ($user->getDerniereConnexion()): ?>
                    <p><strong>Dernière connexion:</strong> <?php echo date('d/m/Y H:i', strtotime($user->getDerniereConnexion())); ?></p>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn-admin btn-submit">Enregistrer les modifications</button>
            <a href="index.php?page=admin&action=listUsers" class="btn-admin btn-cancel">Annuler</a>
        </div>
    </form>
</div> 