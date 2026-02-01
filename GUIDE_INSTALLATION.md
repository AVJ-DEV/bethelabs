# 📚 Guide d'Installation et d'Utilisation - BetheLabs CRUD

## 🎯 Vue d'ensemble

Système CRUD complet en PHP orienté objet avec:
- ✅ Gestion centralisée des erreurs
- ✅ Architecture MVC
- ✅ Dashboard Bootstrap moderne
- ✅ Authentification sécurisée
- ✅ Système de permissions par rôles
- ✅ Logs d'activités

---

## 📁 Structure du Projet

```
bethelabs/
├── config/
│   ├── Database.php          # Connexion base de données (Singleton)
│   └── ErrorHandler.php      # Gestion centralisée des erreurs
├── models/
│   ├── BaseModel.php         # Modèle de base (CRUD générique)
│   ├── Contact.php           # Modèle Contact
│   ├── News.php              # Modèle Actualités
│   ├── Formation.php         # Modèle Formations
│   └── Admin.php             # Modèle Administrateurs
├── controllers/
│   └── AuthController.php    # Contrôleur d'authentification
├── admin/
│   ├── login.php             # Page de connexion
│   ├── dashboard.php         # Tableau de bord
│   ├── contacts.php          # Gestion contacts (CRUD)
│   └── logout.php            # Déconnexion
└── logs/
    └── errors.log            # Journal des erreurs (créé automatiquement)
```

---

## 🚀 Installation (XAMPP)

### Étape 1: Importer la base de données

1. Ouvrez XAMPP et démarrez **Apache** et **MySQL**
2. Accédez à phpMyAdmin: `http://localhost/phpmyadmin`
3. Créez une nouvelle base de données: `bethelabs_db`
4. Importez votre fichier SQL ou exécutez le script fourni
5. Vérifiez que toutes les tables sont créées

### Étape 2: Configuration du projet

1. Placez tous les fichiers dans: `C:\xampp\htdocs\bethelabs\`

2. Vérifiez la configuration de la base de données dans `config/Database.php`:
```php
private $host = "localhost";
private $db_name = "bethelabs_db";
private $username = "root";
private $password = "";  // Généralement vide sur XAMPP
```

### Étape 3: Créer un administrateur par défaut

Exécutez ce script SQL dans phpMyAdmin:

```sql
-- Créer un administrateur Super Admin
INSERT INTO admins (username, email, password, first_name, last_name, role_id, status) 
VALUES (
    'admin',
    'admin@bethelabs.com',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- password: "password"
    'Admin',
    'Principal',
    1,  -- Super Admin role
    'active'
);
```

**Identifiants par défaut:**
- Username: `admin`
- Password: `password`

⚠️ **Important:** Changez ce mot de passe immédiatement après la première connexion!

### Étape 4: Permissions des dossiers

Assurez-vous que le dossier `logs/` est accessible en écriture:
- Sur Windows (XAMPP): généralement pas de problème
- Sur Linux: `chmod 755 logs/`

---

## 🔐 Première Connexion

1. Ouvrez votre navigateur
2. Accédez à: `http://localhost/bethelabs/admin/login.php`
3. Connectez-vous avec les identifiants par défaut
4. Vous serez redirigé vers le dashboard

---

## 🎨 Fonctionnalités du Système

### 1. Gestion des Erreurs

**Système centralisé** dans `config/ErrorHandler.php`:

- ✅ Capture automatique de toutes les erreurs PHP
- ✅ Logging dans `logs/errors.log`
- ✅ Affichage convivial des erreurs
- ✅ Mode développement/production
- ✅ Gestion des exceptions non capturées

**Exemple d'utilisation:**

```php
try {
    $contact = $contactModel->getById($id);
} catch (Exception $e) {
    // L'erreur est automatiquement loggée
    $error = $e->getMessage();
}
```

### 2. Modèles CRUD (BaseModel)

Toutes les opérations CRUD de base sont héritées:

```php
// CREATE
$id = $model->create([
    'name' => 'John Doe',
    'email' => 'john@example.com'
]);

// READ
$all = $model->getAll();
$one = $model->getById($id);
$paginated = $model->paginate($page, $perPage);

// UPDATE
$model->update($id, [
    'name' => 'Jane Doe'
]);

// DELETE
$model->delete($id);

// SEARCH
$results = $model->search('name', 'John');

// COUNT
$total = $model->count();
```

### 3. Validation des Données

Chaque modèle a sa propre méthode `validate()`:

```php
protected function validate($data, $id = null) {
    $this->clearErrors();
    
    if (empty($data['name'])) {
        $this->addError("Le nom est requis.");
    }
    
    return empty($this->errors);
}
```

### 4. Authentification et Permissions

```php
// Vérifier si connecté
if (AuthController::isLoggedIn()) {
    // Utilisateur connecté
}

// Rediriger si non connecté
AuthController::requireAuth();

// Vérifier une permission
if (AuthController::hasPermission('manage_news')) {
    // Autoriser l'action
}

// Exiger une permission
AuthController::requirePermission('manage_users');

// Logger une action
AuthController::log('create', 'news', 'Création actualité ID: 5');
```

### 5. Réponses JSON pour AJAX

```php
// Succès
ErrorHandler::jsonSuccess('Opération réussie', ['id' => $newId]);

// Erreur
ErrorHandler::jsonError('Une erreur est survenue', 400);
```

---

## 🔧 Créer un Nouveau Module CRUD

### Exemple: Module "Testimonials"

**1. Créer le modèle** (`models/Testimonial.php`):

