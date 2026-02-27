<?php
// Gestion de l'authentification et de la session

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';

// Démarrer la session une seule fois
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Enregistre un utilisateur dans la session
 * @param array $user
 */
function login_user(array $user): void {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['nom'] = $user['nom'];
    $_SESSION['prenom'] = $user['prenom'];
    $_SESSION['role'] = $user['role'];
}

// Déconnecte l'utilisateur
function logout_user(): void {
    $_SESSION = [];
    session_destroy();
}

/**
 * Récupère l'utilisateur actuellement connecté
 * @return array|null
 */
function current_user(): ?array {
    if (!is_logged_in()) {
        return null;
    }
    
    $user = db_fetch(
        'SELECT id, email, nom, prenom, role FROM users WHERE id = ?',
        [$_SESSION['user_id']]
    );
    
    return $user;
}

/**
 * Vérifie si l'utilisateur est connecté
 * @return bool
 */
function is_logged_in(): bool {
    return isset($_SESSION['user_id']);
}

/**
 * Vérifie si l'utilisateur est admin
 * @return bool
 */
function is_admin(): bool {
    return is_logged_in() && $_SESSION['role'] === 'admin';
}

// Redirige vers la page de connexion si pas connecté
function require_login(): void {
    if (!is_logged_in()) {
        redirect(PUBLIC_URL . '/connexion.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
    }
}

// Redirige ou affiche erreur 403 si pas admin
function require_admin(): void {
    if (!is_admin()) {
        http_response_code(403);
        die('Accès refusé. Vous n\'avez pas les permissions nécessaires.');
    }
}
