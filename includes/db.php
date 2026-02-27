<?php
// Gestion de la connexion à la base de données

require_once __DIR__ . '/config.php';

// Variable globale pour la connexion PDO
$pdo = null;

/**
 * Établit une connexion PDO à la base de données
 * @return PDO
 */
function db_connect(): PDO {
    global $pdo;
    
    if ($pdo === null) {
        try {
            $pdo = new PDO(
                'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
                DB_USER,
                DB_PASS,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
        } catch (PDOException $e) {
            die('Erreur de connexion à la base de données: ' . $e->getMessage());
        }
    }
    
    return $pdo;
}

/**
 * Exécute une requête préparée
 * @param string $sql
 * @param array $params
 * @return PDOStatement
 */
function db_query(string $sql, array $params = []): PDOStatement {
    $pdo = db_connect();
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt;
}

/**
 * Récupère tous les résultats d'une requête
 * @param string $sql
 * @param array $params
 * @return array
 */
function db_fetch_all(string $sql, array $params = []): array {
    return db_query($sql, $params)->fetchAll();
}

/**
 * Récupère le premier résultat d'une requête
 * @param string $sql
 * @param array $params
 * @return array|null
 */
function db_fetch(string $sql, array $params = []): ?array {
    $result = db_query($sql, $params)->fetch();
    return $result ?: null;
}

/**
 * Récupère l'ID du dernier insert
 * @return string
 */
function db_lastInsertId(): string {
    return db_connect()->lastInsertId();
}

/**
 * Récupère le nombre de lignes affectées par la dernière requête
 * @return int
 */
function db_rowCount(): int {
    return db_query('SELECT LAST_INSERT_ID()')->rowCount();
}
