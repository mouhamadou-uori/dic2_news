<div class="container-fluid">
    <!-- En-tête -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0 admin-page-title"><?= htmlspecialchars($pageTitle) ?></h1>
                    <p class="text-muted mb-0">Créez un nouveau jeton d'authentification pour les services web</p>
                </div>
                <a href="index.php?page=admin&action=listTokens" class="btn btn-secondary me-2 btn-back-list">
                    <i class="fas fa-arrow-left"></i> Retour à la liste
                </a>
            </div>
        </div>
    </div>

    <!-- Messages d'erreur -->
    <?php if ($error): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle"></i>
            <?= htmlspecialchars($error) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row">
        <!-- Formulaire principal -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-plus-circle"></i> Informations du jeton
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="index.php?page=admin&action=createToken" id="createTokenForm">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="user_id" class="form-label">
                                        <i class="fas fa-user"></i> Utilisateur *
                                    </label>
                                    <select class="form-select" id="user_id" name="user_id" required>
                                        <option value="">Sélectionnez un utilisateur</option>
                                        <?php foreach ($users as $user): ?>
                                            <option value="<?= $user->getId() ?>" 
                                                    <?= (isset($old['user_id']) && $old['user_id'] == $user->getId()) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($user->getUsername()) ?> 
                                                (<?= htmlspecialchars($user->getNom() . ' ' . $user->getPrenom()) ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="form-text">L'utilisateur pour lequel ce jeton sera créé.</div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="type" class="form-label">
                                        <i class="fas fa-tag"></i> Type de jeton *
                                    </label>
                                    <select class="form-select" id="type" name="type" required>
                                        <option value="api" <?= (isset($old['type']) && $old['type'] == 'api') ? 'selected' : '' ?>>API</option>
                                        <option value="login" <?= (isset($old['type']) && $old['type'] == 'login') ? 'selected' : '' ?>>Connexion</option>
                                        <option value="reset_password" <?= (isset($old['type']) && $old['type'] == 'reset_password') ? 'selected' : '' ?>>Réinitialisation de mot de passe</option>
                                        <option value="email_verification" <?= (isset($old['type']) && $old['type'] == 'email_verification') ? 'selected' : '' ?>>Vérification d'email</option>
                                    </select>
                                    <div class="form-text">Le type de jeton détermine son utilisation.</div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="expires_in" class="form-label">
                                        <i class="fas fa-clock"></i> Durée de validité
                                    </label>
                                    <select class="form-select" id="expires_in" name="expires_in">
                                        <option value="+1 hour" <?= (isset($old['expires_in']) && $old['expires_in'] == '+1 hour') ? 'selected' : '' ?>>1 heure</option>
                                        <option value="+1 day" <?= (isset($old['expires_in']) && $old['expires_in'] == '+1 day') ? 'selected' : '' ?>>1 jour</option>
                                        <option value="+7 days" <?= (isset($old['expires_in']) && $old['expires_in'] == '+7 days') ? 'selected' : '' ?>>7 jours</option>
                                        <option value="+30 days" <?= (isset($old['expires_in']) && $old['expires_in'] == '+30 days') ? 'selected' : '' ?>>30 jours</option>
                                        <option value="+90 days" <?= (isset($old['expires_in']) && $old['expires_in'] == '+90 days') ? 'selected' : '' ?>>90 jours</option>
                                        <option value="+1 year" <?= (isset($old['expires_in']) && $old['expires_in'] == '+1 year') ? 'selected' : '' ?>>1 an</option>
                                        <option value="never" <?= (isset($old['expires_in']) && $old['expires_in'] == 'never') ? 'selected' : '' ?>>Jamais (permanent)</option>
                                    </select>
                                    <div class="form-text">La durée après laquelle le jeton expirera automatiquement.</div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">
                                        <i class="fas fa-calendar-alt"></i> Date d'expiration calculée
                                    </label>
                                    <div class="form-control-plaintext" id="calculatedExpiry">
                                        <span class="text-muted">Sélectionnez une durée pour voir la date d'expiration</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="description" class="form-label">
                                        <i class="fas fa-sticky-note"></i> Description (optionnel)
                                    </label>
                                    <textarea class="form-control" id="description" name="description" rows="3"
                                              placeholder="Description du jeton (ex: Token pour l'API mobile, Token de développement...)"
                                              maxlength="255" style="width:100%; min-width:100%; max-width:100%; display:block;"><?= isset($old['description']) ? htmlspecialchars($old['description']) : '' ?></textarea>
                                    <div class="form-text">Une description pour identifier facilement ce jeton.</div>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between mt-4 mb-2">
                            <a href="index.php?page=admin&action=listTokens" class="btn btn-secondary me-2">
                                <i class="fas fa-times"></i> Annuler
                            </a>
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <i class="fas fa-plus"></i> Créer le jeton
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Panneau d'informations -->
        <div class="col-lg-4">
            <!-- Informations sur les types -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-info-circle"></i> Types de jetons
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex align-items-center mb-2">
                            <span class="badge bg-info me-2">API</span>
                            <small class="text-muted">Services web et intégrations</small>
                        </div>
                        <small class="text-muted">Pour les applications tierces, API REST, etc.</small>
                    </div>
                    
                    <div class="mb-3">
                        <div class="d-flex align-items-center mb-2">
                            <span class="badge bg-primary me-2">Connexion</span>
                            <small class="text-muted">Sessions web</small>
                        </div>
                        <small class="text-muted">Pour les connexions via navigateur web.</small>
                    </div>
                    
                    <div class="mb-3">
                        <div class="d-flex align-items-center mb-2">
                            <span class="badge bg-warning me-2">Reset mot de passe</span>
                            <small class="text-muted">Réinitialisation</small>
                        </div>
                        <small class="text-muted">Pour la réinitialisation de mots de passe.</small>
                    </div>
                    
                    <div class="mb-0">
                        <div class="d-flex align-items-center mb-2">
                            <span class="badge bg-success me-2">Vérification email</span>
                            <small class="text-muted">Confirmation</small>
                        </div>
                        <small class="text-muted">Pour la vérification d'adresses email.</small>
                    </div>
                </div>
            </div>

            <!-- Recommandations de sécurité -->
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-shield-alt"></i> Recommandations de sécurité
                    </h6>
                </div>
                <div class="card-body">
                    <div class="alert alert-info mb-3">
                        <h6 class="alert-heading">
                            <i class="fas fa-lightbulb"></i> Bonnes pratiques
                        </h6>
                        <ul class="mb-0 small">
                            <li>Choisissez une durée appropriée selon l'usage</li>
                            <li>Les tokens API peuvent être plus longs</li>
                            <li>Les tokens de reset doivent être courts</li>
                            <li>Révoke les tokens compromis immédiatement</li>
                        </ul>
                    </div>
                    
                    <div class="alert alert-warning mb-0">
                        <h6 class="alert-heading">
                            <i class="fas fa-exclamation-triangle"></i> Important
                        </h6>
                        <ul class="mb-0 small">
                            <li>Le token sera généré automatiquement</li>
                            <li>Il ne sera affiché qu'une seule fois</li>
                            <li>Notez-le dans un endroit sécurisé</li>
                            <li>Ne le partagez qu'avec les personnes autorisées</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.card {
    border: none;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
}

