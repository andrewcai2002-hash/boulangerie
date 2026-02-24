<?php
$page_title = 'Inscription';
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
// ⚠️ NE PAS inclure header.php ici - on doit d'abord traiter les redirects !

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $prenom = trim($_POST['prenom'] ?? '');
    $nom = trim($_POST['nom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';
    $adresse = trim($_POST['adresse'] ?? '');
    $telephone = trim($_POST['telephone'] ?? '');

    // Validation
    if (empty($prenom)) {
        $errors[] = 'Le prénom est obligatoire.';
    }
    if (empty($nom)) {
        $errors[] = 'Le nom est obligatoire.';
    }
    if (empty($email)) {
        $errors[] = 'L\'email est obligatoire.';
    } elseif (!is_valid_email($email)) {
        $errors[] = 'L\'email n\'est pas valide.';
    }
    if (empty($password)) {
        $errors[] = 'Le mot de passe est obligatoire.';
    } elseif (!is_valid_password($password)) {
        $errors[] = 'Le mot de passe doit contenir au moins 6 caractères.';
    }
    if ($password !== $password_confirm) {
        $errors[] = 'La confirmation du mot de passe ne correspond pas.';
    }

    // Vérifier si l'email existe déjà
    if (empty($errors)) {
        $existing = db_fetch('SELECT id FROM users WHERE email = ?', [$email]);
        if ($existing) {
            $errors[] = 'Cet email est déjà utilisé.';
        }
    }

    // Insertion en base
    if (empty($errors)) {
        try {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            db_query(
                'INSERT INTO users (nom, prenom, email, mot_de_passe, adresse, telephone, role) 
                 VALUES (?, ?, ?, ?, ?, ?, ?)',
                [$nom, $prenom, $email, $hashed_password, $adresse, $telephone, 'client']
            );
            
            // Auto-login
            $user = db_fetch('SELECT * FROM users WHERE email = ?', [$email]);
            if ($user) {
                require_once __DIR__ . '/../includes/auth.php';
                login_user($user);
            }
            
            // ✅ Redirect AVANT d'inclure header.php
            redirect(PUBLIC_URL . '/index.php?success=inscription');
        } catch (Exception $e) {
            $errors[] = 'Erreur lors de la création du compte.';
        }
    }
}

// ✅ Inclure header.php APRÈS avoir traité le POST
require_once __DIR__ . '/../includes/header.php';
?>


<div class="container">
    <div class="form-container">
        <h1>Créer un compte</h1>

        <?php if ($errors): ?>
            <div class="alert alert-error">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo e($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="POST" action="" class="form">
            <div class="form-group">
                <label for="prenom">Prénom</label>
                <input type="text" id="prenom" name="prenom" required value="<?php echo e($_POST['prenom'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="nom">Nom</label>
                <input type="text" id="nom" name="nom" required value="<?php echo e($_POST['nom'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required value="<?php echo e($_POST['email'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="password">Mot de passe</label>
                <input type="password" id="password" name="password" required>
            </div>

            <div class="form-group">
                <label for="password_confirm">Confirmer le mot de passe</label>
                <input type="password" id="password_confirm" name="password_confirm" required>
            </div>

            <div class="form-group">
                <label for="adresse">Adresse</label>
                <textarea id="adresse" name="adresse"><?php echo e($_POST['adresse'] ?? ''); ?></textarea>
            </div>

            <div class="form-group">
                <label for="telephone">Téléphone</label>
                <input type="tel" id="telephone" name="telephone" value="<?php echo e($_POST['telephone'] ?? ''); ?>">
            </div>

            <button type="submit" class="btn btn-primary">Créer mon compte</button>
        </form>

        <p class="form-footer">
            Vous avez déjà un compte? <a href="<?php echo PUBLIC_URL; ?>/connexion.php">Se connecter</a>
        </p>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
