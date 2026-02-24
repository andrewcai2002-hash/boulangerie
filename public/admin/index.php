<?php
$page_title = 'Dashboard Admin';
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/functions.php';

require_admin();

// Statistiques
$stats = db_fetch(
    'SELECT 
        COUNT(CASE WHEN statut = "en_attente" THEN 1 END) as nb_attente,
        COUNT(*) as nb_total,
        SUM(CASE WHEN statut = "livree" THEN total ELSE 0 END) as total_livree
    FROM commandes'
);

// 5 dernières commandes
$dernieres = db_fetch_all(
    'SELECT c.*, u.prenom, u.nom 
     FROM commandes c 
     JOIN users u ON c.user_id = u.id 
     ORDER BY c.date_commande DESC 
     LIMIT 5'
);

require_once __DIR__ . '/../../includes/header.php';
?>

<div class="container admin-container">
    <h1>Dashboard Admin</h1>

    <!-- Statistiques -->
    <section class="dashboard-stats">
        <div class="stat-card">
            <h3><?php echo $stats['nb_attente']; ?></h3>
            <p>Commandes en attente</p>
        </div>
        <div class="stat-card">
            <h3><?php echo $stats['nb_total']; ?></h3>
            <p>Commandes totales</p>
        </div>
        <div class="stat-card">
            <h3><?php echo format_price($stats['total_livree'] ?? 0); ?></h3>
            <p>Total livré</p>
        </div>
    </section>

    <!-- Navigation Admin -->
    <section class="admin-nav">
        <a href="<?php echo ADMIN_URL; ?>/commandes.php" class="btn btn-secondary">Gérer les commandes</a>
        <a href="<?php echo ADMIN_URL; ?>/produits.php" class="btn btn-secondary">Gérer les produits</a>
    </section>

    <!-- Dernières commandes -->
    <section class="recent-orders">
        <h2>Dernières commandes</h2>
        <?php if ($dernieres): ?>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Client</th>
                    <th>Date</th>
                    <th>Statut</th>
                    <th>Total</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($dernieres as $commande): ?>
                <tr>
                    <td><?php echo $commande['id']; ?></td>
                    <td><?php echo e($commande['prenom'] . ' ' . $commande['nom']); ?></td>
                    <td><?php echo format_date($commande['date_commande']); ?></td>
                    <td><span class="badge"><?php echo format_statut($commande['statut']); ?></span></td>
                    <td><?php echo format_price($commande['total']); ?></td>
                    <td>
                        <a href="<?php echo ADMIN_URL; ?>/commande_detail.php?id=<?php echo $commande['id']; ?>" class="btn btn-sm btn-primary">Détails</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
        <p>Aucune commande pour le moment.</p>
        <?php endif; ?>
    </section>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
