# 🛡️ Architecture de Gestion des Erreurs - BetheLabs

## 📋 Table des Matières

1. [Vue d'ensemble](#vue-densemble)
2. [Composants principaux](#composants-principaux)
3. [Types d'erreurs gérées](#types-derreurs-gérées)
4. [Flux de gestion des erreurs](#flux-de-gestion-des-erreurs)
5. [Exemples pratiques](#exemples-pratiques)
6. [Bonnes pratiques](#bonnes-pratiques)

---

## 🎯 Vue d'ensemble

Le système de gestion des erreurs de BetheLabs est **centralisé**, **robuste** et **extensible**. Il capture automatiquement toutes les erreurs PHP, les exceptions non gérées, et les erreurs fatales, puis les logue et les affiche de manière appropriée selon l'environnement (développement ou production).

### Objectifs:

✅ **Capturer** toutes les erreurs sans exception  
✅ **Logger** pour analyse et débogage  
✅ **Afficher** de manière conviviale à l'utilisateur  
✅ **Sécuriser** en cachant les détails techniques en production  
✅ **Tracer** les actions importantes pour l'audit  

---

## 🧩 Composants principaux

### 1. ErrorHandler (config/ErrorHandler.php)

**Classe centrale** qui gère tous les types d'erreurs.

#### Fonctionnalités:

```php
ErrorHandler::init()                    // Initialise le système d'erreurs
ErrorHandler::logError($exception)      // Logue une erreur dans errors.log
ErrorHandler::jsonError($msg, $code)    // Réponse JSON pour AJAX
ErrorHandler::jsonSuccess($msg, $data)  // Réponse JSON succès
ErrorHandler::sanitize($data)           // Nettoie les données utilisateur
ErrorHandler::validateRequired($data)   // Valide les champs requis
```

#### Configuration:

```php
private static $logFile = __DIR__ . '/../logs/errors.log';
private static $displayErrors = true;  // false en production
```

---

### 2. Database (config/Database.php)

**Connexion sécurisée** avec gestion d'erreurs intégrée.

#### Pattern Singleton:

```php
$db = Database::getInstance()->getConnection();
```

#### Gestion des erreurs:

- Capture les erreurs de connexion PDO
- Logue automatiquement via ErrorHandler
- Lance une exception utilisateur-friendly
- Configure PDO en mode exception

```php
try {
    $this->conn = new PDO(...);
} catch(PDOException $e) {
    ErrorHandler::logError($e);
    throw new Exception("Erreur de connexion à la base de données.");
}
```

---

### 3. BaseModel (models/BaseModel.php)

**Modèle de base** avec gestion d'erreurs pour toutes les opérations CRUD.

#### Méthodes protégées:

```php
protected $errors = [];                 // Stocke les erreurs de validation

protected function validate($data)      // À surcharger dans les modèles enfants
protected function addError($message)   // Ajoute une erreur
public function getErrors()             // Récupère toutes les erreurs
```

#### Gestion d'erreurs dans CRUD:

```php
public function create($data) {
    try {
        if (!$this->validate($data)) {
            return false;  // Erreurs de validation
        }
        // Insert...
    } catch (PDOException $e) {
        ErrorHandler::logError($e);
        $this->errors[] = "Erreur lors de la création.";
        return false;
    }
}
```

---

## 🔍 Types d'erreurs gérées

### 1. Erreurs PHP natives

```php
E_ERROR           // Erreurs fatales
E_WARNING         // Avertissements
E_NOTICE          // Notices
E_PARSE           // Erreurs de parsing
E_STRICT          // Standards stricts
E_DEPRECATED      // Fonctions dépréciées
```

**Capture automatique** via `set_error_handler()`

---

### 2. Exceptions non capturées

```php
set_exception_handler([ErrorHandler::class, 'handleException']);
```

Toute exception non capturée est automatiquement loggée et affichée.

---

### 3. Erreurs fatales

```php
register_shutdown_function([ErrorHandler::class, 'handleShutdown']);
```

Capture les erreurs fatales qui arrêtent l'exécution du script.

---

### 4. Erreurs de base de données (PDO)

```php
PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
```

Toutes les erreurs PDO sont converties en exceptions.

---

### 5. Erreurs de validation

```php
if (!$this->validate($data)) {
    $errors = $this->getErrors();
    // Afficher les erreurs
}
```

Validation métier dans les modèles.

---

## 🔄 Flux de gestion des erreurs

### Scénario 1: Erreur PHP native

```
Code PHP génère une erreur (ex: division par zéro)
           ↓
set_error_handler() capture l'erreur
           ↓
ErrorHandler::handleError() est appelé
           ↓
Erreur loggée dans logs/errors.log
           ↓
Si displayErrors = true → Affichage Bootstrap
Si displayErrors = false → Message générique
```

---

### Scénario 2: Exception non capturée

```
Exception lancée (throw new Exception(...))
           ↓
Aucun try-catch pour la capturer
           ↓
set_exception_handler() capture l'exception
           ↓
ErrorHandler::handleException() est appelé
           ↓
Exception loggée dans logs/errors.log
           ↓
Page d'erreur HTML Bootstrap affichée
           ↓
Exécution stoppée
```

---

### Scénario 3: Erreur de base de données

```
Requête SQL échoue
           ↓
PDO lance une PDOException
           ↓
try-catch dans le modèle capture l'exception
           ↓
ErrorHandler::logError($e) appelé manuellement
           ↓
Exception loggée
           ↓
Exception utilisateur-friendly relancée
           ↓
Capturée dans le contrôleur/page
           ↓
Message affiché à l'utilisateur
```

---

### Scénario 4: Erreur de validation

```
Utilisateur soumet un formulaire
           ↓
Données passées à $model->create($data)
           ↓
$this->validate($data) est appelé
           ↓
Validation échoue → addError() ajoute les erreurs
           ↓
create() retourne false
           ↓
Page vérifie if (!$id) { $errors = $model->getErrors(); }
           ↓
Erreurs affichées à l'utilisateur
```

---

## 💡 Exemples pratiques

### Exemple 1: Création d'un contact avec gestion d'erreurs

```php
<?php
// Page de traitement du formulaire
require_once 'config/ErrorHandler.php';
require_once 'models/Contact.php';

ErrorHandler::init();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Sanitize input
        $data = ErrorHandler::sanitize($_POST);
        
        // Créer le contact
        $contactModel = new Contact();
        $id = $contactModel->create($data);
        
        if ($id) {
            // Succès
            $success = "Contact créé avec succès!";
        } else {
            // Erreurs de validation
            $errors = $contactModel->getErrors();
            $error = implode('<br>', $errors);
        }
        
    } catch (Exception $e) {
        // Erreur technique (DB, etc.)
        // Déjà loggée par ErrorHandler
        $error = $e->getMessage();
    }
}
?>

<!-- Affichage des erreurs -->
<?php if (isset($error)): ?>
    <div class="alert alert-danger">
        <?php echo $error; ?>
    </div>
<?php endif; ?>
```

---

### Exemple 2: Requête AJAX avec gestion d'erreurs JSON

```php
<?php
header('Content-Type: application/json');
require_once 'config/ErrorHandler.php';
require_once 'models/News.php';

ErrorHandler::init();

try {
    // Vérifier la méthode
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception("Méthode non autorisée");
    }
    
    // Sanitize
    $data = ErrorHandler::sanitize($_POST);
    
    // Valider les champs requis
    $errors = ErrorHandler::validateRequired($data, ['title', 'description']);
    if (!empty($errors)) {
        ErrorHandler::jsonError('Champs requis manquants', 400, $errors);
    }
    
    // Créer l'actualité
    $newsModel = new News();
    $id = $newsModel->create($data);
    
    if ($id) {
        ErrorHandler::jsonSuccess('Actualité créée', ['id' => $id]);
    } else {
        $errors = $newsModel->getErrors();
        ErrorHandler::jsonError('Validation échouée', 422, $errors);
    }
    
} catch (Exception $e) {
    ErrorHandler::jsonError($e->getMessage(), 500);
}
```

**JavaScript côté client:**

```javascript
fetch('api/create-news.php', {
    method: 'POST',
    body: formData
})
.then(res => res.json())
.then(data => {
    if (data.success) {
        alert(data.message);
    } else {
        alert('Erreur: ' + data.error);
    }
})
.catch(err => {
    console.error('Erreur réseau:', err);
});
```

---

### Exemple 3: Validation personnalisée dans un modèle

```php
<?php
// models/Formation.php

class Formation extends BaseModel {
    protected $table = 'formations';
    protected $fillable = ['title', 'price', 'level'];
    
    protected function validate($data, $id = null) {
        $this->clearErrors();
        
        // Titre requis
        if (empty($data['title'])) {
            $this->addError("Le titre est requis.");
        } elseif (strlen($data['title']) < 5) {
            $this->addError("Le titre doit contenir au moins 5 caractères.");
        }
        
        // Prix valide
        if (isset($data['price'])) {
            if (!is_numeric($data['price'])) {
                $this->addError("Le prix doit être un nombre.");
            } elseif ($data['price'] < 0) {
                $this->addError("Le prix ne peut pas être négatif.");
            }
        }
        
        // Niveau valide
        $validLevels = ['beginner', 'intermediate', 'advanced'];
        if (isset($data['level']) && !in_array($data['level'], $validLevels)) {
            $this->addError("Niveau invalide. Valeurs acceptées: " . implode(', ', $validLevels));
        }
        
        // Titre unique (sauf pour l'update du même enregistrement)
        if ($this->titleExists($data['title'], $id)) {
            $this->addError("Ce titre existe déjà.");
        }
        
        return empty($this->errors);
    }
    
    private function titleExists($title, $excludeId = null) {
        try {
            $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE title = :title";
            if ($excludeId) {
                $sql .= " AND id != :id";
            }
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':title', $title);
            if ($excludeId) {
                $stmt->bindParam(':id', $excludeId, PDO::PARAM_INT);
            }
            
            $stmt->execute();
            $result = $stmt->fetch();
            return $result['count'] > 0;
            
        } catch (PDOException $e) {
            ErrorHandler::logError($e);
            return false;
        }
    }
}
```

---

### Exemple 4: Logging d'actions administrateur

```php
<?php
// Après une action importante
if ($newsModel->delete($id)) {
    // Logger l'action
    AuthController::log(
        'delete',              // Action
        'news',                // Module
        'Actualité supprimée ID: ' . $id  // Description
    );
    
    $success = "Actualité supprimée avec succès.";
}
```

**Résultat dans la table `admin_logs`:**

| admin_id | action | module | description | ip_address | created_at |
|----------|--------|--------|-------------|------------|------------|
| 1 | delete | news | Actualité supprimée ID: 42 | 127.0.0.1 | 2026-01-28 14:30:00 |

---

## ✅ Bonnes pratiques

### 1. Toujours initialiser ErrorHandler

```php
<?php
require_once 'config/ErrorHandler.php';
ErrorHandler::init();  // TOUJOURS en début de script
```

---

### 2. Try-catch sur toutes les opérations à risque

```php
// ✅ BON
try {
    $result = $model->getById($id);
} catch (Exception $e) {
    $error = $e->getMessage();
}

// ❌ MAUVAIS
$result = $model->getById($id);  // Pas de gestion si erreur
```

---

### 3. Valider avant d'enregistrer

```php
// ✅ BON
if ($model->create($data)) {
    $success = "Créé avec succès";
} else {
    $errors = $model->getErrors();
}

// ❌ MAUVAIS
$model->create($data);  // Ignorer le résultat
```

---

### 4. Sanitizer toutes les entrées utilisateur

```php
// ✅ BON
$data = ErrorHandler::sanitize($_POST);
$name = ErrorHandler::sanitize($_GET['name']);

// ❌ MAUVAIS
$data = $_POST;  // Données brutes non nettoyées
```

---

### 5. Utiliser htmlspecialchars pour l'affichage

```php
// ✅ BON
echo htmlspecialchars($user['name']);

// ❌ MAUVAIS
echo $user['name'];  // Risque XSS
```

---

### 6. Logger les actions importantes

```php
AuthController::log('create', 'users', 'Nouvel utilisateur: ' . $username);
AuthController::log('delete', 'formations', 'Formation supprimée ID: ' . $id);
```

---

### 7. Messages d'erreur utilisateur-friendly

```php
// ✅ BON
throw new Exception("Impossible de supprimer cet enregistrement.");

// ❌ MAUVAIS
throw new Exception("SQLSTATE[23000]: Integrity constraint violation");
```

---

### 8. Ne pas exposer les détails techniques en production

```php
// Dans ErrorHandler.php
private static $displayErrors = false;  // Production

// Les utilisateurs voient:
"Oups ! Une erreur est survenue. Notre équipe a été notifiée."

// Au lieu de:
"PDOException: SQLSTATE[42S02]: Base table 'xyz' not found in /var/www/..."
```

---

## 🔐 Sécurité

### Protection incluse dans le système:

1. **SQL Injection** → Requêtes préparées PDO
2. **XSS** → htmlspecialchars sur affichage + sanitize sur entrée
3. **CSRF** → À implémenter avec tokens (recommandé)
4. **Session Hijacking** → Sessions sécurisées
5. **Information Disclosure** → Masquage erreurs techniques en prod

---

## 📊 Monitoring et Analyse

### Consulter les logs:

```bash
# Voir les dernières erreurs
tail -n 50 logs/errors.log

# Surveiller en temps réel
tail -f logs/errors.log

# Rechercher une erreur spécifique
grep "PDOException" logs/errors.log
```

### Format d'un log:

```
[2026-01-28 14:30:45] PDOException: SQLSTATE[42S02]: Base table or view not found: 1146 Table 'bethelabs_db.xyz' doesn't exist
File: /var/www/bethelabs/models/BaseModel.php
Line: 45
Stack trace:
#0 /var/www/bethelabs/models/BaseModel.php(45): PDOStatement->execute()
#1 /var/www/bethelabs/admin/news.php(25): BaseModel->getAll()
#2 {main}
--------------------------------------------------------------------------------
```

---

## 🎯 Résumé

Le système de gestion des erreurs de BetheLabs offre:

✅ **Capture automatique** de toutes les erreurs  
✅ **Logging centralisé** pour débogage  
✅ **Affichage adapté** selon l'environnement  
✅ **Validation robuste** des données  
✅ **Sécurité renforcée** contre les vulnérabilités  
✅ **Traçabilité** des actions administrateur  
✅ **Facilité d'extension** pour de nouveaux modules  

**Résultat:** Une application robuste, maintenable et sécurisée ! 🚀
