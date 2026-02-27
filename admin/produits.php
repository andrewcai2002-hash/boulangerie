<?php
require_once 'guard.php';
$pageTitle = 'Gestion des produits';

$error = '';
$success = '';

// Suppression
if (isset($_GET['delete'])) {
    $pdo->prepare("UPDATE produits SET disponible = 0 WHERE id = ?")->execute([(int)$_GET['delete']]);
    redirect('produits.php?ok=supprime');
}

// Restaurer
if (isset($_GET['restore'])) {
    $pdo->prepare("UPDATE produits SET disponible = 1 WHERE id = ?")->execute([(int)$_GET['restore']]);
    redirect('produits.php?ok=restaure');
}

// Modifier ou ajouter
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom         = trim($_POST['nom'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $prix        = (float)($_POST['prix'] ?? 0);
    $cat_id      = (int)($_POST['categorie_id'] ?? 0);
    $id          = (int)($_POST['id'] ?? 0);

    if (!$nom || !$prix || !$cat_id) {
        $error = 'Nom, prix et catégorie sont obligatoires.';
    } else {
        if ($id > 0) {
            $stmt = $pdo->prepare("UPDATE produits SET nom=?, description=?, prix=?, categorie_id=? WHERE id=?");
            $stmt->execute([$nom, $description, $prix, $cat_id, $id]);
            $success = 'Produit modifié.';
        } else {
            $stmt = $pdo->prepare("INSERT INTO produits (nom, description, prix, categorie_id) VALUES (?,?,?,?)");
            $stmt->execute([$nom, $description, $prix, $cat_id]);
            $success = 'Produit ajouté.';
        }
    }
}

$categories = $pdo->query("SELECT * FROM categories ORDER BY id")->fetchAll();
$produits   = $pdo->query("SELECT p.*, c.nom AS categorie FROM produits p JOIN categories c ON c.id = p.categorie_id ORDER BY p.disponible DESC, c.id, p.nom")->fetchAll();

// Produit à éditer
$edit = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM produits WHERE id = ?");
    $stmt->execute([(int)$_GET['edit']]);
    $edit = $stmt->fetch();
}
?>
<?php include '../includes/header.php'; ?>

<h1 class="page-title">Gestion des produits</h1>

<div class="admin-nav">
    <a href="index.php" class="btn btn-secondary">← Dashboard</a>
</div>

<?php if ($success): ?><div class="alert alert-success"><?= $success ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-error"><?= $error ?></div><?php endif; ?>
<?php if (isset($_GET['ok'])): ?><div class="alert alert-success">Action effectuée.</div><?php endif; ?>

<!-- Formulaire ajout / édition -->
<div style="background:#fff;padding:28px;border-radius:10px;box-shadow:0 2px 10px rgba(0,0,0,.08);margin-bottom:36px;max-width:600px">
    <h2 style="font-family:'Playfair Display',serif;color:#3b1a0a;margin-bottom:20px"><?= $edit ? 'Modifier le produit' : 'Ajouter un produit' ?></h2>
    <form method="POST">
        <?php if ($edit): ?><input type="hidden" name="id" value="<?= $edit['id'] ?>"><?php endif; ?>
        <div class="form-group">
            <label>Catégorie *</label>
            <select name="categorie_id">
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['id'] ?>" <?= ($edit && $edit['categorie_id']==$cat['id']) ? 'selected' : '' ?>><?= sanitize($cat['nom']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label>Nom *</label>
            <input type="text" name="nom" required value="<?= sanitize($edit['nom'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label>Description</label>
            <textarea name="description"><?= sanitize($edit['description'] ?? '') ?></textarea>
        </div>
        <div class="form-group">
            <label>Prix (€) *</label>
            <input type="number" name="prix" step="0.01" min="0.01" required value="<?= $edit['prix'] ?? '' ?>">
        </div>
        <div style="display:flex;gap:10px">
            <button type="submit" class="btn"><?= $edit ? '✓ Sauvegarder' : '+ Ajouter' ?></button>
            <?php if ($edit): ?><a href="produits.php" class="btn btn-secondary">Annuler</a><?php endif; ?>
        </div>
    </form>
</div>

<!-- Liste des produits -->
<table class="admin-table">
    <thead><tr><th>#</th><th>Nom</th><th>Catégorie</th><th>Prix</th><th>Dispo</th><th>Actions</th></tr></thead>
    <tbody>
        <?php foreach ($produits as $p): ?>
        <tr <?= !$p['disponible'] ? 'style="opacity:.5"' : '' ?>>
            <td><?= $p['id'] ?></td>
            <td><?= sanitize($p['nom']) ?></td>
            <td><?= sanitize($p['categorie']) ?></td>
            <td><?= number_format($p['prix'], 2, ',', ' ') ?> €</td>
            <td><?= $p['disponible'] ? '✅' : '❌' ?></td>
            <td style="display:flex;gap:6px;flex-wrap:wrap">
                <a href="produits.php?edit=<?= $p['id'] ?>" class="btn btn-sm btn-secondary">✏️ Modifier</a>
                <?php if ($p['disponible']): ?>
                    <a href="produits.php?delete=<?= $p['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Supprimer ce produit ?')">🗑</a>
                <?php else: ?>
                    <a href="produits.php?restore=<?= $p['id'] ?>" class="btn btn-sm">Restaurer</a>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php include '../includes/footer.php'; ?>
