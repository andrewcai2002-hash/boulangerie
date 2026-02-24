-- ============================================================
-- Dump SQL pour Boulangerie du Village
-- Database: boulangerie
-- ============================================================

-- Créer la base de données
DROP DATABASE IF EXISTS boulangerie;
CREATE DATABASE boulangerie CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE boulangerie;

-- ============================================================
-- Table: users (Utilisateurs - Clients et Admin)
-- ============================================================
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    mot_de_passe VARCHAR(255) NOT NULL,
    adresse TEXT,
    telephone VARCHAR(20),
    role ENUM('client', 'admin') DEFAULT 'client',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_email (email),
    INDEX idx_role (role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Table: categories (Catégories de produits)
-- ============================================================
CREATE TABLE categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Table: produits (Produits)
-- ============================================================
CREATE TABLE produits (
    id INT PRIMARY KEY AUTO_INCREMENT,
    categorie_id INT NOT NULL,
    nom VARCHAR(150) NOT NULL,
    description TEXT NOT NULL,
    prix DECIMAL(6,2) NOT NULL,
    image VARCHAR(255),
    actif TINYINT(1) DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (categorie_id) REFERENCES categories(id) ON DELETE RESTRICT,
    INDEX idx_categorie (categorie_id),
    INDEX idx_actif (actif)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Table: commandes (Commandes)
-- ============================================================
CREATE TABLE commandes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    date_commande DATETIME DEFAULT CURRENT_TIMESTAMP,
    statut ENUM('en_attente', 'prete', 'livree') DEFAULT 'en_attente',
    total DECIMAL(8,2) NOT NULL,
    adresse_livraison TEXT NOT NULL,
    commentaire TEXT,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user (user_id),
    INDEX idx_statut (statut),
    INDEX idx_date (date_commande)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Table: commande_items (Articles des commandes)
-- ============================================================
CREATE TABLE commande_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    commande_id INT NOT NULL,
    produit_id INT NOT NULL,
    quantite INT NOT NULL,
    prix_unitaire DECIMAL(6,2) NOT NULL,
    
    FOREIGN KEY (commande_id) REFERENCES commandes(id) ON DELETE CASCADE,
    FOREIGN KEY (produit_id) REFERENCES produits(id) ON DELETE RESTRICT,
    INDEX idx_commande (commande_id),
    INDEX idx_produit (produit_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- DONNÉES INITIALES
-- ============================================================

-- Catégories
INSERT INTO categories (nom) VALUES
('Pains'),
('Viennoiseries'),
('Pâtisseries');

-- Utilisateurs (admin et client test)
INSERT INTO users (nom, prenom, email, mot_de_passe, adresse, telephone, role) VALUES
('Admin', 'Boulange', 'admin@boulangerie.local', '$2y$10$Y0nPdKfHBWnBMrGhq8Vp3ep5AKVQjBvDhTwTqKKCVdJ6YqZ6x1F42', '123, rue de la Boulangerie', '01234567890', 'admin'),
('Dupont', 'Jean', 'jean@example.com', '$2y$10$Y0nPdKfHBWnBMrGhq8Vp3ep5AKVQjBvDhTwTqKKCVdJ6YqZ6x1F42', '456, avenue des Fleurs', '06 12 34 56 78', 'client'),
('Martin', 'Marie', 'marie@example.com', '$2y$10$Y0nPdKfHBWnBMrGhq8Vp3ep5AKVQjBvDhTwTqKKCVdJ6YqZ6x1F42', '789, boulevard Saint-Germain', '07 98 76 54 32', 'client');

-- Produits - PAINS (catégorie 1)
INSERT INTO produits (categorie_id, nom, description, prix, actif) VALUES
(1, 'Baguette Traditionnelle', 'Baguette française classique, cuite au feu de bois. Croûte dorée croustillante, intérieur moelleux.', 1.20, 1),
(1, 'Pain Complet', 'Pain riche en fibres, à base de farine complète. Idéal pour une alimentation saine et équilibrée.', 1.50, 1),
(1, 'Pain de Mie', 'Pain blanc doux et moelleux, parfait pour les tartines et sandwiches du quotidien.', 2.00, 1),
(1, 'Ciabatta', 'Pain italien rectangulaire avec une belle structure alvéolaire. Croûte croustillante, mie aérée.', 1.80, 1),
(1, 'Pain aux Noix', 'Délicieux pain aux noix concassées, riche en oméga-3. Saveur subtile et nourrissant.', 2.50, 1),
(1, 'Pain à l\'Ancienne', 'Pain rustique de style français traditionnel. Large et généreux pour toute la famille.', 1.80, 1);

-- Produits - VIENNOISERIES (catégorie 2)
INSERT INTO produits (categorie_id, nom, description, prix, actif) VALUES
(2, 'Croissant pur Beurre', 'Croissant au beurre frais, feuilletage délicat et croustillant. Incontournable du petit-déjeuner.', 1.50, 1),
(2, 'Pain au Chocolat', 'Pâte feuilletée généreuse avec deux barres de chocolat noir premium. Chaud et gourmand.', 1.70, 1),
(2, 'Chausson aux Pommes', 'Feuilletage croustillant garni de pommes cuites et de cannelle. Sucré et parfumé.', 1.80, 1),
(2, 'Brioche', 'Brioche moelleuse et dorée au beurre frais. Parfaite pour le petit-déjeuner ou le goûter.', 1.60, 1),
(2, 'Éclair Café', 'Éclair garni de crème pâtissière et surmonté de glaçage café. Saveur classique et élégante.', 2.20, 1),
(2, 'Millefeuille', 'Trois couches de pâte feuille croustillante alternant avec de la crème pâtissière. Haut savoirfaire.', 2.50, 1);

-- Produits - PÂTISSERIES (catégorie 3)
INSERT INTO produits (categorie_id, nom, description, prix, actif) VALUES
(3, 'Tarte aux Fraises', 'Base de pâte sucrée, crème pâtissière et fraises fraîches de saison. Délicieuse et colorée.', 4.50, 1),
(3, 'Opéra', 'Gâteau au moka multicouches avec biscuit, crème au café et glaçage chocolat. Sophistiqué.', 3.80, 1),
(3, 'Forêt Noire', 'Gâteau classique allemand avec cerises, crème fouettée et copeaux de chocolat. Riche et gourmand.', 4.20, 1),
(3, 'Mille-feuille Vanille', 'Pâte feuille avec crème pâtissière à la vanille et glaçage lisse. Intemporel.', 2.80, 1),
(3, 'Religieuse au Chocolat', 'Pâte à choux garnie de crème pâtissière et café, avec glaçage. Deux petites boules élégantes.', 1.90, 1),
(3, 'Tarte au Citron', 'Curd de citron frais et vif sur pâte brisée sucrée. Acidité équilibrée et fraîcheur.', 4.00, 1);

-- Commandes de test
INSERT INTO commandes (user_id, date_commande, statut, total, adresse_livraison, commentaire) VALUES
(2, '2024-02-20 14:30:00', 'en_attente', 12.50, '456, avenue des Fleurs, 75000 Paris', 'Livrer après 18h si possible'),
(3, '2024-02-21 10:15:00', 'prete', 18.75, '789, boulevard Saint-Germain, 75000 Paris', NULL),
(2, '2024-02-22 09:00:00', 'livree', 8.30, '456, avenue des Fleurs, 75000 Paris', 'Merci!');

-- Items des commandes de test
INSERT INTO commande_items (commande_id, produit_id, quantite, prix_unitaire) VALUES
(1, 1, 1, 1.20),
(1, 7, 2, 1.50),
(1, 13, 1, 4.50),
(2, 2, 1, 1.50),
(2, 8, 2, 1.70),
(2, 14, 1, 3.80),
(2, 15, 1, 4.20),
(3, 3, 1, 2.00),
(3, 9, 1, 1.80),
(3, 16, 1, 2.80),
(3, 17, 1, 1.90);

-- Créer un index pour l'optimisation des recherches
CREATE INDEX idx_produits_recherche ON produits(nom, actif);
CREATE INDEX idx_commandes_user_date ON commandes(user_id, date_commande);

-- ============================================================
-- Vérification des données
-- ============================================================

-- Vérifier les utilisateurs
SELECT COUNT(*) as nb_users FROM users;

-- Vérifier les catégories
SELECT COUNT(*) as nb_categories FROM categories;

-- Vérifier les produits
SELECT COUNT(*) as nb_produits FROM produits;

-- Vérifier les commandes
SELECT COUNT(*) as nb_commandes FROM commandes;

-- ============================================================
-- Notes importantes
-- ============================================================
-- Le mot de passe admin et des clients de test est : 'password'
-- (hash généré avec password_hash('password', PASSWORD_DEFAULT))
-- 
-- Compte admin:
--   Email: admin@boulangerie.local
--   Mot de passe: password
--
-- Comptes clients de test:
--   Email: jean@example.com
--   Email: marie@example.com
--   Mot de passe (pour tous): password