.form-control-plaintext {
    background-color: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
    padding: 0.375rem 0.75rem;
}

.badge {
    font-size: 0.75em;
}

.alert {
    border: none;
    border-radius: 0.5rem;
}

.alert-heading {
    font-size: 0.875rem;
    font-weight: 600;
}

.form-label {
    font-weight: 500;
    color: #495057;
}

.form-text {
    font-size: 0.875em;
}
</style>

<script>
// Calcul de la date d'expiration
document.getElementById('expires_in').addEventListener('change', function() {
    const expiresIn = this.value;
    const calculatedExpiry = document.getElementById('calculatedExpiry');
    
    if (expiresIn === 'never') {
        calculatedExpiry.innerHTML = '<span class="text-success"><i class="fas fa-infinity"></i> Token permanent (n\'expire jamais)</span>';
    } else {
        const now = new Date();
        const expiryDate = new Date(now.getTime() + getDurationInMs(expiresIn));
        
        calculatedExpiry.innerHTML = `
            <span class="text-primary">
                <i class="fas fa-calendar-check"></i> 
                ${expiryDate.toLocaleDateString('fr-FR')} à ${expiryDate.toLocaleTimeString('fr-FR', {hour: '2-digit', minute: '2-digit'})}
            </span>
        `;
    }
});

