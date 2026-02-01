-- Script de création d'un administrateur par défaut
-- À exécuter dans phpMyAdmin après avoir créé la base de données

USE bethelabs_db;

-- Créer l'administrateur Super Admin par défaut
-- Username: admin
-- Password: password (à changer immédiatement après la première connexion!)
INSERT INTO admins (username, email, password, first_name, last_name, role_id, status) 
VALUES (
    'admin',
    'admin@bethelabs.com',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',  -- password
    'Admin',
    'Principal',
    1,  -- Super Admin role
    'active'
);

-- Vérification
SELECT id, username, email, role_id, status, created_at 
FROM admins 
WHERE username = 'admin';

-- Note: Le mot de passe par défaut est "password"
-- IMPORTANT: Changez ce mot de passe immédiatement après votre première connexion!
