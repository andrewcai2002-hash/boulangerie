# Guide d'installation - Boulangerie du Village

## Étapes d'installation rapide (Docker)

### 1. Configuration Docker

Le projet est configuré pour fonctionner avec Docker. Les paramètres sont déjà corrects :
- **Serveur MySQL** : `db` (service Docker)
- **Utilisateur** : `root`
- **Mot de passe** : `root`
- **Site** : `http://localhost:8080/boulangerie/public/`
- **phpMyAdmin** : `http://localhost:8081`

### 2. Importer la base de données

#### Méthode 1 : phpMyAdmin (recommandé)
1. Accéder à http://localhost:8081
2. Se connecter avec:
   - Utilisateur: `root`
   - Mot de passe: `root`
   - Serveur: `db`
3. Créer une nouvelle base de données nommée `boulangerie`
4. Aller dans l'onglet "Importer" et charger `sql/dump.sql`

#### Méthode 2 : En ligne de commande dans le conteneur Docker
```bash
docker exec -i <nom_du_conteneur_db> mysql -u root -proot < sql/dump.sql
```

### 3. Configuration (déjà faite)

Le fichier `includes/config.php` est déjà configuré pour Docker :

```php
define('DB_HOST', 'db');              // Service Docker MySQL
define('DB_NAME', 'boulangerie');     // Nom de la base de données
define('DB_USER', 'root');            // Utilisateur MySQL
define('DB_PASS', 'root');            // Mot de passe MySQL
define('BASE_URL', 'http://localhost:8080/boulangerie');  // URL d'accès
```

### 4. Permissions des dossiers

```bash
# Dossier pour les uploads
chmod 755 assets/img/produits/

# Dossier public
chmod 755 public/
chmod 755 public/admin/

# Dossiers includes
chmod 755 includes/
```

### 5. Accès au site

**Site client:**
- http://localhost:8080/boulangerie/public/
- ou http://boulangerie.local (si Virtual Host configuré)

**Interface admin:**
- Aller sur le site client et cliquer sur "Admin" (une fois connecté)
- ou directement: http://localhost:8080/boulangerie/public/admin/

## Identifiants de test

### Admin
- Email: `admin@boulangerie.local`
- Mot de passe: `password`

### Client 1
- Email: `jean@example.com`
- Mot de passe: `password`

### Client 2
- Email: `marie@example.com`
- Mot de passe: `password`

## Optimisations supplémentaires

### Changer les mots de passe par défaut

1. Se connecter avec un compte (client ou admin)
2. Utiliser la ligne de commande PHP:

```bash
php -r "echo password_hash('votre_nouveau_mot_de_passe', PASSWORD_DEFAULT);"
```

3. Mise à jour dans la base:

```sql
UPDATE users SET mot_de_passe = 'hash_genere' WHERE email = 'admin@boulangerie.local';
```

### Configurer HTTPS

1. Obtenir un certificat SSL (Let's Encrypt par exemple)
2. Configurer Apache avec le certificat
3. Forcer HTTPS dans `includes/config.php`:

```php
if (empty($_SERVER['HTTPS'])) {
    redirect('https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
}
```

### Activer la journalisation des erreurs

Dans `includes/config.php`:

```php
ini_set('log_errors', 1);
ini_set('error_log', PROJECT_ROOT . '/logs/errors.log');
```

Créer le dossier `logs/` avec les bonnes permissions:
```bash
mkdir -p logs
chmod 755 logs
```

## Dépannage

### La page d'accueil est vierge

**Cause:** Apache ne trouve pas l'index.php ou PHP n'est pas activé

**Solution:**
```bash
# Vérifier Apache
apachectl -t

# Vérifier mod_php
apache2ctl -M | grep php
```

### Erreur "Impossible de se connecter à la base de données"

**Causes possibles:**
- MySQL n'est pas en cours d'exécution
- Les identifiants sont incorrects
- La base de données n'existe pas

**Solutions:**
```bash
# Vérifier MySQL
mysql -u root -p -e "SELECT 1;"

# Vérifier la base de données
mysql -u root -p -e "SHOW DATABASES;"

# Réimporter la base si nécessaire
mysql -u root -p < sql/dump.sql
```

### Erreur "Permission denied" sur le dossier uploads

```bash
# Ajouter le propriétaire du serveur au groupe
chown -R www-data:www-data assets/img/produits/
chmod 755 assets/img/produits/
```

### Images ne s'affichent pas

- Vérifier que les fichiers sont téléchargés dans `assets/img/produits/`
- Vérifier que le chemin dans `config.php` est correct
- Vérifier les permissions du dossier

## Sauvegardes

### Sauvegarder la base de données

```bash
# Sauvegarde complète
mysqldump -u root -p boulangerie > boulangerie_backup.sql

# Sauvegarde avec date
mysqldump -u root -p boulangerie > boulangerie_backup_$(date +%Y%m%d).sql
```

### Restaurer une sauvegarde

```bash
mysql -u root -p boulangerie < boulangerie_backup.sql
```

## Support et aide

Consultez le README.md pour:
- Architecture du projet
- Fonctionnalités complètes
- Utilisation

---

**Installation complète!** 🎉
Vous pouvez maintenant accéder au site à http://localhost:8080/boulangerie/public/
