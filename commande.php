<?php
require_once 'includes/config.php';
$pageTitle = 'Mon Panier';

if (!isLoggedIn()) redirect('login.php?msg=Connectez-vous pour commander');

// Initialiser le panier en session
if (!isset($_SESSION['panier'])) $_SESSION['panier'] = [];

$message = '';
$error = '';

// Actions sur le panier
$action = $_POST['action'] ?? $_GET['action'] ?? '';

if ($action === 'add' && isset($_POST['produit_id'])) {
    $pid = (int)$_POST['produit_id'];
    $stmt = $pdo->prepare("SELECT id, nom, prix FROM produits WHERE id = ? AND disponible = 1");
    $stmt->execute([$pid]);
    $prod = $stmt->fetch();
    if ($prod) {
        if (isset($_SESSION['panier'][$pid])) {
            $_SESSION['panier'][$pid]['quantite']++;
        } else {
            $_SESSION['panier'][$pid] = ['nom' => $prod['nom'], 'prix' => $prod['prix'], 'quantite' => 1];
        }
        $message = 'Produit ajouté au panier.';
    }
}

if ($action === 'update' && isset($_POST['quantites'])) {
    foreach ($_POST['quantites'] as $pid => $qty) {
        $pid = (int)$pid;
        $qty = (int)$qty;
        if ($qty <= 0) {
            unset($_SESSION['panier'][$pid]);
        } elseif (isset($_SESSION['panier'][$pid])) {
            $_SESSION['panier'][$pid]['quantite'] = $qty;
        }
    }
    $message = 'Panier mis à jour.';
}

if ($action === 'remove' && isset($_GET['pid'])) {
    unset($_SESSION['panier'][(int)$_GET['pid']]);
    $message = 'Produit retiré du panier.';
}

if ($action === 'clear') {
    $_SESSION['panier'] = [];
    $message = 'Panier vidé.';
}

// Calcul du total
$total = 0;
foreach ($_SESSION['panier'] as $item) {
    $total += $item['prix'] * $item['quantite'];
}

// Soumission de la commande
if ($action === 'commander' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $adresse = trim($_POST['adresse_livraison'] ?? '');
    $commentaire = trim($_POST['commentaire'] ?? '');

    if (empty($_SESSION['panier'])) {
        $error = 'Votre panier est vide.';
    } elseif (!$adresse) {
        $error = 'Veuillez indiquer une adresse de livraison.';
    } else {
        // Insérer la commande
        $stmt = $pdo->prepare("INSERT INTO commandes (utilisateur_id, total, adresse_livraison, commentaire) VALUES (?,?,?,?)");
        $stmt->execute([$_SESSION['user_id'], $total, $adresse, $commentaire]);
        $commande_id = $pdo->lastInsertId();

        // Insérer les lignes
        $stmt = $pdo->prepare("INSERT INTO commande_items (commande_id, produit_id, quantite, prix_unitaire) VALUES (?,?,?,?)");
        foreach ($_SESSION['panier'] as $pid => $item) {
            $stmt->execute([$commande_id, $pid, $item['quantite'], $item['prix']]);
        }

        $_SESSION['panier'] = [];
        redirect('mes_commandes.php?success=1');
    }
}

// Récupérer l'adresse de l'utilisateur
$user = $pdo->prepare("SELECT adresse FROM utilisateurs WHERE id = ?");
$user->execute([$_SESSION['user_id']]);
$user = $user->fetch();
?>
<?php include 'includes/header.php'; ?>

<h1 class="page-title">Mon Panier</h1>

<?php if ($message): ?><div class="alert alert-success"><?= sanitize($message) ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-error"><?= sanitize($error) ?></div><?php endif; ?>

<?php if (empty($_SESSION['panier'])): ?>
    <div class="alert alert-info">Votre panier est vide. <a href="catalogue.php">Voir le catalogue</a></div>
<?php else: ?>

<form method="POST">
    <input type="hidden" name="action" value="update">
    <table class="cart-table">
        <thead>
            <tr><th>Produit</th><th>Prix unit.</th><th>Quantité</th><th>Sous-total</th><th></th></tr>
        </thead>
        <tbody>
            <?php foreach ($_SESSION['panier'] as $pid => $item): ?>
            <tr>
                <td><?= sanitize($item['nom']) ?></td>
                <td><?= number_format($item['prix'], 2, ',', ' ') ?> €</td>
                <td><input type="number" name="quantites[<?= $pid ?>]" value="<?= $item['quantite'] ?>" min="0" max="50"></td>
                <td><?= number_format($item['prix'] * $item['quantite'], 2, ',', ' ') ?> €</td>
                <td><a href="commande.php?action=remove&pid=<?= $pid ?>" class="btn btn-sm btn-danger">✕</a></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <div class="cart-total">Total : <?= number_format($total, 2, ',', ' ') ?> €</div>
    <div style="display:flex;gap:10px;justify-content:flex-end;flex-wrap:wrap">
        <button type="submit" class="btn btn-secondary">Mettre à jour</button>
        <a href="commande.php?action=clear" class="btn btn-danger">Vider le panier</a>
    </div>
</form>

<hr style="margin:40px 0;border:none;border-top:2px solid #f0e6d3">

<h2 style="font-family:'Playfair Display',serif;color:#3b1a0a;margin-bottom:20px">Finaliser ma commande</h2>
<p style="margin-bottom:20px;color:#666">Le paiement s'effectue à la livraison.</p>

<form method="POST" style="max-width:600px">
    <input type="hidden" name="action" value="commander">
    <div class="form-group">
        <label>Adresse de livraison *</label>
        <textarea name="adresse_livraison" required><?= sanitize($user['adresse'] ?? '') ?></textarea>
    </div>
    <div class="form-group">
        <label>Commentaire (optionnel)</label>
        <textarea name="commentaire" placeholder="Instructions particulières…"></textarea>
    </div>
    <div style="background:#fff;padding:16px;border-radius:8px;margin-bottom:16px;border:1px solid #f0e6d3">
        <strong>Récapitulatif :</strong>
        <?php foreach ($_SESSION['panier'] as $item): ?>
            <p style="margin:6px 0"><?= sanitize($item['nom']) ?> × <?= $item['quantite'] ?> = <?= number_format($item['prix'] * $item['quantite'], 2, ',', ' ') ?> €</p>
        <?php endforeach; ?>
        <p style="font-size:1.1rem;font-weight:700;color:#b5451b;margin-top:8px">Total : <?= number_format($total, 2, ',', ' ') ?> €</p>
    </div>
    <button type="submit" class="btn">✓ Confirmer la commande</button>
</form>

<?php endif; ?>

<?php include 'includes/footer.php'; ?>
