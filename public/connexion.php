<?php
$page_title = 'Connexion';
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';


$error = null;
$redirect = $_GET['redirect'] ?? PUBLIC_URL . '/index.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = 'Veuillez remplir tous les champs.';
    } else {
        // Rechercher l'utilisateur
        $user = db_fetch('SELECT * FROM users WHERE email = ?', [$email]);

        if ($user && password_verify($password, $user['mot_de_passe'])) {
            // Login réussi
            login_user($user);
            
            // Redirection selon le rôle et la page de retour
            if ($user['role'] === 'admin') {
                redirect(ADMIN_URL . '/index.php');
            } else {
                redirect($redirect);
            }
        } else {
            $error = 'Email ou mot de passe incorrect.';
        }
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="container">
    <div class="form-container form-container-small">
        <h1>Se connecter</h1>

        <?php if ($error): ?>
            <div class="alert alert-error">
                <?php echo e($error); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="" class="form">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required value="<?php echo e($_POST['email'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="password">Mot de passe</label>
                <input type="password" id="password" name="password" required>
            </div>

            <button type="submit" class="btn btn-primary btn-block">Se connecter</button>
        </form>

        <p class="form-footer">
            Pas de compte? <a href="<?php echo PUBLIC_URL; ?>/inscription.php">S'inscrire</a>
        </p>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
