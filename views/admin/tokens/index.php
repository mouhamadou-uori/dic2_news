<div class="container-fluid">
    <!-- En-tête avec statistiques -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0"><?= htmlspecialchars($pageTitle) ?></h1>
                    <p class="text-muted mb-0">Gérez les jetons d'authentification pour les services web</p>
                </div>
                <a href="index.php?page=admin&action=createTokenForm" class="btn btn-primary btn-create-token">
                    <i class="fas fa-plus"></i> Créer un jeton
                </a>
            </div>
        </div>
    </div>

    <!-- Statistiques -->
    <?php 
    $totalTokens = count($tokens);
    $activeTokens = 0;
    $expiredTokens = 0;
    $revokedTokens = 0;
    
    foreach ($tokens as $token) {
        if (!$token->getIsActive()) {
            $revokedTokens++;
        } elseif ($token->isExpired()) {
            $expiredTokens++;
        } else {
            $activeTokens++;
        }
    }
    ?>
    
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0"><?= $totalTokens ?></h4>
                            <p class="mb-0">Total</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-key fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0"><?= $activeTokens ?></h4>
                            <p class="mb-0">Actifs</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-check-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0"><?= $expiredTokens ?></h4>
                            <p class="mb-0">Expirés</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-clock fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0"><?= $revokedTokens ?></h4>
                            <p class="mb-0">Révokés</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-ban fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Messages d'alerte -->
    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i>
            <?= htmlspecialchars($_SESSION['success_message']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle"></i>
            <?= htmlspecialchars($_SESSION['error_message']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>

    <!-- Filtres et recherche -->
    <div class="card mb-4">
        <div class="card-body">
            <form class="row g-3 align-items-end flex-wrap flex-md-nowrap" id="filtersForm" onsubmit="return false;">
                <div class="col-md-4 col-12">
                    <label for="searchInput" class="form-label">Rechercher</label>
                    <input type="text" class="form-control" id="searchInput" placeholder="Rechercher par utilisateur, type...">
                </div>
                <div class="col-md-3 col-12">
                    <label for="statusFilter" class="form-label">Statut</label>
                    <select class="form-select" id="statusFilter">
                        <option value="">Tous les statuts</option>
                        <option value="active">Actifs</option>
                        <option value="expired">Expirés</option>
                        <option value="revoked">Révokés</option>
                    </select>
                </div>
                <div class="col-md-3 col-12">
                    <label for="typeFilter" class="form-label">Type</label>
                    <select class="form-select" id="typeFilter">
                        <option value="">Tous les types</option>
                        <option value="api">API</option>
                        <option value="login">Connexion</option>
                        <option value="reset_password">Reset mot de passe</option>
                        <option value="email_verification">Vérification email</option>
                    </select>
                </div>
                <div class="col-md-2 col-12 d-flex justify-content-md-end justify-content-start mt-2 mt-md-0">
                    <button type="button" class="btn btn-outline-secondary w-100" style="min-width:150px;" onclick="resetFilters()">
                        <i class="fas fa-undo"></i> Réinitialiser
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Liste des tokens -->
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="fas fa-list"></i> Jetons d'authentification
                </h5>
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="exportTokens()">
                        <i class="fas fa-download"></i> Exporter
                    </button>
                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="refreshTable()">
                        <i class="fas fa-sync-alt"></i> Actualiser
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <?php if (empty($tokens)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-key fa-4x text-muted mb-3"></i>
                    <h5 class="text-muted">Aucun jeton d'authentification trouvé</h5>
                    <p class="text-muted">Commencez par créer votre premier jeton d'authentification</p>
                    <a href="index.php?page=admin&action=createTokenForm" class="btn btn-primary btn-create-token">
                        <i class="fas fa-plus"></i> Créer le premier jeton
                    </a>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="tokensTable">
                        <thead class="table-light">
                            <tr>
                                <th>
                                    <input type="checkbox" class="form-check-input" id="selectAll">
                                </th>
                                <th>ID</th>
                                <th>Utilisateur</th>
                                <th>Type</th>
                                <th>Token</th>
                                <th>Statut</th>
                                <th>Expire le</th>
                                <th>Créé le</th>
                                <th>Dernière utilisation</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($tokens as $token): ?>
                                <?php 
                                $userDao = new UserDao($GLOBALS['db']);
                                $user = $userDao->getById($token->getUserId());
                                ?>
                                <tr class="token-row" 
                                    data-status="<?= $token->getIsActive() ? ($token->isExpired() ? 'expired' : 'active') : 'revoked' ?>"
                                    data-type="<?= htmlspecialchars($token->getType()) ?>"
                                    data-user="<?= $user ? htmlspecialchars(strtolower($user->getUsername())) : '' ?>">
                                    <td>
                                        <input type="checkbox" class="form-check-input token-checkbox" value="<?= $token->getId() ?>">
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">#<?= htmlspecialchars($token->getId()) ?></span>
                                    </td>
                                    <td>
                                        <?php if ($user): ?>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm me-2">
                                                    <i class="fas fa-user-circle fa-lg text-primary"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-bold"><?= htmlspecialchars($user->getUsername()) ?></div>
                                                    <small class="text-muted"><?= htmlspecialchars($user->getNom() . ' ' . $user->getPrenom()) ?></small>
                                                </div>
                                            </div>
                                        <?php else: ?>
                                            <span class="text-muted">Utilisateur inconnu</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php
                                        $typeColors = [
                                            'api' => 'bg-info',
                                            'login' => 'bg-primary',
                                            'reset_password' => 'bg-warning',
                                            'email_verification' => 'bg-success'
                                        ];
                                        $typeColor = $typeColors[$token->getType()] ?? 'bg-secondary';
                                        ?>
                                        <span class="badge <?= $typeColor ?>">
                                            <i class="fas fa-<?= $token->getType() === 'api' ? 'code' : ($token->getType() === 'login' ? 'sign-in-alt' : ($token->getType() === 'reset_password' ? 'key' : 'envelope')) ?>"></i>
                                            <?= htmlspecialchars(ucfirst($token->getType())) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="token-display">
                                            <code class="text-muted"><?= htmlspecialchars($token->getMaskedToken()) ?></code>
                                            <button class="btn btn-sm btn-outline-secondary ms-2" onclick="copyToken('<?= htmlspecialchars($token->getToken()) ?>')" title="Copier le token">
                                                <i class="fas fa-copy"></i>
                                            </button>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge <?= $token->getStatusClass() ?>">
                                            <i class="fas fa-<?= $token->getIsActive() ? ($token->isExpired() ? 'clock' : 'check') : 'ban' ?>"></i>
                                            <?= htmlspecialchars($token->getStatusText()) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($token->getExpiresAt()): ?>
                                            <div class="<?= $token->isExpired() ? 'text-danger' : '' ?>">
                                                <i class="fas fa-calendar-alt"></i>
                                                <?= date('d/m/Y H:i', strtotime($token->getExpiresAt())) ?>
                                            </div>
                                        <?php else: ?>
                                            <span class="text-muted">
                                                <i class="fas fa-infinity"></i> Permanent
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="text-muted">
                                            <i class="fas fa-calendar-plus"></i>
                                            <?= date('d/m/Y H:i', strtotime($token->getCreatedAt())) ?>
                                        </div>
                                    </td>
                                    <td>
                                        <?php if ($token->getLastUsed()): ?>
                                            <div class="text-muted">
                                                <i class="fas fa-clock"></i>
                                                <?= date('d/m/Y H:i', strtotime($token->getLastUsed())) ?>
                                            </div>
                                        <?php else: ?>
                                            <span class="text-muted">
                                                <i class="fas fa-minus"></i> Jamais
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="index.php?page=admin&action=viewToken&id=<?= $token->getId() ?>" 
                                               class="btn btn-sm btn-outline-primary" title="Voir les détails">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            
                                            <?php if ($token->getIsActive()): ?>
                                                <a href="index.php?page=admin&action=revokeToken&id=<?= $token->getId() ?>" 
                                                   class="btn btn-sm btn-outline-warning" 
                                                   onclick="return confirm('Êtes-vous sûr de vouloir révoquer ce jeton ?')"
                                                   title="Révoquer le jeton">
                                                    <i class="fas fa-ban"></i>
                                                </a>
                                            <?php endif; ?>
                                            
                                            <a href="index.php?page=admin&action=deleteToken&id=<?= $token->getId() ?>" 
                                               class="btn btn-sm btn-outline-danger" 
                                               onclick="return confirm('Êtes-vous sûr de vouloir supprimer définitivement ce jeton ? Cette action est irréversible.')"
                                               title="Supprimer définitivement">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Actions en lot -->
                <div class="card-footer">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted">
                                <span id="selectedCount">0</span> jeton(s) sélectionné(s)
                            </span>
                        </div>
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-outline-warning btn-sm" onclick="revokeSelected()" disabled id="revokeSelectedBtn">
                                <i class="fas fa-ban"></i> Révoquer sélectionnés
                            </button>
                            <button type="button" class="btn btn-outline-danger btn-sm" onclick="deleteSelected()" disabled id="deleteSelectedBtn">
                                <i class="fas fa-trash"></i> Supprimer sélectionnés
                            </button>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.avatar-sm {
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.token-display {
    display: flex;
    align-items: center;
}

.token-display code {
    font-size: 0.85em;
    background: #f8f9fa;
    padding: 2px 6px;
    border-radius: 4px;
}

.table th {
    border-top: none;
    font-weight: 600;
    color: #495057;
}

.table td {
    vertical-align: middle;
}

.card {
    border: none;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
}

.btn-group .btn {
    border-radius: 0.25rem !important;
}

.btn-group .btn:not(:last-child) {
    margin-right: 0.25rem;
}
</style>

<script>
// Filtrage et recherche
document.getElementById('searchInput').addEventListener('input', filterTokens);
document.getElementById('statusFilter').addEventListener('change', filterTokens);
document.getElementById('typeFilter').addEventListener('change', filterTokens);

function filterTokens() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const statusFilter = document.getElementById('statusFilter').value;
    const typeFilter = document.getElementById('typeFilter').value;
    
    const rows = document.querySelectorAll('.token-row');
    
    rows.forEach(row => {
        const user = row.getAttribute('data-user');
        const status = row.getAttribute('data-status');
        const type = row.getAttribute('data-type');
        
        const matchesSearch = user.includes(searchTerm);
        const matchesStatus = !statusFilter || status === statusFilter;
        const matchesType = !typeFilter || type === typeFilter;
        
        if (matchesSearch && matchesStatus && matchesType) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

function resetFilters() {
    document.getElementById('searchInput').value = '';
    document.getElementById('statusFilter').value = '';
    document.getElementById('typeFilter').value = '';
    filterTokens();
}

// Sélection en lot
document.getElementById('selectAll').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.token-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
    });
    updateSelectedCount();
});

document.querySelectorAll('.token-checkbox').forEach(checkbox => {
    checkbox.addEventListener('change', updateSelectedCount);
});

function updateSelectedCount() {
    const selectedCount = document.querySelectorAll('.token-checkbox:checked').length;
    document.getElementById('selectedCount').textContent = selectedCount;
    
    const hasSelection = selectedCount > 0;
    document.getElementById('revokeSelectedBtn').disabled = !hasSelection;
    document.getElementById('deleteSelectedBtn').disabled = !hasSelection;
}

// Actions en lot
function revokeSelected() {
    const selectedIds = Array.from(document.querySelectorAll('.token-checkbox:checked')).map(cb => cb.value);
    if (selectedIds.length === 0) return;
    
    if (confirm(`Êtes-vous sûr de vouloir révoquer ${selectedIds.length} jeton(s) ?`)) {
        // Implémenter la révocation en lot
        console.log('Révoquer:', selectedIds);
    }
}

function deleteSelected() {
    const selectedIds = Array.from(document.querySelectorAll('.token-checkbox:checked')).map(cb => cb.value);
    if (selectedIds.length === 0) return;
    
    if (confirm(`Êtes-vous sûr de vouloir supprimer définitivement ${selectedIds.length} jeton(s) ? Cette action est irréversible.`)) {
        // Implémenter la suppression en lot
        console.log('Supprimer:', selectedIds);
    }
}

// Copie de token
function copyToken(token) {
    navigator.clipboard.writeText(token).then(() => {
        // Feedback visuel
        const button = event.target.closest('button');
        const originalHTML = button.innerHTML;
        button.innerHTML = '<i class="fas fa-check"></i>';
        button.classList.remove('btn-outline-secondary');
        button.classList.add('btn-success');
        
        setTimeout(() => {
            button.innerHTML = originalHTML;
            button.classList.remove('btn-success');
            button.classList.add('btn-outline-secondary');
        }, 2000);
    });
}

// Autres fonctions
function exportTokens() {
    alert('Fonctionnalité d\'export à implémenter');
}

function refreshTable() {
    location.reload();
}

// Initialisation
document.addEventListener('DOMContentLoaded', function() {
    updateSelectedCount();
});
</script> 