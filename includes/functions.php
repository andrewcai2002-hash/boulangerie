<?php
/**
 * ====== FICHIER FUNCTIONS.PHP ======
 * Ce fichier contient TOUTES les fonctions utilitaires du site
 * - Gestion du panier (ajouter, modifier, supprimer produits)
 * - Validation (email, mot de passe)
 * - Formatage de données (prix, dates)
 * - Utilitaires généraux (redirection, échappement HTML)
 */

require_once __DIR__ . '/config.php';

/**
 * FONCTION: e()
 * ÉCHAPPE les caractères HTML pour éviter les failles XSS
 * TOUJOURS utiliser e() en affichant des données utilisateur !
 * @param string $string - La chaîne à échapper
 * @return string - La chaîne échappée (sécurisée)
 */
function e(string $string): string {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

/**
 * FONCTION: redirect()
 * Redirige l'utilisateur vers une autre URL
 * @param string $url - L'URL vers laquelle rediriger
 */
function redirect(string $url): void {
    header('Location: ' . $url);  // Envoie au navigateur l'ordre de redirection
    exit;                          // Arrête l'exécution du script
}

/**
 * FONCTION: panier_add()
 * Ajoute UN produit au panier (ou ajoute à la quantité s'il existe déjà)
 * Appelé quand on clique sur "Ajouter au panier" dans le catalogue
 * @param int $produit_id - L'ID du produit à ajouter
 * @param int $quantite - Combien de produits ajouter
 * @param array $produit - Les données du produit (nom, prix...)
 */
function panier_add(int $produit_id, int $quantite, array $produit): void {
    // Vérifier si le panier existe en session
    if (!isset($_SESSION['panier'])) {
        $_SESSION['panier'] = [];  // Si pas, crée un tableau vide
    }
    
    // Si le produit est déjà dans le panier
    if (isset($_SESSION['panier'][$produit_id])) {
        // Ajoute simplement à la quantité existante
        $_SESSION['panier'][$produit_id]['quantite'] += $quantite;
    } else {
        // Sinon, ajoute le produit avec ses données
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
 * FONCTION: panier_total()
 * CALCULE le prix total du panier
 * Boucle sur chaque produit: prix * quantité
 * @return float - Le total en euros (arrondi à 2 décimales)
 */
function panier_total(): float {
    // Si le panier n'existe pas, le total est 0
    if (!isset($_SESSION['panier'])) {
        return 0.0;
    }
    
    $total = 0.0;
    // Parcourt chaque article du panier
    foreach ($_SESSION['panier'] as $item) {
        // Ajoute au total: prix de l'article * quantité
        $total += $item['prix'] * $item['quantite'];
    }
    
    // Retourne le total arrondi à 2 décimales (ex: 25.50)
    return round($total, 2);
}

/**
 * FONCTION: panier_count()
 * COMPTE le nombre TOTAL d'articles dans le panier
 * @return int - Le nombre d'articles
 */
function panier_count(): int {
    if (!isset($_SESSION['panier'])) {
        return 0;  // Panier vide
    }
    
    $count = 0;
    // Parcourt chaque produit et somme les quantités
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
 * FONCTION: format_price()
 * FORMATE un nombre en prix lisible
 * Ex: 25.5 devient "25,50 €" (format français)
 * @param float $price - Le prix en nombre (ex: 25.5)
 * @return string - Le prix formaté (ex: "25,50 €")
 */
function format_price(float $price): string {
    // number_format: convertit le nombre avec virgule (,) et espace (?)
    // 2 = 2 décimales, ',' = séparateur décimal, ' ' = séparateur de milliers
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