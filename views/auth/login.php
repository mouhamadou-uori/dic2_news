<div class="auth-container">
    <h2>Connexion</h2>
    
    <?php if (isset($error)): ?>
        <div class="alert alert-error">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>
    
    <form action="index.php?page=login&action=submit" method="post" class="auth-form">
        <div class="form-group">
            <label for="username">Nom d'utilisateur ou Email</label>
            <input type="text" id="username" name="username" required>
        </div>
        
        <div class="form-group">
            <label for="password">Mot de passe</label>
            <input type="password" id="password" name="password" required>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn-auth btn-login">Se connecter</button>
        </div>
    </form>
    
    <div class="auth-links">
        <p>Vous n'avez pas de compte ? <a href="index.php?page=register">Inscrivez-vous</a></p>
    </div>
</div> 