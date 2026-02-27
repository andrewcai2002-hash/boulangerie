<?php
require_once 'includes/config.php';
$pageTitle = 'Catalogue';

$cat_id = isset($_GET['cat']) ? (int)$_GET['cat'] : 0;

$categories = $pdo->query("SELECT * FROM categories ORDER BY id")->fetchAll();

if ($cat_id > 0) {
    $stmt = $pdo->prepare("SELECT p.*, c.nom AS categorie FROM produits p JOIN categories c ON p.categorie_id = c.id WHERE p.disponible = 1 AND p.categorie_id = ? ORDER BY p.nom");
    $stmt->execute([$cat_id]);
} else {
    $stmt = $pdo->query("SELECT p.*, c.nom AS categorie FROM produits p JOIN categories c ON p.categorie_id = c.id WHERE p.disponible = 1 ORDER BY c.id, p.nom");
}
$produits = $stmt->fetchAll();
?>
<?php include 'includes/header.php'; ?>

<h1 class="page-title">Notre Catalogue</h1>

<div class="category-tabs">
    <a href="catalogue.php" class="<?= $cat_id === 0 ? 'active' : '' ?>">Tous</a>
    <?php foreach ($categories as $cat): ?>
        <a href="catalogue.php?cat=<?= $cat['id'] ?>" class="<?= $cat_id === $cat['id'] ? 'active' : '' ?>"><?= sanitize($cat['nom']) ?></a>
    <?php endforeach; ?>
</div>

<?php if (empty($produits)): ?>
    <div class="alert alert-info">Aucun produit dans cette catégorie pour le moment.</div>
<?php else: ?>
<div class="grid">
    <?php foreach ($produits as $p): ?>
    <div class="card">
        <div class="card-img" style="background:#f0e6d3;display:flex;align-items:center;justify-content:center;font-size:4rem;">
            <?php
            $emoji = ['Pains'=>'🍞','Viennoiseries'=>'🥐','Pâtisseries'=>'🎂'][$p['categorie']] ?? '🍰';
            echo $emoji;
            ?>
        </div>
        <div class="card-body">
            <small style="color:#b5451b;font-weight:700;text-transform:uppercase"><?= sanitize($p['categorie']) ?></small>
            <h3><?= sanitize($p['nom']) ?></h3>
            <p><?= sanitize($p['description']) ?></p>
            <div style="display:flex;align-items:center;justify-content:space-between;margin-top:10px">
                <span class="price"><?= number_format($p['prix'], 2, ',', ' ') ?> €</span>
                <?php if (isLoggedIn()): ?>
                    <form method="POST" action="commande.php">
                        <input type="hidden" name="action" value="add">
                        <input type="hidden" name="produit_id" value="<?= $p['id'] ?>">
                        <button type="submit" class="btn btn-sm">+ Panier</button>
                    </form>
                <?php else: ?>
                    <a href="login.php" class="btn btn-sm btn-outline" style="border-color:#b5451b;color:#b5451b">Connexion</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>
