<?php
$page_title = 'Confirmation de commande';
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/header.php';

$commande_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($commande_id > 0) {
    $user_id = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;
    $commande = db_fetch('SELECT * FROM commandes WHERE id = ? AND user_id = ?', [$commande_id, $user_id]);
} else {
    $commande = null;
}
?>

<div class="container">
    <div class="confirmation-box">
        <?php if ($commande): ?>
            <div class="success-message">
                <h1>✓ Commande confirmée!</h1>
                <p>Votre commande n°<strong><?php echo $commande['id']; ?></strong> a bien été enregistrée.</p>
            </div>

            <div class="confirmation-details">
                <p><strong>Date de commande:</strong> <?php echo format_date($commande['date_commande']); ?></p>
                <p><strong>Statut:</strong> <span class="badge"><?php echo format_statut($commande['statut']); ?></span></p>
                <p><strong>Total:</strong> <?php echo format_price($commande['total']); ?></p>
                <p class="payment-info">Le paiement se fera à la livraison.</p>
            </div>

            <div class="next-steps">
                <h2>Prochaines étapes</h2>
                <ol>
                    <li>Nous préparons votre commande</li>
                    <li>Nous vous confirmerons la date de livraison par email</li>
                    <li>Vous recevrez vos produits à l'adresse indiquée</li>
                    <li>Paiement à la livraison</li>
                </ol>
            </div>

            <div class="confirmation-actions">
                <a href="<?php echo PUBLIC_URL; ?>/catalogue.php" class="btn btn-primary">Continuer mes achats</a>
                <a href="<?php echo PUBLIC_URL; ?>/index.php" class="btn btn-secondary">Retour à l'accueil</a>
            </div>
        <?php else: ?>
            <div class="error-message">
                <h1>Commande non trouvée</h1>
                <p>Nous n'avons pas pu trouver votre commande. Veuillez vérifier l'ID fourni.</p>
                <a href="<?php echo PUBLIC_URL; ?>/index.php" class="btn btn-primary">Retour à l'accueil</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>