<?php
$page_title = 'Panier';
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

panier_init();

$action = $_GET['action'] ?? 'view';

// Ajouter au panier
if ($action === 'add' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $produit_id = (int)($_POST['produit_id'] ?? 0);
    $quantite = (int)($_POST['quantite'] ?? 1);

    if ($produit_id > 0 && $quantite > 0) {
        $produit = db_fetch(
            'SELECT * FROM produits WHERE id = ? AND actif = 1',
            [$produit_id]
        );

        if ($produit) {
            panier_add($produit_id, $quantite, $produit);
            redirect(PUBLIC_URL . '/panier.php?added=1');
        }
    }
    redirect(PUBLIC_URL . '/catalogue.php');
}

// Mettre à jour le panier
if ($action === 'update' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $quantites = $_POST['quantite'] ?? [];

    foreach ($quantites as $produit_id => $quantite) {
        $quantite = (int)$quantite;
        panier_update((int)$produit_id, $quantite);
    }

    redirect(PUBLIC_URL . '/panier.php?updated=1');
}

// Vider le panier
if ($action === 'clear') {
    panier_clear();
    redirect(PUBLIC_URL . '/panier.php?cleared=1');
}

// Supprimer un article
if ($action === 'remove' && isset($_GET['id'])) {
    panier_remove((int)$_GET['id']);
    redirect(PUBLIC_URL . '/panier.php');
}

// Affichage du panier
require_once __DIR__ . '/../includes/header.php';
$panier = panier_get();
$total = panier_total();
$success_message = null;

if (isset($_GET['added'])) {
    $success_message = 'Produit ajouté au panier avec succès!';
} elseif (isset($_GET['updated'])) {
    $success_message = 'Panier mis à jour.';
} elseif (isset($_GET['cleared'])) {
    $success_message = 'Panier vidé.';
}
?>

<div class="container">
    <h1>Mon panier</h1>

    <?php if ($success_message): ?>
        <div class="alert alert-success"><?php echo $success_message; ?></div>
    <?php endif; ?>

    <?php if (empty($panier)): ?>
        <div class="empty-cart">
            <p>Votre panier est vide.</p>
            <a href="<?php echo PUBLIC_URL; ?>/catalogue.php" class="btn btn-primary">Continuer vos achats</a>
        </div>
    <?php else: ?>
        <form method="POST" action="<?php echo PUBLIC_URL; ?>/panier.php?action=update" class="cart-form">
            <table class="cart-table">
                <thead>
                    <tr>
                        <th>Produit</th>
                        <th>Prix unitaire</th>
                        <th>Quantité</th>
                        <th>Sous-total</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($panier as $produit_id => $item): ?>
                    <tr>
                        <td><?php echo e($item['nom']); ?></td>
                        <td><?php echo format_price($item['prix']); ?></td>
                        <td>
                            <input type="number" name="quantite[<?php echo $produit_id; ?>]" min="0" value="<?php echo $item['quantite']; ?>" class="quantity-small">
                        </td>
                        <td><?php echo format_price($item['prix'] * $item['quantite']); ?></td>
                        <td>
                            <a href="<?php echo PUBLIC_URL; ?>/panier.php?action=remove&id=<?php echo $produit_id; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Supprimer cet article?')">Supprimer</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="cart-summary">
                <div class="cart-total">
                    <strong>Total: <?php echo format_price($total); ?></strong>
                </div>
                <div class="cart-actions">
                    <button type="submit" class="btn btn-secondary">Mettre à jour le panier</button>
                    <a href="<?php echo PUBLIC_URL; ?>/panier.php?action=clear" class="btn btn-danger" onclick="return confirm('Vider tout le panier?')">Vider le panier</a>
                </div>
            </div>
        </form>

        <div class="checkout-section">
            <?php if (is_logged_in()): ?>
                <a href="<?php echo PUBLIC_URL; ?>/commande_recap.php" class="btn btn-primary btn-lg">Valider ma commande</a>
            <?php else: ?>
                <p>Vous devez être connecté pour passer commande.</p>
                <a href="<?php echo PUBLIC_URL; ?>/connexion.php" class="btn btn-primary">Se connecter</a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
