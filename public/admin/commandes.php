<?php
$page_title = 'Gestion des commandes';
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/functions.php';

require_admin();

// Traitement du changement de statut
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['commande_id'])) {
    $commande_id = (int)$_POST['commande_id'];
    $nouveau_statut = $_POST['nouveau_statut'] ?? '';
    
    $statuts_valides = ['en_attente', 'prete', 'livree'];
    if (in_array($nouveau_statut, $statuts_valides)) {
        db_query(
            'UPDATE commandes SET statut = ? WHERE id = ?',
            [$nouveau_statut, $commande_id]
        );
    }
    redirect(ADMIN_URL . '/commandes.php');
}

// Récupérer les commandes avec filtre optionnel
$statut_filtre = $_GET['statut'] ?? null;
$statuts_valides = ['en_attente', 'prete', 'livree'];

if ($statut_filtre && !in_array($statut_filtre, $statuts_valides)) {
    $statut_filtre = null;
}

if ($statut_filtre) {
    $commandes = db_fetch_all(
        'SELECT c.*, u.prenom, u.nom 
         FROM commandes c 
         JOIN users u ON c.user_id = u.id 
         WHERE c.statut = ? 
         ORDER BY c.date_commande DESC',
        [$statut_filtre]
    );
} else {
    $commandes = db_fetch_all(
        'SELECT c.*, u.prenom, u.nom 
         FROM commandes c 
         JOIN users u ON c.user_id = u.id 
         ORDER BY c.date_commande DESC'
    );
}

require_once __DIR__ . '/../../includes/header.php';
?>

<div class="container admin-container">
    <h1>Gestion des commandes</h1>

    <!-- Filtres -->
    <div class="admin-filters">
        <a href="<?php echo ADMIN_URL; ?>/commandes.php" class="btn <?php echo $statut_filtre === null ? 'btn-primary' : 'btn-secondary'; ?>">
            Toutes (<?php echo db_fetch('SELECT COUNT(*) as count FROM commandes')['count'] ?? 0; ?>)
        </a>
        <?php foreach ($statuts_valides as $statut): ?>
            <a href="<?php echo ADMIN_URL; ?>/commandes.php?statut=<?php echo $statut; ?>" 
               class="btn <?php echo $statut_filtre === $statut ? 'btn-primary' : 'btn-secondary'; ?>">
                <?php 
                $count = db_fetch('SELECT COUNT(*) as count FROM commandes WHERE statut = ?', [$statut])['count'] ?? 0;
                echo format_statut($statut) . ' (' . $count . ')';
                ?>
            </a>
        <?php endforeach; ?>
    </div>

    <!-- Tableau des commandes -->
    <?php if ($commandes): ?>
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
            <?php foreach ($commandes as $commande): ?>
            <tr>
                <td><strong><?php echo $commande['id']; ?></strong></td>
                <td><?php echo e($commande['prenom'] . ' ' . $commande['nom']); ?></td>
                <td><?php echo format_date($commande['date_commande']); ?></td>
                <td>
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="commande_id" value="<?php echo $commande['id']; ?>">
                        <select name="nouveau_statut" onchange="this.form.submit()">
                            <option value="en_attente" <?php echo $commande['statut'] === 'en_attente' ? 'selected' : ''; ?>>En attente</option>
                            <option value="prete" <?php echo $commande['statut'] === 'prete' ? 'selected' : ''; ?>>Prête à livrer</option>
                            <option value="livree" <?php echo $commande['statut'] === 'livree' ? 'selected' : ''; ?>>Livrée</option>
                        </select>
                    </form>
                </td>
                <td><?php echo format_price($commande['total']); ?></td>
                <td>
                    <a href="<?php echo ADMIN_URL; ?>/commande_detail.php?id=<?php echo $commande['id']; ?>" class="btn btn-sm btn-primary">Détails</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php else: ?>
    <p>Aucune commande trouvée.</p>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>