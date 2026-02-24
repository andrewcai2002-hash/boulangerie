# Boulangerie du Village - Site e-commerce

## Description

Site web complet de e-commerce pour la **Boulangerie du Village**, permettant aux clients de consulter l'offre de produits, créer un compte, composer un panier et passer des commandes en ligne.

Interface d'administration pour gérer le catalogue de produits et le suivi des commandes.

## Fonctionnalités

### Pour les clients

- ✅ Consulter le catalogue de produits (pains, viennoiseries, pâtisseries)
- ✅ Filtrer les produits par catégorie
- ✅ Créer un compte utilisateur
- ✅ Se connecter / déconnecter
- ✅ Gérer un panier (ajouter, modifier, supprimer des articles)
- ✅ Passer une commande avec récapitulatif
- ✅ Suivi simple des commandes
- ✅ Consulter les informations pratiques (horaires, zones de livraison, contact)

### Pour l'administrateur

- ✅ Dashboard avec statistiques
- ✅ Gérer le catalogue (ajouter, modifier, activer/désactiver les produits)
- ✅ Gérer les commandes (voir la liste, consulter les détails)
- ✅ Mettre à jour le statut des commandes (en attente → prête → livrée)
- ✅ Upload d'images pour les produits

## Technologies utilisées

- **Frontend:** HTML5, CSS3, JavaScript ES6
- **Backend:** PHP 7.4+
- **Base de données:** MySQL 5.7+
- **Serveur web:** Apache avec mod_rewrite

## Structure du projet

```
boulangerie/
├── public/
│   ├── index.php              # Accueil
│   ├── catalogue.php          # Catalogue avec filtres
│   ├── inscription.php        # Formulaire d'inscription
│   ├── connexion.php          # Formulaire de connexion
│   ├── deconnexion.php        # Déconnexion
│   ├── panier.php             # Gestion du panier
│   ├── commande_recap.php     # Récapitulatif avant commande
│   ├── commande_confirmation.php  # Confirmation après commande
│   ├── informations.php       # Informations pratiques
│   ├── admin/
│   │   ├── index.php          # Dashboard admin
│   │   ├── commandes.php      # Liste des commandes
│   │   ├── commande_detail.php   # Détails d'une commande
│   │   ├── produits.php       # Gestion du catalogue
│   │   └── produit_edit.php   # Édition/ajout de produit
│   └── assets/
│       ├── css/style.css      # Styles globaux
│       ├── js/main.js         # Logique JavaScript
│       └── img/
│           └── produits/      # Images des produits
├── includes/
│   ├── config.php             # Configuration
│   ├── db.php                 # Fonctions de base de données
│   ├── auth.php               # Gestion de l'authentification
│   ├── functions.php          # Fonctions utilitaires
│   ├── header.php             # En-tête HTML
│   └── footer.php             # Pied de page HTML
├── sql/
│   └── dump.sql               # Dump SQL complet
└── README.md                  # Ce fichier
```

## Installation

### 1. Prerequisites

- PHP 7.4 ou supérieur
- MySQL 5.7 ou supérieur
- Serveur web Apache (ou équivalent)

### 2. Configuration du serveur web

Copier les fichiers du projet dans le répertoire web (ex: `/var/www/html/boulangerie/`)

### 3. Créer la base de données

1. Ouvrir phpMyAdmin ou un client MySQL
2. Exécuter le script SQL:
   ```bash
   mysql -u root -p < sql/dump.sql
   ```

Ou en ligne de commande directement:
```bash
mysql -u root -p boulangerie < sql/dump.sql
```

### 4. Configurer la connexion à la base de données

Éditer `includes/config.php` et ajuster les paramètres si nécessaire:

```php
define('DB_HOST', 'db');      // Hôte MySQL (service Docker)
define('DB_NAME', 'boulangerie');    // Nom de la BDD
define('DB_USER', 'root');           // Utilisateur MySQL
define('DB_PASS', 'root');               // Mot de passe MySQL
define('BASE_URL', 'http://localhost:8080/boulangerie');
```

