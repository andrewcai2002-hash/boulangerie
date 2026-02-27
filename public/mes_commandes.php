<?php
$page_title = 'Mes commandes';
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

require_login();

$user = current_user();

// Récupérer toutes les commandes de l'utilisateur connecté
$commandes = db_fetch_all(
    'SELECT c.*, 
        COUNT(ci.id) as nb_articles,
        SUM(ci.quantite) as nb_produits
     FROM commandes c
     LEFT JOIN commande_items ci ON ci.commande_id = c.id
     WHERE c.user_id = ?
     GROUP BY c.id
     ORDER BY c.date_commande DESC',
    [$user['id']]
);

// Détail d'une commande spécifique
$commande_detail = null;
$items_detail = [];

if (isset($_GET['id'])) {
    $commande_id = (int)$_GET['id'];

    // Vérifier que la commande appartient bien à l'utilisateur connecté
    $commande_detail = db_fetch(
        'SELECT * FROM commandes WHERE id = ? AND user_id = ?',
        [$commande_id, $user['id']]
    );

    if ($commande_detail) {
        $items_detail = db_fetch_all(
            'SELECT ci.*, p.nom, p.image, p.description
             FROM commande_items ci
             JOIN produits p ON ci.produit_id = p.id
             WHERE ci.commande_id = ?',
            [$commande_id]
        );
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="container">
    <h1>Mes commandes</h1>

    <?php if ($commande_detail): ?>
        <!-- Vue détail d'une commande -->
        <div class="commande-detail-header">
            <a href="<?php echo PUBLIC_URL; ?>/mes_commandes.php" class="btn btn-secondary">← Retour à mes commandes</a>
        </div>

        <div class="commande-detail-box">
            <div class="commande-detail-meta">
                <h2>Commande n°<?php echo $commande_detail['id']; ?></h2>
                <div class="commande-meta-grid">
                    <div class="meta-item">
                        <span class="meta-label">Date</span>
                        <span class="meta-value"><?php echo format_date($commande_detail['date_commande']); ?></span>
                    </div>
                    <div class="meta-item">
                        <span class="meta-label">Statut</span>
                        <span class="meta-value statut-badge statut-<?php echo $commande_detail['statut']; ?>">
                            <?php echo format_statut($commande_detail['statut']); ?>
                        </span>
                    </div>
                    <div class="meta-item">
                        <span class="meta-label">Total</span>
                        <span class="meta-value prix-total"><?php echo format_price($commande_detail['total']); ?></span>
                    </div>
                    <div class="meta-item">
                        <span class="meta-label">Adresse de livraison</span>
                        <span class="meta-value"><?php echo e($commande_detail['adresse_livraison']); ?></span>
                    </div>
                    <?php if ($commande_detail['commentaire']): ?>
                    <div class="meta-item">
                        <span class="meta-label">Commentaire</span>
                        <span class="meta-value"><?php echo e($commande_detail['commentaire']); ?></span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <h3>Articles commandés</h3>
            <table class="cart-table">
                <thead>
                    <tr>
                        <th>Produit</th>
                        <th>Prix unitaire</th>
                        <th>Quantité</th>
                        <th>Sous-total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items_detail as $item): ?>
                    <tr>
                        <td>
                            <div class="produit-cell">
                                <?php if ($item['image']): ?>
                                    <img src="<?php echo ASSETS_WEB_PATH; ?>/img/produits/<?php echo e($item['image']); ?>"
                                         alt="<?php echo e($item['nom']); ?>"
                                         class="produit-thumb">
                                <?php endif; ?>
                                <span><?php echo e($item['nom']); ?></span>
                            </div>
                        </td>
                        <td><?php echo format_price($item['prix_unitaire']); ?></td>
                        <td><?php echo $item['quantite']; ?></td>
                        <td><?php echo format_price($item['prix_unitaire'] * $item['quantite']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3"><strong>Total</strong></td>
                        <td><strong><?php echo format_price($commande_detail['total']); ?></strong></td>
                    </tr>
                </tfoot>
            </table>

            <div class="commande-detail-actions">
                <a href="<?php echo PUBLIC_URL; ?>/catalogue.php" class="btn btn-primary">Commander à nouveau</a>
            </div>
        </div>

    <?php elseif (empty($commandes)): ?>
        <!-- Aucune commande -->
        <div class="empty-cart">
            <p>Vous n'avez pas encore passé de commande.</p>
            <a href="<?php echo PUBLIC_URL; ?>/catalogue.php" class="btn btn-primary">Découvrir notre catalogue</a>
        </div>

    <?php else: ?>
        <!-- Liste des commandes -->
        <p class="commandes-intro">Bonjour <strong><?php echo e($user['prenom']); ?></strong>, voici l'historique de vos commandes.</p>

        <div class="commandes-list">
            <?php foreach ($commandes as $commande): ?>
            <div class="commande-card">
                <div class="commande-card-header">
                    <div class="commande-card-id">
                        <strong>Commande n°<?php echo $commande['id']; ?></strong>
                        <span class="commande-date"><?php echo format_date($commande['date_commande']); ?></span>
                    </div>
                    <span class="statut-badge statut-<?php echo $commande['statut']; ?>">
                        <?php echo format_statut($commande['statut']); ?>
                    </span>
                </div>
                <div class="commande-card-body">
                    <div class="commande-card-info">
                        <span><?php echo (int)$commande['nb_produits']; ?> article<?php echo $commande['nb_produits'] > 1 ? 's' : ''; ?></span>
                        <span class="commande-total"><?php echo format_price($commande['total']); ?></span>
                    </div>
                    <a href="<?php echo PUBLIC_URL; ?>/mes_commandes.php?id=<?php echo $commande['id']; ?>" class="btn btn-sm btn-primary">
                        Voir le détail
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>