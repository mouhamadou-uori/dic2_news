<div class="auth-container">
    <h2>Inscription</h2>
    
    <?php if (isset($error)): ?>
        <div class="alert alert-error">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>
    
    <form action="index.php?page=register&action=submit" method="post" class="auth-form">
        <div class="form-group">
            <label for="username">Nom d'utilisateur</label>
            <input type="text" id="username" name="username" value="<?php echo isset($old['username']) ? htmlspecialchars($old['username']) : ''; ?>" required>
        </div>
        
        <div class="form-group">
            <label for="email">Adresse email</label>
            <input type="email" id="email" name="email" value="<?php echo isset($old['email']) ? htmlspecialchars($old['email']) : ''; ?>" required>
        </div>
        
        <div class="form-group">
            <label for="nom">Nom</label>
            <input type="text" id="nom" name="nom" value="<?php echo isset($old['nom']) ? htmlspecialchars($old['nom']) : ''; ?>" required>
        </div>
        
        <div class="form-group">
            <label for="prenom">Prénom</label>
            <input type="text" id="prenom" name="prenom" value="<?php echo isset($old['prenom']) ? htmlspecialchars($old['prenom']) : ''; ?>" required>
        </div>
        
        <div class="form-group">
            <label for="password">Mot de passe</label>
            <input type="password" id="password" name="password" required>
            <small>Le mot de passe doit contenir au moins 8 caractères.</small>
        </div>
        
        <div class="form-group">
            <label for="password_confirm">Confirmer le mot de passe</label>
            <input type="password" id="password_confirm" name="password_confirm" required>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn-auth btn-register">S'inscrire</button>
        </div>
    </form>
    
    <div class="auth-links">
        <p>Vous avez déjà un compte ? <a href="index.php?page=login">Connectez-vous</a></p>
    </div>
</div> 