<?php
require_once 'includes/config.php';
$pageTitle = 'Connexion';

if (isLoggedIn()) redirect('index.php');

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $pass  = $_POST['mot_de_passe'] ?? '';

    if (!$email || !$pass) {
        $error = 'Veuillez remplir tous les champs.';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($pass, $user['mot_de_passe'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['nom']     = $user['nom'];
            $_SESSION['prenom']  = $user['prenom'];
            $_SESSION['role']    = $user['role'];
            redirect($user['role'] === 'admin' ? 'admin/index.php' : 'index.php');
        } else {
            $error = 'Email ou mot de passe incorrect.';
        }
    }
}
?>
<?php include 'includes/header.php'; ?>

<div class="form-card">
    <h2>Connexion</h2>
    <?php if ($error): ?><div class="alert alert-error"><?= $error ?></div><?php endif; ?>
    <?php if (isset($_GET['msg'])): ?>
        <div class="alert alert-info"><?= sanitize($_GET['msg']) ?></div>
    <?php endif; ?>
    <form method="POST">
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" required value="<?= sanitize($_POST['email'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label>Mot de passe</label>
            <input type="password" name="mot_de_passe" required>
        </div>
        <button type="submit" class="btn">Se connecter</button>
    </form>
    <p style="text-align:center;margin-top:16px;font-size:.9rem">Pas encore de compte ? <a href="register.php">S'inscrire</a></p>
</div>

<?php include 'includes/footer.php'; ?>
