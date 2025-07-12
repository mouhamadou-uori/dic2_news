<div class="admin-container">
    <div class="admin-header">
        <h1>Gestion des catégories</h1>
        <div class="admin-actions">
            <a href="index.php?page=admin&action=createCategoryForm" class="btn-admin">Nouvelle catégorie</a>
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
    
    <?php if (count($categories) > 0): ?>
        <div class="admin-table-container">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Libellé</th>
                        <th>Description</th>
                        <th>Date de création</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categories as $categorie): ?>
                        <tr>
                            <td><?php echo $categorie->getId(); ?></td>
                            <td><?php echo htmlspecialchars($categorie->getLibelle()); ?></td>
                            <td><?php echo $categorie->getDescription() ? htmlspecialchars($categorie->getDescription()) : '-'; ?></td>
                            <td><?php echo $categorie->getDateCreation() ? date('d/m/Y', strtotime($categorie->getDateCreation())) : '-'; ?></td>
                            <td class="actions">
                                <a href="index.php?page=admin&action=editCategoryForm&id=<?php echo $categorie->getId(); ?>" class="btn-action btn-edit" title="Modifier la catégorie">
                                    <span>Modifier</span>
                                </a>
                                <a href="javascript:void(0);" onclick="confirmDelete(<?php echo $categorie->getId(); ?>, '<?php echo addslashes(htmlspecialchars($categorie->getLibelle())); ?>')" class="btn-action btn-delete" title="Supprimer la catégorie">
                                    <span>Supprimer</span>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="no-items">
            <p>Aucune catégorie disponible pour le moment.</p>
            <a href="index.php?page=admin&action=createCategoryForm" class="btn-admin">Créer une catégorie</a>
        </div>
    <?php endif; ?>
</div>

<script>
function confirmDelete(id, libelle) {
    if (confirm("Êtes-vous sûr de vouloir supprimer la catégorie \"" + libelle + "\" ?\n\nAttention : Si des articles sont associés à cette catégorie, elle sera désactivée au lieu d'être supprimée.")) {
        window.location.href = "index.php?page=admin&action=deleteCategory&id=" + id;
    }
}
</script>