```php
<?php
require_once __DIR__ . '/BaseModel.php';

class Testimonial extends BaseModel {
    protected $table = 'testimonials';
    protected $fillable = ['name', 'email', 'rating', 'comment', 'status'];

    protected function validate($data, $id = null) {
        $this->clearErrors();
        
        if (empty($data['name'])) {
            $this->addError("Le nom est requis.");
        }
        
        if (empty($data['rating']) || $data['rating'] < 1 || $data['rating'] > 5) {
            $this->addError("La note doit être entre 1 et 5.");
        }
        
        return empty($this->errors);
    }
    
    public function getApproved() {
        try {
            $sql = "SELECT * FROM {$this->table} WHERE status = 'approved' ORDER BY created_at DESC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            ErrorHandler::logError($e);
            throw new Exception("Erreur lors de la récupération.");
        }
    }
}
```

**2. Créer la page CRUD** (`admin/testimonials.php`):

```php
<?php
require_once __DIR__ . '/../config/ErrorHandler.php';
require_once __DIR__ . '/../controllers/AuthController.php';
require_once __DIR__ . '/../models/Testimonial.php';

ErrorHandler::init();
AuthController::requireAuth();

$model = new Testimonial();

// Handle CREATE
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create') {
    try {
        $data = ErrorHandler::sanitize($_POST);
        $id = $model->create($data);
        
        if ($id) {
            AuthController::log('create', 'testimonials', 'Nouveau témoignage ID: ' . $id);
            $success = 'Témoignage créé avec succès.';
        } else {
            $error = implode('<br>', $model->getErrors());
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// Handle UPDATE
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update') {
    try {
        $id = $_POST['id'];
        $data = ErrorHandler::sanitize($_POST);
        
        if ($model->update($id, $data)) {
            AuthController::log('update', 'testimonials', 'Témoignage modifié ID: ' . $id);
            $success = 'Témoignage modifié avec succès.';
        } else {
            $error = implode('<br>', $model->getErrors());
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// Handle DELETE
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    try {
        $id = $_POST['id'];
        $model->delete($id);
        AuthController::log('delete', 'testimonials', 'Témoignage supprimé ID: ' . $id);
        $success = 'Témoignage supprimé avec succès.';
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// Get all records
$testimonials = $model->getAll('created_at DESC');
?>

<!-- Votre HTML avec le tableau et les formulaires -->
```

---

## 📊 Gestion des Erreurs - Bonnes Pratiques

### 1. Toujours entourer de try-catch

```php
try {
    $result = $model->someOperation();
} catch (Exception $e) {
    // L'erreur est automatiquement loggée
    $error = $e->getMessage();
}
```

### 2. Valider avant d'enregistrer

```php
$data = ErrorHandler::sanitize($_POST);

if ($model->create($data)) {
    // Succès
} else {
    // Afficher les erreurs de validation
    $errors = $model->getErrors();
}
```

### 3. Logger les actions importantes

```php
AuthController::log('delete', 'users', 'Utilisateur supprimé: ' . $username);
```

### 4. Mode développement vs production

Dans `config/ErrorHandler.php`:

```php
private static $displayErrors = true;  // Développement
private static $displayErrors = false; // Production
```

---

## 🔒 Sécurité

### Protection incluse:

1. **Requêtes préparées PDO** - Protection SQL Injection
2. **Hachage mot de passe** - bcrypt avec PASSWORD_DEFAULT
3. **Sanitization** - `ErrorHandler::sanitize()`
4. **Sessions sécurisées** - Gestion authentification
5. **Permissions par rôles** - Contrôle d'accès
6. **Logging** - Traçabilité des actions

### Recommandations:

```php
// Toujours sanitizer les entrées utilisateur
$data = ErrorHandler::sanitize($_POST);

// Toujours utiliser htmlspecialchars pour l'affichage
echo htmlspecialchars($user['name']);

// Vérifier les permissions
AuthController::requirePermission('manage_users');
```

---

## 📝 Logs et Débogage

### Consulter les logs:

```
logs/errors.log
```

Exemple de log:

```
[2026-01-28 14:30:45] PDOException: SQLSTATE[42S02]: Base table or view not found
File: /home/claude/models/BaseModel.php
Line: 45
Stack trace:
...
```

### Vider les logs:

Supprimez simplement `logs/errors.log` - il sera recréé automatiquement.

---

## 🎯 Prochaines Étapes

Vous avez maintenant:
- ✅ Base de données configurée
- ✅ Système d'erreurs centralisé
- ✅ Authentification fonctionnelle
- ✅ Dashboard moderne
- ✅ Premier module CRUD (Contacts)

**Pour continuer:**

1. Créez les autres modules CRUD (News, Formations, Concours, etc.)
2. Ajoutez l'upload de fichiers pour les images
3. Implémentez les emails de notification
4. Ajoutez l'export Excel/PDF
5. Créez le frontend public

---

## 🆘 Dépannage

### Erreur: "Cannot connect to database"

- Vérifiez que MySQL est démarré dans XAMPP
- Vérifiez les identifiants dans `config/Database.php`
- Vérifiez que la base `bethelabs_db` existe

### Erreur: "Permission denied" sur logs/

```bash
# Windows: Clic droit sur dossier logs/ > Propriétés > Décocher "Lecture seule"
# Linux: chmod 755 logs/
```

### Page blanche / Erreur 500

Consultez `logs/errors.log` pour voir l'erreur exacte.

---

## 📞 Support

Pour toute question, vérifiez:
1. Les logs d'erreurs
2. La console du navigateur (F12)
3. Les logs Apache/MySQL de XAMPP

---

**Bon développement! 🚀**