function getDurationInMs(duration) {
    const now = new Date();
    const future = new Date(now.getTime());
    
    if (duration.includes('hour')) {
        future.setHours(future.getHours() + parseInt(duration));
    } else if (duration.includes('day')) {
        future.setDate(future.getDate() + parseInt(duration));
    } else if (duration.includes('year')) {
        future.setFullYear(future.getFullYear() + parseInt(duration));
    }
    
    return future.getTime() - now.getTime();
}

// Validation en temps réel
document.getElementById('createTokenForm').addEventListener('submit', function(e) {
    const userId = document.getElementById('user_id').value;
    const type = document.getElementById('type').value;
    
    if (!userId) {
        e.preventDefault();
        showError('Veuillez sélectionner un utilisateur.');
        return;
    }
    
    if (!type) {
        e.preventDefault();
        showError('Veuillez sélectionner un type de jeton.');
        return;
    }
    
    // Désactiver le bouton pour éviter les doubles soumissions
    document.getElementById('submitBtn').disabled = true;
    document.getElementById('submitBtn').innerHTML = '<i class="fas fa-spinner fa-spin"></i> Création en cours...';
});

function showError(message) {
    // Supprimer les anciennes alertes
    const existingAlert = document.querySelector('.alert-danger');
    if (existingAlert) {
        existingAlert.remove();
    }
    
    // Créer une nouvelle alerte
    const alert = document.createElement('div');
    alert.className = 'alert alert-danger alert-dismissible fade show';
    alert.innerHTML = `
        <i class="fas fa-exclamation-triangle"></i>
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    // Insérer l'alerte après l'en-tête
    const header = document.querySelector('.row.mb-4');
    header.parentNode.insertBefore(alert, header.nextSibling);
    
    // Scroll vers l'alerte
    alert.scrollIntoView({ behavior: 'smooth', block: 'center' });
}

// Prévisualisation du type sélectionné
document.getElementById('type').addEventListener('change', function() {
    const type = this.value;
    const typeInfo = {
        'api': 'Pour les services web et intégrations tierces',
        'login': 'Pour les connexions via navigateur web',
        'reset_password': 'Pour la réinitialisation de mots de passe',
        'email_verification': 'Pour la vérification d\'adresses email'
    };
    
    // Mettre à jour l'info-bulle du type
    const typeSelect = this;
    const formText = typeSelect.parentNode.querySelector('.form-text');
    if (typeInfo[type]) {
        formText.textContent = typeInfo[type];
    }
});

// Initialisation
document.addEventListener('DOMContentLoaded', function() {
    // Déclencher le calcul de la date d'expiration au chargement
    const expiresInSelect = document.getElementById('expires_in');
    if (expiresInSelect.value) {
        expiresInSelect.dispatchEvent(new Event('change'));
    }
    
    // Déclencher la prévisualisation du type au chargement
    const typeSelect = document.getElementById('type');
    if (typeSelect.value) {
        typeSelect.dispatchEvent(new Event('change'));
    }
});
</script> 