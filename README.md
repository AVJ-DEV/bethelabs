# 🚀 BetheLabs - Système CRUD Complet

Système de gestion complet en PHP orienté objet avec gestion centralisée des erreurs, dashboard Bootstrap moderne et architecture MVC.

## ✨ Fonctionnalités

- ✅ **Gestion centralisée des erreurs** - Capture et logging automatique
- ✅ **Architecture MVC** - Code organisé et maintenable
- ✅ **CRUD complet** - Toutes opérations Create, Read, Update, Delete
- ✅ **Dashboard moderne** - Interface Bootstrap 5 responsive
- ✅ **Authentification sécurisée** - Sessions et permissions par rôles
- ✅ **Validation des données** - Côté serveur avec messages clairs
- ✅ **Logging d'activités** - Traçabilité des actions admin
- ✅ **Pagination** - Navigation efficace des données
- ✅ **Recherche** - Fonctionnalité de recherche intégrée
- ✅ **Sécurité** - Protection SQL Injection, XSS, sessions sécurisées

## 📁 Structure du Projet

```
bethelabs/
├── config/
│   ├── Database.php          # Connexion DB (Singleton)
│   └── ErrorHandler.php      # Gestion des erreurs
├── models/
│   ├── BaseModel.php         # CRUD générique
│   ├── Contact.php
│   ├── News.php
│   ├── Formation.php
│   └── Admin.php
├── controllers/
│   └── AuthController.php
├── admin/
│   ├── login.php
│   ├── dashboard.php
│   ├── contacts.php
│   └── logout.php
├── logs/
│   └── errors.log            # Logs automatiques
├── GUIDE_INSTALLATION.md     # Guide complet
├── ARCHITECTURE_ERREURS.md   # Doc gestion erreurs
└── README.md                 # Ce fichier
```

## 🚀 Installation Rapide

### 1. Prérequis

- XAMPP (Apache + MySQL + PHP 7.4+)
- Navigateur web moderne

### 2. Installation

1. **Démarrer XAMPP**
   - Lancer Apache et MySQL

2. **Créer la base de données**
   - Ouvrir phpMyAdmin: `http://localhost/phpmyadmin`
   - Créer la base: `bethelabs_db`
   - Importer votre fichier SQL

3. **Installer le projet**
   - Placer les fichiers dans: `C:\xampp\htdocs\bethelabs\`

4. **Créer un admin**
   ```sql
   INSERT INTO admins (username, email, password, role_id, status) 
   VALUES ('admin', 'admin@bethelabs.com', 
           '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
           1, 'active');
   ```

5. **Accéder au dashboard**
   - URL: `http://localhost/bethelabs/admin/login.php`
   - Username: `admin`
   - Password: `password`

## 📚 Documentation

- **[GUIDE_INSTALLATION.md](GUIDE_INSTALLATION.md)** - Guide complet d'installation et d'utilisation
- **[ARCHITECTURE_ERREURS.md](ARCHITECTURE_ERREURS.md)** - Documentation détaillée de la gestion des erreurs

## 🎯 Utilisation

### Créer un nouveau module CRUD

1. Créer le modèle dans `models/`
2. Hériter de `BaseModel`
3. Définir `$table` et `$fillable`
4. Surcharger `validate()` si nécessaire
5. Créer la page admin dans `admin/`

Exemple minimal:

```php
<?php
class MyModel extends BaseModel {
    protected $table = 'my_table';
    protected $fillable = ['name', 'email'];
    
    protected function validate($data, $id = null) {
        $this->clearErrors();
        if (empty($data['name'])) {
            $this->addError("Le nom est requis.");
        }
        return empty($this->errors);
    }
}
```

### Utiliser le modèle

```php
// CREATE
$id = $model->create(['name' => 'Test', 'email' => 'test@test.com']);

// READ
$all = $model->getAll();
$one = $model->getById(1);

// UPDATE
$model->update(1, ['name' => 'Nouveau nom']);

// DELETE
$model->delete(1);

// PAGINATION
$data = $model->paginate($page, $perPage);

// SEARCH
$results = $model->search('name', 'recherche');
```

## 🔐 Sécurité

### Protection incluse

- ✅ Requêtes préparées PDO (SQL Injection)
- ✅ Hachage bcrypt des mots de passe
- ✅ Sanitization des entrées (XSS)
- ✅ htmlspecialchars sur l'affichage
- ✅ Sessions sécurisées
- ✅ Permissions par rôles
- ✅ Logging des actions

### Recommandations

```php
// Toujours sanitizer
$data = ErrorHandler::sanitize($_POST);

// Toujours échapper l'affichage
echo htmlspecialchars($user['name']);

// Toujours vérifier les permissions
AuthController::requirePermission('manage_users');
```

## 🛠️ Gestion des Erreurs

### Initialisation

```php
require_once 'config/ErrorHandler.php';
ErrorHandler::init();  // En début de chaque script
```

### Try-Catch

```php
try {
    $result = $model->someOperation();
} catch (Exception $e) {
    // Erreur déjà loggée automatiquement
    $error = $e->getMessage();
}
```

### Validation

```php
if ($model->create($data)) {
    // Succès
} else {
    // Erreurs de validation
    $errors = $model->getErrors();
}
```

### Logs

Consultez: `logs/errors.log`

## 📊 Modules Inclus

- ✅ **Contacts** - Gestion des messages de contact
- ✅ **Authentification** - Login/Logout avec sessions
- ✅ **Dashboard** - Statistiques et aperçu

### À créer (templates fournis):

- News / Actualités
- Formations
- Concours
- Témoignages
- Équipe
- Administrateurs
- Médias

## 🎨 Interface

Dashboard moderne avec:
- Sidebar navigation
- Statistiques en temps réel
- Tableaux interactifs
- Recherche et pagination
- Modals Bootstrap
- Design responsive
- Gradient moderne

## 🔧 Configuration

### Database (config/Database.php)

```php
private $host = "localhost";
private $db_name = "bethelabs_db";
private $username = "root";
private $password = "";
```

### ErrorHandler (config/ErrorHandler.php)

```php
private static $displayErrors = true;  // false en production
```

## 📞 Dépannage

### Erreur de connexion DB
- Vérifier que MySQL est démarré
- Vérifier les credentials dans `config/Database.php`

### Page blanche
- Consulter `logs/errors.log`
- Vérifier les permissions du dossier `logs/`

### Erreur 404
- Vérifier le chemin d'installation
- Vérifier la configuration Apache

## 📄 Licence

Projet développé pour AGBEMATCHI Venceslas Jean-de-Dieu.

## 🤝 Contribution

Pour toute question ou amélioration, consultez la documentation complète dans les fichiers MD.

---

**Développé avec AVJ pour BetheLabs**

🚀 **Bon développement!**
