<?php
$page_title = 'Récapitulatif de commande';
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

// Obligation d'être connecté
require_login();

// Initialiser le panier si vide
panier_init();
$panier = panier_get();

// Rediriger si panier vide
if (empty($panier)) {
    redirect(PUBLIC_URL . '/panier.php');
}

$user = current_user();
$total = panier_total();
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Créer la commande
        db_query(
            'INSERT INTO commandes (user_id, total, adresse_livraison, commentaire, statut) 
             VALUES (?, ?, ?, ?, ?)',
            [
                $user['id'],
                $total,
                $user['adresse'] ?? 'Non spécifiée',
                $_POST['commentaire'] ?? '',
                'en_attente'
            ]
        );

        $commande_id = db_lastInsertId();

        // Ajouter les items à la commande
        foreach ($panier as $produit_id => $item) {
            db_query(
                'INSERT INTO commande_items (commande_id, produit_id, quantite, prix_unitaire) 
                 VALUES (?, ?, ?, ?)',
                [
                    $commande_id,
                    $produit_id,
                    $item['quantite'],
                    $item['prix']
                ]
            );
        }

        // Vider le panier
        panier_clear();

        // Rediriger vers confirmation
        redirect(PUBLIC_URL . '/commande_confirmation.php?id=' . $commande_id);
    } catch (Exception $e) {
        $error = 'Erreur lors de la création de la commande. Veuillez réessayer.';
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="container">
    <h1>Récapitulatif de votre commande</h1>

    <?php if ($error): ?>
        <div class="alert alert-error"><?php echo e($error); ?></div>
    <?php endif; ?>

    <div class="recap-layout">
        <!-- Informations client -->
        <section class="recap-section">
            <h2>Informations de livraison</h2>
            <div class="info-block">
                <p><strong>Nom:</strong> <?php echo e($user['prenom'] . ' ' . $user['nom']); ?></p>
                <p><strong>Email:</strong> <?php echo e($user['email']); ?></p>
                <p><strong>Adresse:</strong> <?php echo e($user['adresse'] ?? 'Non spécifiée'); ?></p>
                <p><strong>Téléphone:</strong> <?php echo e($user['telephone'] ?? 'Non spécifié'); ?></p>
            </div>
        </section>

        <!-- Récapitulatif des produits -->
        <section class="recap-section">
            <h2>Votre commande</h2>
            <table class="recap-table">
                <thead>
                    <tr>
                        <th>Produit</th>
                        <th>Prix unitaire</th>
                        <th>Quantité</th>
                        <th>Sous-total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($panier as $item): ?>
                    <tr>
                        <td><?php echo e($item['nom']); ?></td>
                        <td><?php echo format_price($item['prix']); ?></td>
                        <td><?php echo $item['quantite']; ?></td>
                        <td><?php echo format_price($item['prix'] * $item['quantite']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="recap-total">
                <strong>Total: <?php echo format_price($total); ?></strong>
                <p class="payment-note">Paiement à la livraison</p>
            </div>
        </section>

        <!-- Formulaire de confirmation -->
        <section class="recap-section">
            <h2>Commentaire de livraison</h2>
            <form method="POST" action="">
                <div class="form-group">
                    <label for="commentaire">Ajouter un commentaire (optionnel)</label>
                    <textarea id="commentaire" name="commentaire" rows="4" placeholder="Ex: Sonner à la porte, laisser le colis sur le paillasson..."></textarea>
                </div>

                <div class="form-actions">
                    <a href="<?php echo PUBLIC_URL; ?>/panier.php" class="btn btn-secondary">Retour au panier</a>
                    <button type="submit" class="btn btn-primary btn-lg">Confirmer ma commande</button>
                </div>
            </form>
        </section>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
