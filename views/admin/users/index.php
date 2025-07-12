<div class="admin-container">
    <div class="admin-header">
        <h1>Gestion des utilisateurs</h1>
        <div class="admin-actions">
            <a href="index.php?page=admin&action=createUserForm" class="btn-admin">Nouvel utilisateur</a>
            <a href="index.php?page=admin" class="btn-admin">Retour au tableau de bord</a>
        </div>
    </div>
    
    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success">
            <?php echo htmlspecialchars($_SESSION['success_message']); ?>
            <?php unset($_SESSION['success_message']); ?>
        </div>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-error">
            <?php echo htmlspecialchars($_SESSION['error_message']); ?>
            <?php unset($_SESSION['error_message']); ?>
        </div>
    <?php endif; ?>
    
    <?php if (count($users) > 0): ?>
        <div class="admin-table-container">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nom d'utilisateur</th>
                        <th>Email</th>
                        <th>Nom complet</th>
                        <th>Rôle</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?php echo $user->getId(); ?></td>
                            <td><?php echo htmlspecialchars($user->getUsername()); ?></td>
                            <td><?php echo htmlspecialchars($user->getEmail()); ?></td>
                            <td><?php echo htmlspecialchars($user->getPrenom() . ' ' . $user->getNom()); ?></td>
                            <td><?php echo ucfirst(htmlspecialchars($user->getRole())); ?></td>
                            <td><?php echo $user->isActif() ? 'Actif' : 'Inactif'; ?></td>
                            <td class="actions">
                                <a href="index.php?page=admin&action=editUserForm&id=<?php echo $user->getId(); ?>" class="btn-action btn-edit" title="Modifier l'utilisateur">
                                    <span>Modifier</span>
                                </a>
                                <?php if ($user->getId() != $_SESSION['user_id']): ?>
                                    <a href="javascript:void(0);" onclick="confirmDelete(<?php echo $user->getId(); ?>, '<?php echo addslashes(htmlspecialchars($user->getUsername())); ?>')" class="btn-action btn-delete" title="Supprimer l'utilisateur">
                                        <span>Supprimer</span>
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="no-items">
            <p>Aucun utilisateur disponible.</p>
            <a href="index.php?page=admin&action=createUserForm" class="btn-admin">Créer un utilisateur</a>
        </div>
    <?php endif; ?>
</div>

<script>
function confirmDelete(id, username) {
    if (confirm("Êtes-vous sûr de vouloir supprimer l'utilisateur \"" + username + "\" ?")) {
        window.location.href = "index.php?page=admin&action=deleteUser&id=" + id;
    }
}
</script> 