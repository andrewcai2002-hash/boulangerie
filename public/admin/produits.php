<?php
$page_title = 'Gestion des produits';
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/functions.php';

require_admin();

// Traitement toggle actif
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'toggle_active') {
    $produit_id = (int)$_POST['produit_id'];
    $produit = db_fetch('SELECT actif FROM produits WHERE id = ?', [$produit_id]);
    
    if ($produit) {
        $new_actif = $produit['actif'] ? 0 : 1;
        db_query('UPDATE produits SET actif = ? WHERE id = ?', [$new_actif, $produit_id]);
    }
    
    redirect(ADMIN_URL . '/produits.php');
}

// Suppression d'un produit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $produit_id = (int)$_POST['produit_id'];
    db_query('DELETE FROM produits WHERE id = ?', [$produit_id]);
    redirect(ADMIN_URL . '/produits.php');
}

// Récupérer tous les produits
$produits = db_fetch_all(
    'SELECT p.*, c.nom as categorie_nom 
     FROM produits p 
     JOIN categories c ON p.categorie_id = c.id 
     ORDER BY p.created_at DESC'
);

require_once __DIR__ . '/../../includes/header.php';
?>

<div class="container admin-container">
    <div class="products-header">
        <h1>Gestion des produits</h1>
        <a href="<?php echo ADMIN_URL; ?>/produit_edit.php" class="btn btn-primary btn-lg">+ Ajouter un produit</a>
    </div>

    <?php if ($produits): ?>
    <table class="admin-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Image</th>
                <th>Nom</th>
                <th>Catégorie</th>
                <th>Prix</th>
                <th>Actif</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($produits as $produit): ?>
            <tr>
                <td><?php echo $produit['id']; ?></td>
                <td>
                    <?php if ($produit['image']): ?>
                    <img src="<?php echo ASSETS_WEB_PATH; ?>/img/produits/<?php echo e($produit['image']); ?>" alt="" style="max-height: 50px; max-width: 50px;">
                    <?php else: ?>
                    <span class="text-muted">Pas d'image</span>
                    <?php endif; ?>
                </td>
                <td><?php echo e($produit['nom']); ?></td>
                <td><?php echo e($produit['categorie_nom']); ?></td>
                <td><?php echo format_price($produit['prix']); ?></td>
                <td>
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="action" value="toggle_active">
                        <input type="hidden" name="produit_id" value="<?php echo $produit['id']; ?>">
                        <button type="submit" class="btn btn-sm <?php echo $produit['actif'] ? 'btn-success' : 'btn-danger'; ?>">
                            <?php echo $produit['actif'] ? 'Actif' : 'Inactif'; ?>
                        </button>
                    </form>
                    <form method="POST" style="display: inline;" onsubmit="return confirm('Supprimer ce produit définitivement ?')">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="produit_id" value="<?php echo $produit['id']; ?>">
                        <button type="submit" class="btn btn-sm btn-danger">Supprimer</button>
                    </form>
                </td>
                <td>
                    <a href="<?php echo ADMIN_URL; ?>/produit_edit.php?id=<?php echo $produit['id']; ?>" class="btn btn-sm btn-secondary">Éditer</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php else: ?>
    <p>Aucun produit pour le moment.</p>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
