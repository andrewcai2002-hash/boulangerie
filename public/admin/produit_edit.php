<?php
$page_title = 'Édition de produit';
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/functions.php';

require_admin();

$produit_id = isset($_GET['id']) ? (int)$_GET['id'] : null;
$mode = $produit_id ? 'edit' : 'add';
$produit = null;
$errors = [];

if ($mode === 'edit') {
    $produit = db_fetch('SELECT * FROM produits WHERE id = ?', [$produit_id]);
    if (!$produit) {
        http_response_code(404);
        die('Produit non trouvé.');
    }
}

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom'] ?? '');
    $categorie_id = (int)($_POST['categorie_id'] ?? 0);
    $description = trim($_POST['description'] ?? '');
    $prix = (float)($_POST['prix'] ?? 0);
    $actif = isset($_POST['actif']) ? 1 : 0;
    $image = $_POST['image'] ?? '';

    // Validation
    if (empty($nom)) {
        $errors[] = 'Le nom est obligatoire.';
    }
    if ($categorie_id === 0) {
        $errors[] = 'Veuillez sélectionner une catégorie.';
    }
    if (empty($description)) {
        $errors[] = 'La description est obligatoire.';
    }
    if ($prix <= 0) {
        $errors[] = 'Le prix doit être supérieur à 0.';
    }

    // Gestion de l'upload d'image
    if (!empty($_FILES['image']['name'])) {
        $file = $_FILES['image'];
        $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        
        if (!in_array($file['type'], $allowed)) {
            $errors[] = 'Type de fichier non autorisé. Acceptés: JPG, PNG, GIF, WebP';
        } elseif ($file['size'] > 5000000) { // 5MB
            $errors[] = 'Le fichier est trop volumineux (max 5MB).';
        } elseif ($file['error'] === UPLOAD_ERR_OK) {
            $upload_dir = PROJECT_ROOT . '/assets/img/produits/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            $filename = 'produit_' . time() . '_' . uniqid() . '.' . pathinfo($file['name'], PATHINFO_EXTENSION);
            $filepath = $upload_dir . $filename;
            
            if (move_uploaded_file($file['tmp_name'], $filepath)) {
                $image = $filename;
            } else {
                $errors[] = 'Erreur lors de l\'upload du fichier.';
            }
        }
    }

    // Insérer ou mettre à jour
    if (empty($errors)) {
        try {
            if ($mode === 'add') {
                db_query(
                    'INSERT INTO produits (categorie_id, nom, description, prix, image, actif) 
                     VALUES (?, ?, ?, ?, ?, ?)',
                    [$categorie_id, $nom, $description, $prix, $image, $actif]
                );
            } else {
                // Garder l'image existante si pas d'upload
                if (empty($image)) {
                    $image = $produit['image'];
                }
                
                db_query(
                    'UPDATE produits SET categorie_id = ?, nom = ?, description = ?, prix = ?, image = ?, actif = ? 
                     WHERE id = ?',
                    [$categorie_id, $nom, $description, $prix, $image, $actif, $produit_id]
                );
            }
            
            redirect(ADMIN_URL . '/produits.php');
        } catch (Exception $e) {
            $errors[] = 'Erreur lors de la sauvegarde: ' . $e->getMessage();
        }
    }
}

// Récupérer les catégories
$categories = db_fetch_all('SELECT * FROM categories ORDER BY nom');

require_once __DIR__ . '/../../includes/header.php';
?>

<div class="container admin-container">
    <div class="form-header">
        <h1><?php echo $mode === 'add' ? 'Ajouter un produit' : 'Éditer le produit'; ?></h1>
        <a href="<?php echo ADMIN_URL; ?>/produits.php" class="btn btn-secondary">Retour</a>
    </div>

    <?php if ($errors): ?>
        <div class="alert alert-error">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?php echo e($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" class="form form-wide">
        <div class="form-row">
            <div class="form-group">
                <label for="nom">Nom du produit *</label>
                <input type="text" id="nom" name="nom" required value="<?php echo e($produit['nom'] ?? $_POST['nom'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label for="categorie_id">Catégorie *</label>
                <select id="categorie_id" name="categorie_id" required>
                    <option value="">-- Sélectionner une catégorie --</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo $cat['id']; ?>" <?php echo ($produit['categorie_id'] ?? 0) == $cat['id'] ? 'selected' : ''; ?>>
                            <?php echo e($cat['nom']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="prix">Prix (€) *</label>
                <input type="number" id="prix" name="prix" step="0.01" min="0" required value="<?php echo isset($produit['prix']) ? $produit['prix'] : ($_POST['prix'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label for="actif">
                    <input type="checkbox" id="actif" name="actif" <?php echo (isset($produit['actif']) && $produit['actif']) || !isset($produit) ? 'checked' : ''; ?>>
                    Produit actif
                </label>
            </div>
        </div>

        <div class="form-group">
            <label for="description">Description *</label>
            <textarea id="description" name="description" rows="5" required><?php echo e($produit['description'] ?? $_POST['description'] ?? ''); ?></textarea>
        </div>

        <div class="form-group">
            <label for="image">Image du produit</label>
            <?php if ($produit && $produit['image']): ?>
                <div class="current-image">
                    <p>Image actuelle:</p>
                    <img src="<?php echo ASSETS_WEB_PATH; ?>/img/produits/<?php echo e($produit['image']); ?>" alt="" style="max-height: 200px;">
                </div>
            <?php endif; ?>
            <input type="file" id="image" name="image" accept="image/*">
            <small>Formats acceptés: JPG, PNG, GIF, WebP. Taille max: 5MB</small>
        </div>

        <div class="form-actions">
            <a href="<?php echo ADMIN_URL; ?>/produits.php" class="btn btn-secondary">Annuler</a>
            <button type="submit" class="btn btn-primary">
                <?php echo $mode === 'add' ? 'Créer le produit' : 'Mettre à jour'; ?>
            </button>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
