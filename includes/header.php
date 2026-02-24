<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/functions.php';

panier_init();
$user = current_user();
$panier_count = panier_count();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? e($page_title) . ' - ' : ''; echo APP_NAME; ?></title>
    <link rel="stylesheet" href="<?php echo ASSETS_WEB_PATH; ?>/css/style.css">
</head>
<body>
    <header class="main-header">
        <div class="container">
            <div class="header-content">
                <a href="<?php echo PUBLIC_URL; ?>/index.php" class="logo">
                    <h1><?php echo APP_NAME; ?></h1>
                </a>
                <nav class="main-nav">
                    <ul>
                        <li><a href="<?php echo PUBLIC_URL; ?>/index.php">Accueil</a></li>
                        <li><a href="<?php echo PUBLIC_URL; ?>/catalogue.php">Catalogue</a></li>
                        <li><a href="<?php echo PUBLIC_URL; ?>/informations.php">Informations</a></li>
                        <li><a href="<?php echo PUBLIC_URL; ?>/panier.php" class="panier-link">
                            Panier
                            <?php if ($panier_count > 0): ?>
                                <span class="badge"><?php echo $panier_count; ?></span>
                            <?php endif; ?>
                        </a></li>
                    </ul>
                </nav>
                <div class="user-menu">
                    <?php if ($user): ?>
                        <span class="greeting">Bonjour, <?php echo e($user['prenom']); ?></span>
                        <?php if ($user['role'] === 'admin'): ?>
                            <a href="<?php echo ADMIN_URL; ?>/index.php" class="btn-admin">Admin</a>
                        <?php endif; ?>
                        <a href="<?php echo PUBLIC_URL; ?>/deconnexion.php" class="btn-logout">Déconnexion</a>
                    <?php else: ?>
                        <a href="<?php echo PUBLIC_URL; ?>/connexion.php" class="btn-login">Connexion</a>
                        <a href="<?php echo PUBLIC_URL; ?>/inscription.php" class="btn-register">Inscription</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </header>
    <main class="main-content">
