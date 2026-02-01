<?php
require_once __DIR__ . '/../config/ErrorHandler.php';
require_once __DIR__ . '/../config/Database.php';

ErrorHandler::init();

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Diagnostic - BetheLabs</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body {
            background: #f5f5f5;
            padding: 20px;
        }
        .diagnostic-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 20px;
            margin-bottom: 20px;
        }
        .check-item {
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px;
            border-left: 4px solid;
        }
        .check-ok {
            border-left-color: #28a745;
            background-color: #f0f9f4;
        }
        .check-error {
            border-left-color: #dc3545;
            background-color: #fef5f5;
        }
        .check-warning {
            border-left-color: #ffc107;
            background-color: #fffbf0;
        }
    </style>
</head>
<body>
    <div class="container-lg">
        <h1 class="mb-4"><i class="bi bi-tools"></i> Diagnostic du Système</h1>

        <?php
        function addCheck($status, $title, $message = '') {
            $class = '';
            $icon = '';
            
            switch ($status) {
                case 'ok':
                    $class = 'check-ok';
                    $icon = '<i class="bi bi-check-circle text-success"></i>';
                    break;
                case 'error':
                    $class = 'check-error';
                    $icon = '<i class="bi bi-x-circle text-danger"></i>';
                    break;
                case 'warning':
                    $class = 'check-warning';
                    $icon = '<i class="bi bi-exclamation-circle text-warning"></i>';
                    break;
            }
            
            echo '<div class="check-item ' . $class . '">';
            echo $icon . ' <strong>' . $title . '</strong>';
            if ($message) {
                echo '<br><small>' . $message . '</small>';
            }
            echo '</div>';
        }

        try {
            // 1. Database Connection
            echo '<div class="diagnostic-card">';
            echo '<h5><i class="bi bi-database"></i> Base de Données</h5>';
            
            try {
                $db = Database::getInstance();
                addCheck('ok', 'Connexion MySQL', 'Connecté avec succès à la base de données');
            } catch (Exception $e) {
                addCheck('error', 'Connexion MySQL', 'Erreur: ' . $e->getMessage());
                throw $e;
            }

            // 2. Check tables
            echo '<h6 class="mt-3">Tables</h6>';
            $tables = ['roles', 'permissions', 'role_permissions', 'admins', 'admin_logs', 'news', 'formations', 'concours', 'media'];
            
            foreach ($tables as $table) {
                try {
                    $pdo = $db->getConnection();
                    $stmt = $pdo->prepare("SELECT COUNT(*) FROM information_schema.TABLES WHERE TABLE_SCHEMA = 'bethelabs_db' AND TABLE_NAME = :table");
                    $stmt->execute(['table' => $table]);
                    $exists = $stmt->fetchColumn() > 0;
                    
                    if ($exists) {
                        $countStmt = $pdo->prepare("SELECT COUNT(*) FROM " . $table);
                        $countStmt->execute();
                        $count = $countStmt->fetchColumn();
                        addCheck('ok', "Table: $table", "($count enregistrements)");
                    } else {
                        addCheck('error', "Table: $table", 'Table non trouvée - Exécutez le script SQL');
                    }
                } catch (Exception $e) {
                    addCheck('error', "Table: $table", $e->getMessage());
                }
            }

            echo '</div>';

            // 3. Admin & Authentication
            echo '<div class="diagnostic-card">';
            echo '<h5><i class="bi bi-shield-check"></i> Authentification</h5>';
            
            try {
                $pdo = $db->getConnection();
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM admins WHERE username = 'admin' AND status = 'active'");
                $stmt->execute();
                $adminCount = $stmt->fetchColumn();
                
                if ($adminCount > 0) {
                    addCheck('ok', 'Administrateur par défaut', 'Admin "admin" existe et est actif');
                    
                    // Test password verification
                    $stmt = $pdo->prepare("SELECT password FROM admins WHERE username = 'admin'");
                    $stmt->execute();
                    $adminData = $stmt->fetch();
                    
                    if (password_verify('password', $adminData['password'])) {
                        addCheck('ok', 'Mot de passe', 'Le mot de passe "password" est valide');
                    } else {
                        addCheck('warning', 'Mot de passe', 'Le mot de passe "password" ne correspond pas');
                    }
                } else {
                    addCheck('error', 'Administrateur par défaut', 'Admin "admin" n\'existe pas ou est inactif');
                    echo '<div class="alert alert-info mt-3">';
                    echo '<strong>Solution:</strong> ';
                    echo '<a href="setup_admin.php" class="btn btn-sm btn-primary">Créer l\'administrateur par défaut</a>';
                    echo '</div>';
                }
            } catch (Exception $e) {
                addCheck('error', 'Vérification Admin', $e->getMessage());
            }

            echo '</div>';

            // 4. Roles & Permissions
            echo '<div class="diagnostic-card">';
            echo '<h5><i class="bi bi-lock"></i> Rôles & Permissions</h5>';
            
            try {
                $pdo = $db->getConnection();
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM roles");
                $stmt->execute();
                $roleCount = $stmt->fetchColumn();
                
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM permissions");
                $stmt->execute();
                $permCount = $stmt->fetchColumn();
                
                if ($roleCount > 0) {
                    addCheck('ok', "Rôles", "$roleCount rôles configurés");
                } else {
                    addCheck('warning', "Rôles", "Aucun rôle configuré");
                }
                
                if ($permCount > 0) {
                    addCheck('ok', "Permissions", "$permCount permissions configurées");
                } else {
                    addCheck('warning', "Permissions", "Aucune permission configurée");
                }
            } catch (Exception $e) {
                addCheck('error', 'Rôles/Permissions', $e->getMessage());
            }

            echo '</div>';

            // 5. Files & Permissions
            echo '<div class="diagnostic-card">';
            echo '<h5><i class="bi bi-folder"></i> Fichiers & Dossiers</h5>';
            
            $logDir = __DIR__ . '/../logs';
            if (is_dir($logDir) && is_writable($logDir)) {
                addCheck('ok', 'Dossier logs/', 'Accessible et accessible en écriture');
            } else {
                addCheck('error', 'Dossier logs/', 'Non accessible ou non accessible en écriture');
            }

            $uploadDir = __DIR__ . '/../uploads';
            if (!is_dir($uploadDir)) {
                @mkdir($uploadDir, 0755, true);
            }
            
            if (is_dir($uploadDir) && is_writable($uploadDir)) {
                addCheck('ok', 'Dossier uploads/', 'Accessible et accessible en écriture');
            } else {
                addCheck('warning', 'Dossier uploads/', 'Création automatique tentée');
            }

            echo '</div>';

            // 6. PHP Configuration
            echo '<div class="diagnostic-card">';
            echo '<h5><i class="bi bi-gear"></i> Configuration PHP</h5>';
            
            addCheck('ok', 'Version PHP', phpversion());
            
            $extensions = ['pdo', 'pdo_mysql', 'openssl', 'hash'];
            foreach ($extensions as $ext) {
                if (extension_loaded($ext)) {
                    addCheck('ok', "Extension $ext", 'Activée');
                } else {
                    addCheck('error', "Extension $ext", 'Non activée');
                }
            }

            echo '</div>';

            // 7. Next Steps
            echo '<div class="diagnostic-card alert alert-info">';
            echo '<h5><i class="bi bi-arrow-right-circle"></i> Étapes suivantes</h5>';
            echo '<ol>';
            echo '<li>Vérifiez tous les diagnostics ci-dessus</li>';
            echo '<li>Si l\'admin n\'existe pas, <a href="setup_admin.php">créez-le ici</a></li>';
            echo '<li>Allez à <a href="login.php">la page de connexion</a></li>';
            echo '<li>Connectez-vous avec: <strong>admin</strong> / <strong>password</strong></li>';
            echo '</ol>';
            echo '</div>';

        } catch (Exception $e) {
            echo '<div class="alert alert-danger">';
            echo '<strong>Erreur Fatale:</strong> ' . htmlspecialchars($e->getMessage());
            echo '</div>';
        }
        ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
