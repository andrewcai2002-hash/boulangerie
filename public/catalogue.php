<?php
$page_title = 'Catalogue';
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/header.php';

// Récupérer toutes les catégories
$categories = db_fetch_all('SELECT * FROM categories ORDER BY nom');

// Déterminer si un filtre de catégorie est appliqué
$categorie_id = isset($_GET['categorie']) ? (int)$_GET['categorie'] : null;
$categorie_active = null;

// Récupérer les produits
if ($categorie_id) {
    // Vérifier que la catégorie existe
    $categorie_active = db_fetch('SELECT * FROM categories WHERE id = ?', [$categorie_id]);
    if (!$categorie_active) {
        redirect(PUBLIC_URL . '/catalogue.php');
    }
    
    $produits = db_fetch_all(
        'SELECT p.*, c.nom as categorie_nom 
         FROM produits p 
         JOIN categories c ON p.categorie_id = c.id 
         WHERE p.actif = 1 AND p.categorie_id = ? 
         ORDER BY p.nom',
        [$categorie_id]
    );
} else {
    $produits = db_fetch_all(
        'SELECT p.*, c.nom as categorie_nom 
         FROM produits p 
         JOIN categories c ON p.categorie_id = c.id 
         WHERE p.actif = 1 
         ORDER BY p.nom'
    );
}
?>

<div class="container">
    <h1>Catalogue</h1>
    
    <div class="catalogue-layout">
        <!-- Filtres -->
        <aside class="filters">
            <h3>Catégories</h3>
            <ul>
                <li>
                    <a href="<?php echo PUBLIC_URL; ?>/catalogue.php" 
                       class="<?php echo $categorie_id === null ? 'active' : ''; ?>">
                        Tous les produits
                    </a>
                </li>
                <?php foreach ($categories as $cat): ?>
                <li>
                    <a href="<?php echo PUBLIC_URL; ?>/catalogue.php?categorie=<?php echo $cat['id']; ?>"
                       class="<?php echo $categorie_id === $cat['id'] ? 'active' : ''; ?>">
                        <?php echo e($cat['nom']); ?>
                    </a>
                </li>
                <?php endforeach; ?>
            </ul>
        </aside>

        <!-- Liste des produits -->
        <section class="products-section">
            <?php if ($categorie_active): ?>
                <h2><?php echo e($categorie_active['nom']); ?></h2>
            <?php else: ?>
                <h2>Tous nos produits</h2>
            <?php endif; ?>

            <?php if (empty($produits)): ?>
                <p class="no-products">Aucun produit disponible dans cette catégorie.</p>
            <?php else: ?>
                <div class="products-grid">
                    <?php foreach ($produits as $produit): ?>
                    <div class="product-card">
                        <div class="product-image">
                            <?php if ($produit['image']): ?>
                                <img src="<?php echo ASSETS_WEB_PATH; ?>/img/produits/<?php echo e($produit['image']); ?>" alt="<?php echo e($produit['nom']); ?>">
                            <?php else: ?>
                                <div class="placeholder-image">Pas d'image</div>
                            <?php endif; ?>
                        </div>
                        <div class="product-info">
                            <h3><?php echo e($produit['nom']); ?></h3>
                            <p class="description"><?php echo e($produit['description']); ?></p>
                            <div class="product-footer">
                                <span class="price"><?php echo format_price($produit['prix']); ?></span>
                                <form method="POST" action="<?php echo PUBLIC_URL; ?>/panier.php?action=add" class="add-to-cart-form">
                                    <input type="hidden" name="produit_id" value="<?php echo $produit['id']; ?>">
                                    <div class="quantity-input">
                                        <input type="number" name="quantite" min="1" value="1" required>
                                    </div>
                                    <button type="submit" class="btn btn-sm btn-primary">Ajouter au panier</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
