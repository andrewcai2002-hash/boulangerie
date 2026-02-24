<?php
$page_title = 'Détail de la commande';
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/functions.php';

require_admin();

$commande_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$commande_id) {
    http_response_code(404);
    die('Commande non trouvée.');
}

// Récupérer la commande
$commande = db_fetch(
    'SELECT c.*, u.prenom, u.nom, u.email, u.adresse, u.telephone 
     FROM commandes c 
     JOIN users u ON c.user_id = u.id 
     WHERE c.id = ?',
    [$commande_id]
);

if (!$commande) {
    http_response_code(404);
    die('Commande non trouvée.');
}

// Traitement du changement de statut
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nouveau_statut'])) {
    $nouveau_statut = $_POST['nouveau_statut'];
    $statuts_valides = ['en_attente', 'prete', 'livree'];
    
    if (in_array($nouveau_statut, $statuts_valides)) {
        db_query(
            'UPDATE commandes SET statut = ? WHERE id = ?',
            [$nouveau_statut, $commande_id]
        );
        redirect(ADMIN_URL . '/commande_detail.php?id=' . $commande_id . '&updated=1');
    }
}

// Récupérer les items de la commande
$items = db_fetch_all(
    'SELECT ci.*, p.nom, p.image 
     FROM commande_items ci 
     JOIN produits p ON ci.produit_id = p.id 
     WHERE ci.commande_id = ?',
    [$commande_id]
);

require_once __DIR__ . '/../../includes/header.php';
?>

<div class="container admin-container">
    <div class="detail-header">
        <h1>Commande n°<?php echo $commande['id']; ?></h1>
        <a href="<?php echo ADMIN_URL; ?>/commandes.php" class="btn btn-secondary">Retour</a>
    </div>

    <?php if (isset($_GET['updated'])): ?>
    <div class="alert alert-success">Statut mis à jour avec succès.</div>
    <?php endif; ?>

    <!-- Informations client -->
    <section class="detail-section">
        <h2>Informations client</h2>
        <div class="info-grid">
            <div>
                <strong>Nom:</strong> <?php echo e($commande['prenom'] . ' ' . $commande['nom']); ?>
            </div>
            <div>
                <strong>Email:</strong> <?php echo e($commande['email']); ?>
            </div>
            <div>
                <strong>Téléphone:</strong> <?php echo e($commande['telephone'] ?? 'Non spécifié'); ?>
            </div>
            <div>
                <strong>Adresse de livraison:</strong> <?php echo e($commande['adresse_livraison']); ?>
            </div>
        </div>
    </section>

    <!-- Informations commande -->
    <section class="detail-section">
        <h2>Détails de la commande</h2>
        <div class="info-grid">
            <div>
                <strong>Date:</strong> <?php echo format_date($commande['date_commande']); ?>
            </div>
            <div>
                <strong>Total:</strong> <?php echo format_price($commande['total']); ?>
            </div>
            <div>
                <strong>Commentaire:</strong> <?php echo e($commande['commentaire'] ?? 'Aucun'); ?>
            </div>
        </div>
    </section>

    <!-- Changement de statut -->
    <section class="detail-section">
        <h2>Statut de la commande</h2>
        <form method="POST">
            <div class="form-group">
                <label for="nouveau_statut">Nouvelle statut:</label>
                <select id="nouveau_statut" name="nouveau_statut" required>
                    <option value="en_attente" <?php echo $commande['statut'] === 'en_attente' ? 'selected' : ''; ?>>En attente</option>
                    <option value="prete" <?php echo $commande['statut'] === 'prete' ? 'selected' : ''; ?>>Prête à livrer</option>
                    <option value="livree" <?php echo $commande['statut'] === 'livree' ? 'selected' : ''; ?>>Livrée</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Mettre à jour le statut</button>
        </form>
    </section>

    <!-- Items de la commande -->
    <section class="detail-section">
        <h2>Articles commandés</h2>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Produit</th>
                    <th>Prix unitaire</th>
                    <th>Quantité</th>
                    <th>Sous-total</th>
                </tr>
            </thead>
            <tbody>
                <?php $sous_total = 0; ?>
                <?php foreach ($items as $item): ?>
                <tr>
                    <td><?php echo e($item['nom']); ?></td>
                    <td><?php echo format_price($item['prix_unitaire']); ?></td>
                    <td><?php echo $item['quantite']; ?></td>
                    <td><?php echo format_price($item['prix_unitaire'] * $item['quantite']); ?></td>
                </tr>
                <?php $sous_total += $item['prix_unitaire'] * $item['quantite']; ?>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3"><strong>Total:</strong></td>
                    <td><strong><?php echo format_price($sous_total); ?></strong></td>
                </tr>
            </tfoot>
        </table>
    </section>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
