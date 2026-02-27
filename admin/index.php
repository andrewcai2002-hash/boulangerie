<?php
require_once 'guard.php';
$pageTitle = 'Administration';

$nb_commandes = $pdo->query("SELECT COUNT(*) FROM commandes WHERE statut='en_attente'")->fetchColumn();
$nb_produits  = $pdo->query("SELECT COUNT(*) FROM produits WHERE disponible=1")->fetchColumn();
$ca_total     = $pdo->query("SELECT SUM(total) FROM commandes WHERE statut='livree'")->fetchColumn();

$dernieres_commandes = $pdo->query("
    SELECT c.*, u.prenom, u.nom FROM commandes c
    JOIN utilisateurs u ON u.id = c.utilisateur_id
    ORDER BY c.created_at DESC LIMIT 5
")->fetchAll();

$badges = ['en_attente'=>'badge-attente','prete'=>'badge-prete','livree'=>'badge-livree','annulee'=>'badge-annulee'];
$labels = ['en_attente'=>'En préparation','prete'=>'Prête','livree'=>'Livrée','annulee'=>'Annulée'];
?>
<?php include '../includes/header.php'; ?>

<h1 class="page-title">🛠 Administration</h1>

<div class="admin-nav">
    <a href="index.php" class="btn">Tableau de bord</a>
    <a href="commandes.php" class="btn">Commandes</a>
    <a href="produits.php" class="btn">Produits</a>
    <a href="../index.php" class="btn btn-secondary">← Site</a>
</div>

<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:20px;margin-bottom:40px">
    <div style="background:#fff;padding:24px;border-radius:10px;text-align:center;box-shadow:0 2px 10px rgba(0,0,0,.08)">
        <div style="font-size:2.5rem;font-weight:700;color:#b5451b"><?= $nb_commandes ?></div>
        <div style="color:#666">Commandes en attente</div>
    </div>
    <div style="background:#fff;padding:24px;border-radius:10px;text-align:center;box-shadow:0 2px 10px rgba(0,0,0,.08)">
        <div style="font-size:2.5rem;font-weight:700;color:#3b1a0a"><?= $nb_produits ?></div>
        <div style="color:#666">Produits disponibles</div>
    </div>
    <div style="background:#fff;padding:24px;border-radius:10px;text-align:center;box-shadow:0 2px 10px rgba(0,0,0,.08)">
        <div style="font-size:2.5rem;font-weight:700;color:#27ae60"><?= number_format((float)$ca_total, 2, ',', ' ') ?> €</div>
        <div style="color:#666">CA livré total</div>
    </div>
</div>

<h2 class="section-title">Dernières commandes</h2>
<table class="admin-table">
    <thead><tr><th>#</th><th>Client</th><th>Total</th><th>Statut</th><th>Date</th><th>Action</th></tr></thead>
    <tbody>
        <?php foreach ($dernieres_commandes as $c): ?>
        <tr>
            <td><?= $c['id'] ?></td>
            <td><?= sanitize($c['prenom'] . ' ' . $c['nom']) ?></td>
            <td><?= number_format($c['total'], 2, ',', ' ') ?> €</td>
            <td><span class="badge <?= $badges[$c['statut']] ?>"><?= $labels[$c['statut']] ?></span></td>
            <td><?= date('d/m/Y H:i', strtotime($c['created_at'])) ?></td>
            <td><a href="commandes.php?id=<?= $c['id'] ?>" class="btn btn-sm">Gérer</a></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php include '../includes/footer.php'; ?>
