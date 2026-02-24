<?php
$page_title = 'Accueil';
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/header.php';

// Récupérer quelques produits phares
$produits = db_fetch_all(
    'SELECT p.*, c.nom as categorie_nom 
     FROM produits p 
     JOIN categories c ON p.categorie_id = c.id 
     WHERE p.actif = 1 
     ORDER BY p.created_at DESC 
     LIMIT 6'
);
?>

<div class="container">
    <!-- Section Hero -->
    <section class="hero">
        <div class="hero-content">
            <h1>Bienvenue à la <?php echo APP_NAME; ?></h1>
            <p>Des pains et pâtisseries artisanales, préparés chaque jour avec les meilleurs ingrédients</p>
            <a href="<?php echo PUBLIC_URL; ?>/catalogue.php" class="btn btn-primary btn-lg">Découvrir nos produits</a>
        </div>
    </section>

    <!-- Section À propos -->
    <section class="about">
        <h2>À propos de nous</h2>
        <div class="about-content">
            <div class="about-text">
                <p>Depuis plus de 20 ans, la <?php echo APP_NAME; ?> perpétue la tradition boulangère française. 
                Chaque jour, nos artisans préparent du pain frais, des viennoiseries gourmandes et des pâtisseries délicieuses.</p>
                <p>Nous utilisons exclusivement des ingrédients de qualité supérieure et des méthodes traditionnelles 
                pour vous offrir le meilleur de la boulangerie artisanale.</p>
            </div>
            <div class="about-image">
                <img src="<?php echo ASSETS_WEB_PATH; ?>/img/boulangerie.jpg" alt="Notre boulangerie" style="background: #ccc; height: 300px; width: 100%;">
            </div>
        </div>
    </section>

    <!-- Section Produits phares -->
    <section class="featured-products">
        <h2>Nos produits phares</h2>
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
                    <p class="category"><?php echo e($produit['categorie_nom']); ?></p>
                    <p class="description"><?php echo e(substr($produit['description'], 0, 100)); ?>...</p>
                    <div class="product-footer">
                        <span class="price"><?php echo format_price($produit['prix']); ?></span>
                        <form method="POST" action="<?php echo PUBLIC_URL; ?>/panier.php?action=add" class="add-to-cart-form">
                            <input type="hidden" name="produit_id" value="<?php echo $produit['id']; ?>">
                            <div class="quantity-input">
                                <input type="number" name="quantite" min="1" value="1" required>
                            </div>
                            <button type="submit" class="btn btn-sm btn-primary">Ajouter</button>
                        </form>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </section>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
