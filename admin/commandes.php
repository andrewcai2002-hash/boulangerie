<?php
require_once 'guard.php';
$pageTitle = 'Gestion des commandes';

// Changer statut
if (isset($_POST['statut'], $_POST['commande_id'])) {
    $statuts_valides = ['en_attente','prete','livree','annulee'];
    if (in_array($_POST['statut'], $statuts_valides)) {
        $stmt = $pdo->prepare("UPDATE commandes SET statut = ? WHERE id = ?");
        $stmt->execute([$_POST['statut'], (int)$_POST['commande_id']]);
    }
    redirect('commandes.php?ok=1');
}

$filtre = $_GET['statut'] ?? '';
$statuts_valides = ['en_attente','prete','livree','annulee'];

if ($filtre && in_array($filtre, $statuts_valides)) {
    $stmt = $pdo->prepare("
        SELECT c.*, u.prenom, u.nom, u.email, u.telephone FROM commandes c
        JOIN utilisateurs u ON u.id = c.utilisateur_id
        WHERE c.statut = ? ORDER BY c.created_at DESC
    ");
    $stmt->execute([$filtre]);
} else {
    $stmt = $pdo->query("
        SELECT c.*, u.prenom, u.nom, u.email, u.telephone FROM commandes c
        JOIN utilisateurs u ON u.id = c.utilisateur_id
        ORDER BY c.created_at DESC
    ");
}
$commandes = $stmt->fetchAll();

$badges = ['en_attente'=>'badge-attente','prete'=>'badge-prete','livree'=>'badge-livree','annulee'=>'badge-annulee'];
$labels = ['en_attente'=>'En préparation','prete'=>'Prête','livree'=>'Livrée','annulee'=>'Annulée'];

// Détail d'une commande
$detail = null;
if (isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT p.nom, ci.quantite, ci.prix_unitaire FROM commande_items ci JOIN produits p ON p.id = ci.produit_id WHERE ci.commande_id = ?");
    $stmt->execute([(int)$_GET['id']]);
    $detail = $stmt->fetchAll();
}
?>
<?php include '../includes/header.php'; ?>

<h1 class="page-title">Gestion des commandes</h1>

<div class="admin-nav">
    <a href="index.php" class="btn btn-secondary">← Dashboard</a>
    <a href="commandes.php" class="btn <?= !$filtre ? '' : 'btn-secondary' ?>">Toutes</a>
    <a href="commandes.php?statut=en_attente" class="btn <?= $filtre==='en_attente'?'':'btn-secondary' ?>">En attente</a>
    <a href="commandes.php?statut=prete" class="btn <?= $filtre==='prete'?'':'btn-secondary' ?>">Prêtes</a>
    <a href="commandes.php?statut=livree" class="btn <?= $filtre==='livree'?'':'btn-secondary' ?>">Livrées</a>
</div>

<?php if (isset($_GET['ok'])): ?><div class="alert alert-success">Statut mis à jour.</div><?php endif; ?>

<table class="admin-table">
    <thead><tr><th>#</th><th>Client</th><th>Contact</th><th>Total</th><th>Statut</th><th>Date</th><th>Changer statut</th></tr></thead>
    <tbody>
        <?php foreach ($commandes as $c): ?>
        <tr>
            <td><a href="commandes.php?id=<?= $c['id'] ?>">#<?= $c['id'] ?></a></td>
            <td><?= sanitize($c['prenom'] . ' ' . $c['nom']) ?></td>
            <td style="font-size:.85rem"><?= sanitize($c['email']) ?><br><?= sanitize($c['telephone'] ?? '') ?></td>
            <td><?= number_format($c['total'], 2, ',', ' ') ?> €</td>
            <td><span class="badge <?= $badges[$c['statut']] ?>"><?= $labels[$c['statut']] ?></span></td>
            <td><?= date('d/m/Y H:i', strtotime($c['created_at'])) ?></td>
            <td>
                <form method="POST" style="display:flex;gap:6px;flex-wrap:wrap">
                    <input type="hidden" name="commande_id" value="<?= $c['id'] ?>">
                    <select name="statut" style="padding:4px 8px;border-radius:4px;border:1px solid #ddd">
                        <?php foreach ($labels as $val => $lab): ?>
                            <option value="<?= $val ?>" <?= $c['statut']===$val?'selected':'' ?>><?= $lab ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" class="btn btn-sm">✓</button>
                </form>
            </td>
        </tr>
        <?php if ($detail && (int)$_GET['id'] === (int)$c['id']): ?>
        <tr>
            <td colspan="7" style="background:#fdf5ec;padding:16px">
                <strong>Détail de la commande #<?= $c['id'] ?> :</strong>
                <?php foreach ($detail as $item): ?>
                    <div style="margin:4px 0"><?= sanitize($item['nom']) ?> × <?= $item['quantite'] ?> = <?= number_format($item['prix_unitaire'] * $item['quantite'], 2, ',', ' ') ?> €</div>
                <?php endforeach; ?>
                <div style="margin-top:8px"><strong>Adresse :</strong> <?= sanitize($c['adresse_livraison']) ?></div>
                <?php if ($c['commentaire']): ?><div><strong>Note :</strong> <?= sanitize($c['commentaire']) ?></div><?php endif; ?>
            </td>
        </tr>
        <?php endif; ?>
        <?php endforeach; ?>
    </tbody>
</table>

<?php include '../includes/footer.php'; ?>
