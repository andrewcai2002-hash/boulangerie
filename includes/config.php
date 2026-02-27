<?php
// Configuration de la boulangerie

// Configuration base de données
define('DB_HOST', 'db');
define('DB_NAME', 'boulangerie');
define('DB_USER', 'root');
define('DB_PASS', 'root');

// Configuration URL
define('BASE_URL', 'http://localhost:8080/boulangerie');
define('WEB_ROOT', '/boulangerie');  // Chemin racine web pour les assets
define('PUBLIC_URL', BASE_URL . '/public');
define('ADMIN_URL', PUBLIC_URL . '/admin');

// Chemins web relatifs pour CSS/JS
define('ASSETS_WEB_PATH', WEB_ROOT . '/assets');

// Chemins fichiers
define('PROJECT_ROOT', dirname(dirname(__FILE__)));
define('PUBLIC_DIR', PROJECT_ROOT . '/public');
define('ASSETS_DIR', PROJECT_ROOT . '/assets');
define('UPLOADS_DIR', ASSETS_DIR . '/img/produits');

// Configuration générale
define('APP_NAME', 'Boulangerie du Village');
define('DEFAULT_PAGINATION', 10);
