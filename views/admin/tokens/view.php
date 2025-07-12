<div class="container-fluid">
    <!-- En-tête -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0"><?= htmlspecialchars($pageTitle) ?></h1>
                    <p class="text-muted mb-0">Détails et gestion du jeton d'authentification</p>
                </div>
                <div class="btn-group" role="group">
                    <a href="index.php?page=admin&action=listTokens" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Retour à la liste
                    </a>
                    <?php if ($token->getIsActive()): ?>
                        <a href="index.php?page=admin&action=revokeToken&id=<?= $token->getId() ?>" 
                           class="btn btn-warning"
                           onclick="return confirm('Êtes-vous sûr de vouloir révoquer ce jeton ?')">
                            <i class="fas fa-ban"></i> Révoquer
                        </a>
                    <?php endif; ?>
                    <a href="index.php?page=admin&action=deleteToken&id=<?= $token->getId() ?>" 
                       class="btn btn-danger"
                       onclick="return confirm('Êtes-vous sûr de vouloir supprimer définitivement ce jeton ? Cette action est irréversible.')">
                        <i class="fas fa-trash"></i> Supprimer
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Informations principales -->
        <div class="col-lg-8">
            <!-- Carte d'informations du token -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-info-circle"></i> Informations du jeton
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <dl class="row">
                                <dt class="col-sm-4">
                                    <i class="fas fa-hashtag text-primary"></i> ID
                                </dt>
                                <dd class="col-sm-8">
                                    <span class="badge bg-secondary fs-6">#<?= htmlspecialchars($token->getId()) ?></span>
                                </dd>
                                
                                <dt class="col-sm-4">
                                    <i class="fas fa-tag text-info"></i> Type
                                </dt>
                                <dd class="col-sm-8">
                                    <?php
                                    $typeColors = [
                                        'api' => 'bg-info',
                                        'login' => 'bg-primary',
                                        'reset_password' => 'bg-warning',
                                        'email_verification' => 'bg-success'
                                    ];
                                    $typeColor = $typeColors[$token->getType()] ?? 'bg-secondary';
                                    $typeIcons = [
                                        'api' => 'fa-code',
                                        'login' => 'fa-sign-in-alt',
                                        'reset_password' => 'fa-key',
                                        'email_verification' => 'fa-envelope'
                                    ];
                                    $typeIcon = $typeIcons[$token->getType()] ?? 'fa-tag';
                                    ?>
                                    <span class="badge <?= $typeColor ?> fs-6">
                                        <i class="fas <?= $typeIcon ?>"></i>
                                        <?= htmlspecialchars(ucfirst($token->getType())) ?>
                                    </span>
                                </dd>
                                
                                <dt class="col-sm-4">
                                    <i class="fas fa-shield-alt text-success"></i> Statut
                                </dt>
                                <dd class="col-sm-8">
                                    <span class="badge <?= $token->getStatusClass() ?> fs-6">
                                        <i class="fas fa-<?= $token->getIsActive() ? ($token->isExpired() ? 'clock' : 'check') : 'ban' ?>"></i>
                                        <?= htmlspecialchars($token->getStatusText()) ?>
                                    </span>
                                </dd>
                                
                                <dt class="col-sm-4">
                                    <i class="fas fa-calendar-plus text-primary"></i> Créé le
                                </dt>
                                <dd class="col-sm-8">
                                    <div class="text-muted">
                                        <i class="fas fa-calendar-alt"></i>
                                        <?= date('d/m/Y H:i:s', strtotime($token->getCreatedAt())) ?>
                                    </div>
                                </dd>
                            </dl>
                        </div>
                        
                        <div class="col-md-6">
                            <dl class="row">
                                <dt class="col-sm-4">
                                    <i class="fas fa-clock text-warning"></i> Expire le
                                </dt>
                                <dd class="col-sm-8">
                                    <?php if ($token->getExpiresAt()): ?>
                                        <div class="<?= $token->isExpired() ? 'text-danger' : 'text-success' ?>">
                                            <i class="fas fa-calendar-alt"></i>
                                            <?= date('d/m/Y H:i:s', strtotime($token->getExpiresAt())) ?>
                                            <?php if ($token->isExpired()): ?>
                                                <br><small class="text-danger">(Expiré)</small>
                                            <?php else: ?>
                                                <br><small class="text-success">(Valide)</small>
                                            <?php endif; ?>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-success">
                                            <i class="fas fa-infinity"></i> Permanent
                                            <br><small>(N'expire jamais)</small>
                                        </span>
                                    <?php endif; ?>
                                </dd>
                                
                                <dt class="col-sm-4">
                                    <i class="fas fa-history text-info"></i> Dernière utilisation
                                </dt>
                                <dd class="col-sm-8">
                                    <?php if ($token->getLastUsed()): ?>
                                        <div class="text-muted">
                                            <i class="fas fa-clock"></i>
                                            <?= date('d/m/Y H:i:s', strtotime($token->getLastUsed())) ?>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-muted">
                                            <i class="fas fa-minus"></i> Jamais utilisée
                                        </span>
                                    <?php endif; ?>
                                </dd>
                                
                                <dt class="col-sm-4">
                                    <i class="fas fa-globe text-secondary"></i> User Agent
                                </dt>
                                <dd class="col-sm-8">
                                    <?php if ($token->getUserAgent()): ?>
                                        <small class="text-muted"><?= htmlspecialchars($token->getUserAgent()) ?></small>
                                    <?php else: ?>
                                        <span class="text-muted">Non spécifié</span>
                                    <?php endif; ?>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Carte du token complet -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-key"></i> Token complet
                    </h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Attention :</strong> Ce token est sensible. Ne le partagez qu'avec les personnes autorisées.
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label">
                            <i class="fas fa-copy"></i> Token (cliquez pour copier)
                        </label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="tokenValue" 
                                   value="<?= htmlspecialchars($token->getToken()) ?>" readonly>
                            <button class="btn btn-outline-secondary" type="button" onclick="copyToken()">
                                <i class="fas fa-copy"></i> Copier
                            </button>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label">
                            <i class="fas fa-code"></i> Format pour les requêtes API
                        </label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="apiFormat" 
                                   value="Authorization: Bearer <?= htmlspecialchars($token->getToken()) ?>" readonly>
                            <button class="btn btn-outline-secondary" type="button" onclick="copyApiFormat()">
                                <i class="fas fa-copy"></i> Copier
                            </button>
                        </div>
                    </div>

                    <!-- Exemples d'utilisation -->
                    <div class="mt-4">
                        <h6><i class="fas fa-lightbulb"></i> Exemples d'utilisation</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title">cURL</h6>
                                        <pre class="mb-0"><code>curl -X GET "<?= $_SERVER['HTTP_HOST'] ?>/api/test.php" \
  -H "Authorization: Bearer <?= htmlspecialchars($token->getToken()) ?>"</code></pre>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title">JavaScript</h6>
                                        <pre class="mb-0"><code>fetch('/api/test.php', {
  headers: {
    'Authorization': 'Bearer <?= htmlspecialchars($token->getToken()) ?>'
  }
})</code></pre>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Panneau latéral -->
        <div class="col-lg-4">
            <!-- Informations utilisateur -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-user"></i> Utilisateur associé
                    </h6>
                </div>
                <div class="card-body text-center">
                    <!-- <?php 
                    $userDao = new UserDao($GLOBALS['db']);
                    $user = $userDao->getById($token->getUserId());
                    ?> -->
                    
                    <?php if ($user): ?>
                        <div class="mb-3">
                            <div class="avatar-lg mx-auto mb-3">
                                <i class="fas fa-user-circle fa-4x text-primary"></i>
                            </div>
                            <h5><?= htmlspecialchars($user->getNom() . ' ' . $user->getPrenom()) ?></h5>
                            <p class="text-muted mb-2">@<?= htmlspecialchars($user->getUsername()) ?></p>
                            <p class="text-muted mb-3"><?= htmlspecialchars($user->getEmail()) ?></p>
                            <span class="badge bg-secondary fs-6"><?= htmlspecialchars($user->getRole()) ?></span>
                        </div>
                        
                        <div class="d-grid">
                            <a href="index.php?page=admin&action=editUserForm&id=<?= $user->getId() ?>" 
                               class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-edit"></i> Modifier l'utilisateur
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="text-center text-muted">
                            <i class="fas fa-user-slash fa-4x mb-3"></i>
                            <h6>Utilisateur introuvable</h6>
                            <p>L'utilisateur associé à ce token n'existe plus.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Actions rapides -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-bolt"></i> Actions rapides
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <?php if ($token->getIsActive()): ?>
                            <button class="btn btn-outline-warning" onclick="revokeToken()">
                                <i class="fas fa-ban"></i> Révoquer le jeton
                            </button>
                        <?php else: ?>
                            <button class="btn btn-outline-success" onclick="reactivateToken()">
                                <i class="fas fa-check"></i> Réactiver le jeton
                            </button>
                        <?php endif; ?>
                        
                        <button class="btn btn-outline-info" onclick="testToken()">
                            <i class="fas fa-vial"></i> Tester le jeton
                        </button>
                        
                        <button class="btn btn-outline-secondary" onclick="showTokenHistory()">
                            <i class="fas fa-history"></i> Historique d'utilisation
                        </button>
                    </div>
                </div>
            </div>

            <!-- Statistiques d'utilisation -->
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-chart-bar"></i> Statistiques
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="border-end">
                                <h4 class="text-primary mb-0">
                                    <?= $token->getLastUsed() ? '1' : '0' ?>
                                </h4>
                                <small class="text-muted">Utilisations</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <h4 class="text-info mb-0">
                                <?php 
                                if ($token->getExpiresAt()) {
                                    $created = new DateTime($token->getCreatedAt());
                                    $expires = new DateTime($token->getExpiresAt());
                                    $total = $created->diff($expires)->days;
                                    echo $total;
                                } else {
                                    echo '∞';
                                }
                                ?>
                            </h4>
                            <small class="text-muted">Jours de validité</small>
                        </div>
                    </div>
                    
                    <?php if ($token->getExpiresAt() && !$token->isExpired()): ?>
                        <div class="mt-3">
                            <small class="text-muted">Temps restant :</small>
                            <div class="progress mt-1" style="height: 6px;">
                                <?php
                                $created = new DateTime($token->getCreatedAt());
                                $expires = new DateTime($token->getExpiresAt());
                                $now = new DateTime();
                                $total = $created->diff($expires)->days;
                                $elapsed = $created->diff($now)->days;
                                $percentage = 0;
                                if ($total > 0) {
                                    $percentage = min(100, max(0, ($elapsed / $total) * 100));
                                } else {
                                    $percentage = $token->isExpired() ? 0 : 100;
                                }
                                ?>
                                <div class="progress-bar bg-success" style="width: <?= $percentage ?>%"></div>
                            </div>
                            <small class="text-muted">
                                <?= $total - $elapsed ?> jours restants
                            </small>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.card {
    border: none;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    transition: box-shadow 0.15s ease-in-out;
}

.card:hover {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
}

.avatar-lg {
    width: 80px;
    height: 80px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
}

.badge {
    font-size: 0.75em;
}

.badge.fs-6 {
    font-size: 0.875em !important;
}

pre {
    background-color: #f8f9fa;
    border: 1px solid #e9ecef;
    border-radius: 0.375rem;
    padding: 0.75rem;
    font-size: 0.875em;
    margin: 0;
}

code {
    color: #e83e8c;
}

.progress {
    background-color: #e9ecef;
}

.btn-group .btn {
    border-radius: 0.375rem !important;
}

.btn-group .btn:not(:last-child) {
    margin-right: 0.25rem;
}

.form-control:focus {
    border-color: #86b7fe;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}

.alert {
    border: none;
    border-radius: 0.5rem;
}

dl.row dt {
    font-weight: 500;
    color: #495057;
}

dl.row dd {
    color: #6c757d;
}
</style>

<script>
// Copie de token
function copyToken() {
    const tokenInput = document.getElementById('tokenValue');
    tokenInput.select();
    tokenInput.setSelectionRange(0, 99999);
    document.execCommand('copy');
    
    // Feedback visuel
    const button = event.target.closest('button');
    const originalHTML = button.innerHTML;
    button.innerHTML = '<i class="fas fa-check"></i> Copié !';
    button.classList.remove('btn-outline-secondary');
    button.classList.add('btn-success');
    
    setTimeout(() => {
        button.innerHTML = originalHTML;
        button.classList.remove('btn-success');
        button.classList.add('btn-outline-secondary');
    }, 2000);
}

// Copie du format API
function copyApiFormat() {
    const apiInput = document.getElementById('apiFormat');
    apiInput.select();
    apiInput.setSelectionRange(0, 99999);
    document.execCommand('copy');
    
    // Feedback visuel
    const button = event.target.closest('button');
    const originalHTML = button.innerHTML;
    button.innerHTML = '<i class="fas fa-check"></i> Copié !';
    button.classList.remove('btn-outline-secondary');
    button.classList.add('btn-success');
    
    setTimeout(() => {
        button.innerHTML = originalHTML;
        button.classList.remove('btn-success');
        button.classList.add('btn-outline-secondary');
    }, 2000);
}

// Actions rapides
function revokeToken() {
    if (confirm('Êtes-vous sûr de vouloir révoquer ce jeton ?')) {
        window.location.href = 'index.php?page=admin&action=revokeToken&id=<?= $token->getId() ?>';
    }
}

function reactivateToken() {
    alert('Fonctionnalité de réactivation à implémenter');
}


</script> 