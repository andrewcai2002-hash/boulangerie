-- Base de données : boulangerie_du_village
CREATE DATABASE IF NOT EXISTS boulangerie CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE boulangerie;

-- Table utilisateurs
CREATE TABLE utilisateurs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    mot_de_passe VARCHAR(255) NOT NULL,
    role ENUM('client','admin') DEFAULT 'client',
    adresse TEXT,
    telephone VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table catégories
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    description TEXT
);

-- Table produits
CREATE TABLE produits (
    id INT AUTO_INCREMENT PRIMARY KEY,
    categorie_id INT NOT NULL,
    nom VARCHAR(150) NOT NULL,
    description TEXT,
    prix DECIMAL(6,2) NOT NULL,
    image VARCHAR(255) DEFAULT 'default.jpg',
    disponible TINYINT(1) DEFAULT 1,
    FOREIGN KEY (categorie_id) REFERENCES categories(id) ON DELETE CASCADE
);

-- Table commandes
CREATE TABLE commandes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    utilisateur_id INT NOT NULL,
    statut ENUM('en_attente','prete','livree','annulee') DEFAULT 'en_attente',
    total DECIMAL(8,2) NOT NULL,
    adresse_livraison TEXT NOT NULL,
    commentaire TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id)
);

-- Table lignes de commande
CREATE TABLE commande_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    commande_id INT NOT NULL,
    produit_id INT NOT NULL,
    quantite INT NOT NULL,
    prix_unitaire DECIMAL(6,2) NOT NULL,
    FOREIGN KEY (commande_id) REFERENCES commandes(id) ON DELETE CASCADE,
    FOREIGN KEY (produit_id) REFERENCES produits(id)
);

-- Données initiales
INSERT INTO utilisateurs (nom, prenom, email, mot_de_passe, role) VALUES
('Admin', 'Boulangerie', 'admin@boulangerie.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');
-- Mot de passe admin : password

INSERT INTO categories (nom, description) VALUES
('Pains', 'Nos pains artisanaux cuits au feu de bois'),
('Viennoiseries', 'Croissants, brioches et pains au chocolat'),
('Pâtisseries', 'Gâteaux, tartes et entremets maison');

INSERT INTO produits (categorie_id, nom, description, prix) VALUES
(1, 'Pain de campagne', 'Pain rustique à la mie dense, croûte croustillante', 3.50),
(1, 'Baguette tradition', 'Baguette française façonnée à la main', 1.20),
(1, 'Pain aux céréales', 'Mélange de graines de tournesol, lin et sésame', 4.00),
(2, 'Croissant au beurre', 'Croissant pur beurre, feuilletage délicat', 1.50),
(2, 'Pain au chocolat', 'Deux barres de chocolat noir dans une pâte feuilletée', 1.60),
(2, 'Brioche tressée', 'Brioche maison moelleuse et dorée', 5.50),
(3, 'Tarte aux pommes', 'Pommes caramélisées sur pâte sablée maison', 4.50),
(3, 'Éclair au café', 'Pâte à choux garnie de crème au café', 3.20),
(3, 'Mille-feuille', 'Crème pâtissière vanille entre feuilletages dorés', 3.80);