### 5. Permissions des dossiers

S'assurer que le serveur web peut écrire dans le dossier de uploads:
```bash
chmod 755 assets/img/produits/
```

### 6. Accès au site

- **Site client:** http://localhost:8080/boulangerie/public/
- **Admin:** http://localhost:8080/boulangerie/public/admin/ (après connexion admin)

## Comptes de test

### Administrateur
- **Email:** admin@boulangerie.local
- **Mot de passe:** password

### Client 1
- **Email:** jean@example.com
- **Mot de passe:** password

### Client 2
- **Email:** marie@example.com
- **Mot de passe:** password

## Utilisation

### Pour les clients

1. **Consulter le catalogue:** Cliquer sur "Catalogue" dans le menu
2. **Filtrer par catégorie:** Utiliser le menu latéral des catégories
3. **Créer un compte:** Cliquer sur "Inscription" (optionnel mais nécessaire pour commander)
4. **Ajouter au panier:** Entrer une quantité et cliquer sur "Ajouter au panier"
5. **Passer une commande:** 
   - Aller au panier
   - Cliquer sur "Valider ma commande"
   - Remplir le récapitulatif
   - Confirmer la commande
6. **Demander l'aide:** Aller à "Informations" pour les détails de livraison

### Pour l'administrateur

1. **Se connecter:** Utiliser le compte admin avec email et mot de passe
2. **Accéder au dashboard:** Cliquer sur le bouton "Admin" ou aller à `/public/admin/`
3. **Gérer les produits:** 
   - Cliquer sur "Gérer les produits"
   - Ajouter, éditer, activer/désactiver des produits
   - Télécharger des images pour les produits
4. **Gérer les commandes:**
   - Cliquer sur "Gérer les commandes"
   - Voir la liste avec filtres de statut
   - Cliquer "Détails" pour voir les articles
   - Mettre à jour le statut (En attente → Prête → Livrée)

## Sécurité

### Implémentation

- ✅ Hashage des mots de passe avec `password_hash()`
- ✅ Vérification avec `password_verify()`
- ✅ Requêtes préparées (PDO) pour éviter les injections SQL
- ✅ Échappement HTML avec `htmlspecialchars()`
- ✅ Sessions PHP pour l'authentification
- ✅ Contrôle d'accès: `require_login()` et `require_admin()`

### Recommendations

- Changer les mots de passe par défaut après installation
- Utiliser HTTPS en production
- Mettre en place un certificat SSL/TLS
- Faire des sauvegardes régulières de la base de données
- Maintenir PHP et MySQL à jour

## Problèmes courants

### "Erreur de connexion à la base de données"
- Vérifier que MySQL est démarré
- Vérifier les paramètres de connexion dans `includes/config.php`
- Vérifier les droits d'utilisateur MySQL

### "Page blank ou erreurs 500"
- Vérifier les logs PHP/Apache
- Vérifier que PHP 7.4+ est installé
- S'assurer que l'extension PDO MySQL est activée

### "Images ne s'affichent pas"
- Vérifier les permissions du dossier `assets/img/produits/`
- Vérifier que les fichiers d'images ont été correctement uploadés
- Vérifier le chemin d'accès dans `includes/config.php`

## Améliorations futures possibles

- Intégration de paiement en ligne (Stripe, PayPal)
- Système d'avis clients
- Recherche avancée de produits
- Newsletter et notifications email
- Historique des commandes pour les clients
- Rapport de ventes pour l'admin
- Gestion des stocks
- Codes promotionnels/réductions

## Support

Pour toute question ou problème, veuillez consulter le code source ou contacter le développeur.

## Licence

Ce projet est fourni à titre d'exemple pédagogique.

---

**Boulangerie du Village - 2024**
