<?php
/**
 * Fonctions utilitaires générales
 */

require_once __DIR__ . '/config.php';

/**
 * Échappe les caractères HTML
 * @param string $string
 * @return string
 */
function e(string $string): string {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

/**
 * Redirige vers une URL
 * @param string $url
 */
function redirect(string $url): void {
    header('Location: ' . $url);
    exit;
}

/**
 * Ajoute un produit au panier
 * @param int $produit_id
 * @param int $quantite
 * @param array $produit
 */
function panier_add(int $produit_id, int $quantite, array $produit): void {
    if (!isset($_SESSION['panier'])) {
        $_SESSION['panier'] = [];
    }
    
    if (isset($_SESSION['panier'][$produit_id])) {
        $_SESSION['panier'][$produit_id]['quantite'] += $quantite;
    } else {
        $_SESSION['panier'][$produit_id] = [
            'nom' => $produit['nom'],
            'prix' => $produit['prix'],
            'quantite' => $quantite
        ];
    }
}

/**
 * Met à jour la quantité d'un produit dans le panier
 * @param int $produit_id
 * @param int $quantite
 */
function panier_update(int $produit_id, int $quantite): void {
    if (!isset($_SESSION['panier'])) {
        $_SESSION['panier'] = [];
    }
    
    if ($quantite <= 0) {
        panier_remove($produit_id);
    } else {
        $_SESSION['panier'][$produit_id]['quantite'] = $quantite;
    }
}

/**
 * Supprime un produit du panier
 * @param int $produit_id
 */
function panier_remove(int $produit_id): void {
    if (isset($_SESSION['panier'][$produit_id])) {
        unset($_SESSION['panier'][$produit_id]);
    }
}

/**
 * Vide complètement le panier
 */
function panier_clear(): void {
    $_SESSION['panier'] = [];
}

/**
 * Calcule le total du panier
 * @return float
 */
function panier_total(): float {
    if (!isset($_SESSION['panier'])) {
        return 0.0;
    }
    
    $total = 0.0;
    foreach ($_SESSION['panier'] as $item) {
        $total += $item['prix'] * $item['quantite'];
    }
    
    return round($total, 2);
}

/**
 * Récupère le nombre d'articles dans le panier
 * @return int
 */
function panier_count(): int {
    if (!isset($_SESSION['panier'])) {
        return 0;
    }
    
    $count = 0;
    foreach ($_SESSION['panier'] as $item) {
        $count += $item['quantite'];
    }
    
    return $count;
}

/**
 * Récupère le panier
 * @return array
 */
function panier_get(): array {
    return $_SESSION['panier'] ?? [];
}

/**
 * Initialise la session du panier
 */
function panier_init(): void {
    if (!isset($_SESSION['panier'])) {
        $_SESSION['panier'] = [];
    }
}

/**
 * Valide une adresse email
 * @param string $email
 * @return bool
 */
function is_valid_email(string $email): bool {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Valide un mot de passe (au moins 6 caractères)
 * @param string $password
 * @return bool
 */
function is_valid_password(string $password): bool {
    return strlen($password) >= 6;
}

/**
 * Formate un prix
 * @param float $price
 * @return string
 */
function format_price(float $price): string {
    return number_format($price, 2, ',', ' ') . ' €';
}

/**
 * Formate une date
 * @param string $date
 * @return string
 */
function format_date(string $date): string {
    $timestamp = strtotime($date);
    return date('d/m/Y \à H:i', $timestamp);
}

/**
 * Convertit un statut de commande en label lisible
 * @param string $statut
 * @return string
 */
function format_statut(string $statut): string {
    $statuts = [
        'en_attente' => 'En attente',
        'prete' => 'Prête à être livrée',
        'livree' => 'Livrée'
    ];
    
    return $statuts[$statut] ?? $statut;
}

/**
 * Génère un slug à partir d'un texte
 * @param string $text
 * @return string
 */
function to_slug(string $text): string {
    $text = strtolower($text);
    $text = preg_replace('/[^a-z0-9]+/', '-', $text);
    $text = trim($text, '-');
    return $text;
}