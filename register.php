<?php
require_once 'includes/config.php';
$pageTitle = 'Inscription';

if (isLoggedIn()) redirect('index.php');

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom    = trim($_POST['nom'] ?? '');
    $prenom = trim($_POST['prenom'] ?? '');
    $email  = trim($_POST['email'] ?? '');
    $pass   = $_POST['mot_de_passe'] ?? '';
    $pass2  = $_POST['mot_de_passe2'] ?? '';
    $adresse = trim($_POST['adresse'] ?? '');
    $tel    = trim($_POST['telephone'] ?? '');

    if (!$nom || !$prenom || !$email || !$pass) {
        $error = 'Veuillez remplir tous les champs obligatoires.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Adresse email invalide.';
    } elseif (strlen($pass) < 6) {
        $error = 'Le mot de passe doit contenir au moins 6 caractères.';
    } elseif ($pass !== $pass2) {
        $error = 'Les mots de passe ne correspondent pas.';
    } else {
        // Vérifier email unique
        $stmt = $pdo->prepare("SELECT id FROM utilisateurs WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = 'Cet email est déjà utilisé.';
        } else {
            $hash = password_hash($pass, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO utilisateurs (nom, prenom, email, mot_de_passe, adresse, telephone) VALUES (?,?,?,?,?,?)");
            $stmt->execute([$nom, $prenom, $email, $hash, $adresse, $tel]);
            $success = 'Compte créé avec succès ! <a href="login.php">Se connecter</a>';
        }
    }
}
?>
<?php include 'includes/header.php'; ?>

<div class="form-card">
    <h2>Créer un compte</h2>
    <?php if ($error): ?><div class="alert alert-error"><?= $error ?></div><?php endif; ?>
    <?php if ($success): ?><div class="alert alert-success"><?= $success ?></div><?php endif; ?>
    <form method="POST">
        <div class="form-group">
            <label>Prénom *</label>
            <input type="text" name="prenom" required value="<?= sanitize($_POST['prenom'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label>Nom *</label>
            <input type="text" name="nom" required value="<?= sanitize($_POST['nom'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label>Email *</label>
            <input type="email" name="email" required value="<?= sanitize($_POST['email'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label>Mot de passe * (min. 6 caractères)</label>
            <input type="password" name="mot_de_passe" required>
        </div>
        <div class="form-group">
            <label>Confirmer le mot de passe *</label>
            <input type="password" name="mot_de_passe2" required>
        </div>
        <div class="form-group">
            <label>Adresse de livraison</label>
            <textarea name="adresse"><?= sanitize($_POST['adresse'] ?? '') ?></textarea>
        </div>
        <div class="form-group">
            <label>Téléphone</label>
            <input type="tel" name="telephone" value="<?= sanitize($_POST['telephone'] ?? '') ?>">
        </div>
        <button type="submit" class="btn">Créer mon compte</button>
    </form>
    <p style="text-align:center;margin-top:16px;font-size:.9rem">Déjà un compte ? <a href="login.php">Se connecter</a></p>
</div>

<?php include 'includes/footer.php'; ?>
