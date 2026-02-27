<?php
require_once 'includes/config.php';
$pageTitle = 'Mes commandes';

if (!isLoggedIn()) redirect('login.php');

$commandes = $pdo->prepare("
    SELECT c.*, 
           GROUP_CONCAT(p.nom, ' ×', ci.quantite ORDER BY p.nom SEPARATOR ', ') AS produits_liste
    FROM commandes c
    JOIN commande_items ci ON ci.commande_id = c.id
    JOIN produits p ON p.id = ci.produit_id
    WHERE c.utilisateur_id = ?
    GROUP BY c.id
    ORDER BY c.created_at DESC
");
$commandes->execute([$_SESSION['user_id']]);
$commandes = $commandes->fetchAll();

$badges = [
    'en_attente' => 'badge-attente',
    'prete'      => 'badge-prete',
    'livree'     => 'badge-livree',
    'annulee'    => 'badge-annulee',
];
$labels = [
    'en_attente' => 'En préparation',
    'prete'      => 'Prête à livrer',
    'livree'     => 'Livrée',
    'annulee'    => 'Annulée',
];
?>
<?php include 'includes/header.php'; ?>

<h1 class="page-title">Mes commandes</h1>

<?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success">✅ Votre commande a bien été enregistrée ! Nous vous contacterons pour confirmer la livraison.</div>
<?php endif; ?>

<?php if (empty($commandes)): ?>
    <div class="alert alert-info">Vous n'avez pas encore passé de commande. <a href="catalogue.php">Voir le catalogue</a></div>
<?php else: ?>
    <?php foreach ($commandes as $c): ?>
    <div class="order-card">
        <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:8px">
            <h3>Commande #<?= $c['id'] ?> — <?= date('d/m/Y à H:i', strtotime($c['created_at'])) ?></h3>
            <span class="badge <?= $badges[$c['statut']] ?>"><?= $labels[$c['statut']] ?></span>
        </div>
        <p style="margin:10px 0;color:#555"><?= sanitize($c['produits_liste']) ?></p>
        <p><strong>Total :</strong> <?= number_format($c['total'], 2, ',', ' ') ?> € — <strong>Livraison :</strong> <?= sanitize($c['adresse_livraison']) ?></p>
        <?php if ($c['commentaire']): ?>
            <p style="font-size:.9rem;color:#888;margin-top:6px">Note : <?= sanitize($c['commentaire']) ?></p>
        <?php endif; ?>
    </div>
    <?php endforeach; ?>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>
