# 🔧 Guide de Dépannage - Erreur d'Authentification

## Problème: "Erreur lors de l'authentification"

Vous rencontrez cette erreur lorsque vous essayez de vous connecter avec les identifiants par défaut.

---

## ✅ Étape 1: Diagnostic Rapide

Accédez à la page de diagnostic:
```
http://localhost/bethelabs/admin/diagnostic.php
```

Cette page va vérifier:
- ✓ La connexion à la base de données
- ✓ L'existence des tables requises
- ✓ L'existence de l'administrateur "admin"
- ✓ La validité du mot de passe
- ✓ Les rôles et permissions
- ✓ Les permissions des fichiers

---

## 🔍 Étape 2: Causes Possibles

### Cause 1: L'administrateur n'existe pas dans la base de données

**Symptôme:** Le diagnostic affiche "Admin 'admin' n'existe pas ou est inactif"

**Solution:**

1. Accédez à: `http://localhost/bethelabs/admin/setup_admin.php`
2. Cliquez sur "Créer l'administrateur par défaut"
3. Essayez de vous connecter à nouveau

Ou exécutez ce script SQL dans phpMyAdmin:

```sql
-- Se connecter à bethelabs_db d'abord
USE bethelabs_db;

-- Insérer l'administrateur par défaut
INSERT INTO admins (username, email, password, first_name, last_name, role_id, status) 
VALUES (
    'admin',
    'admin@bethelabs.com',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'Admin',
    'Principal',
    1,
    'active'
);

-- Vérification
SELECT * FROM admins WHERE username = 'admin';
```

---

### Cause 2: Le mot de passe est incorrect

**Symptôme:** Le diagnostic affiche "Le mot de passe 'password' ne correspond pas"

**Solution 1: Réinitialiser le mot de passe**

Exécutez ce script SQL:

```sql
USE bethelabs_db;

-- Réinitialiser le mot de passe à "password"
UPDATE admins 
SET password = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'
WHERE username = 'admin';

-- Vérification
SELECT id, username, status FROM admins WHERE username = 'admin';
```

**Solution 2: Créer un nouvel hash de mot de passe**

Si vous voulez utiliser un mot de passe différent, visitez:
```
https://www.php.net/manual/en/function.password-hash.php
```

Puis utilisez ce hash dans la commande UPDATE.

---

### Cause 3: L'administrateur est inactif

**Symptôme:** Le diagnostic affiche "Admin 'admin' existe mais est inactif"

**Solution:**

Exécutez ce script SQL:

```sql
USE bethelabs_db;

-- Activer l'administrateur
UPDATE admins 
SET status = 'active'
WHERE username = 'admin';

-- Vérification
SELECT id, username, status FROM admins WHERE username = 'admin';
```

---

### Cause 4: Les tables n'existent pas

**Symptôme:** Le diagnostic affiche "Table non trouvée - Exécutez le script SQL"

**Solution:**

1. Ouvrez phpMyAdmin: `http://localhost/phpmyadmin`
2. Sélectionnez la base de données `bethelabs_db`
3. Allez à l'onglet "Importer"
4. Sélectionnez le fichier `db.sql` de votre projet
5. Cliquez sur "Exécuter"

---

### Cause 5: Les rôles n'existent pas

**Symptôme:** Le diagnostic affiche "0 rôles configurés"

**Solution:**

Exécutez ce script SQL:

```sql
USE bethelabs_db;

-- Créer les rôles
INSERT INTO roles (role_name, description) VALUES
('Super Admin', 'Full access to all features and settings'),
('Admin', 'Can manage content and users'),
('Editor', 'Can create and edit content'),
('Moderator', 'Can moderate comments and submissions');

-- Vérification
SELECT * FROM roles;
```

---

### Cause 6: Problème de connexion à la base de données

**Symptôme:** Le diagnostic affiche "Erreur de connexion MySQL"

**Solution:**

1. Vérifiez que MySQL est démarré dans XAMPP
2. Ouvrez phpMyAdmin pour tester la connexion
3. Vérifiez les paramètres dans `config/Database.php`:

```php
private $host = "localhost";
private $db_name = "bethelabs_db";
private $username = "root";
private $password = "";  // Vérifiez le mot de passe MySQL
```

---

## 🚀 Étape 3: Après le Dépannage

Une fois que le diagnostic affiche tout en vert ✓:

1. Accédez à: `http://localhost/bethelabs/admin/login.php`
2. Connectez-vous avec:
   - **Username:** `admin`
   - **Password:** `password`
3. Vous devriez être redirigé vers le dashboard

---

## ⚠️ Étape 4: Première Connexion

Après votre première connexion réussie:

1. Allez dans le tableau de bord
2. Naviguez vers "Gestion des Administrateurs" ou "Profil"
3. **Changez immédiatement le mot de passe par défaut**

---

## 🔍 Débogage Avancé

### Vérifier les logs d'erreur

Les logs sont stockés dans: `logs/errors.log`

Ouvrez ce fichier pour voir les erreurs détaillées.

### Vérifier la console PHP

Dans phpMyAdmin:

```sql
USE bethelabs_db;

-- Voir tous les administrateurs
SELECT id, username, email, status, created_at FROM admins;

-- Voir tous les rôles
SELECT id, role_name FROM roles;

-- Voir les rôles assignés
SELECT a.username, r.role_name 
FROM admins a
LEFT JOIN roles r ON a.role_id = r.id;
```

### Tester manuellement le hash du mot de passe

Créez un fichier `test_password.php`:

```php
<?php
$password = "password";
$hash = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi';

if (password_verify($password, $hash)) {
    echo "✓ Le mot de passe est correct";
} else {
    echo "✗ Le mot de passe est incorrect";
}
?>
```

Accédez à: `http://localhost/bethelabs/test_password.php`

---

## 📞 Besoin d'aide?

1. **Vérifiez le diagnostic:** `http://localhost/bethelabs/admin/diagnostic.php`
2. **Consultez les logs:** `logs/errors.log`
3. **Vérifiez phpMyAdmin:** `http://localhost/phpmyadmin`
4. **Réexécutez le setup:** `http://localhost/bethelabs/admin/setup_admin.php`

---

**Si le problème persiste après ces étapes, vérifiez les logs détaillés pour plus d'informations.**
