<?php
require_once __DIR__ . '/../config/ErrorHandler.php';
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/Admin.php';

ErrorHandler::init();

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuration Admin - BetheLabs</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .setup-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            padding: 40px;
            max-width: 600px;
            width: 100%;
        }
        .alert {
            border-radius: 10px;
        }
    </style>
</head>
<body>
    <div class="setup-card">
        <h2 class="mb-4"><i class="bi bi-shield-check"></i> Configuration Administrateur</h2>

        <?php
        try {
            // Get database connection
            $db = Database::getInstance();
            $pdo = $db->getConnection();

            // Check if admins table exists
            $checkTable = $pdo->prepare("SELECT COUNT(*) FROM information_schema.TABLES WHERE TABLE_SCHEMA = 'bethelabs_db' AND TABLE_NAME = 'admins'");
            $checkTable->execute();
            $tableExists = $checkTable->fetchColumn() > 0;

            if (!$tableExists) {
                echo '<div class="alert alert-danger">
                    <strong>Erreur:</strong> La table "admins" n\'existe pas. Veuillez d\'abord exécuter le script SQL pour créer les tables.
                </div>';
            } else {
                // Check if default admin exists
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM admins WHERE username = 'admin'");
                $stmt->execute();
                $adminExists = $stmt->fetchColumn() > 0;

                if ($adminExists) {
                    echo '<div class="alert alert-info">
                        <strong>Info:</strong> Un administrateur "admin" existe déjà dans la base de données.
                    </div>';
                } else {
                    // Create default admin
                    $adminModel = new Admin();
                    
                    $defaultPassword = 'password';
                    $hashedPassword = password_hash($defaultPassword, PASSWORD_DEFAULT);
                    
                    $data = [
                        'username' => 'admin',
                        'email' => 'admin@bethelabs.com',
                        'password' => $hashedPassword,
                        'first_name' => 'Admin',
                        'last_name' => 'Principal',
                        'role_id' => 1,
                        'status' => 'active'
                    ];

                    // Insert directly into database
                    $sql = "INSERT INTO admins (username, email, password, first_name, last_name, role_id, status) 
                            VALUES (:username, :email, :password, :first_name, :last_name, :role_id, :status)";
                    
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute($data);

                    echo '<div class="alert alert-success">
                        <strong>✓ Succès!</strong> L\'administrateur par défaut a été créé avec succès.
                    </div>';
                }

                // Display credentials
                echo '<div class="alert alert-warning">
                    <strong>Identifiants de connexion:</strong><br>
                    <strong>Username:</strong> <code>admin</code><br>
                    <strong>Password:</strong> <code>password</code><br><br>
                    ⚠️ <strong>Important:</strong> Changez ce mot de passe immédiatement après votre première connexion!
                </div>';

                // Display existing admins
                $stmt = $pdo->prepare("SELECT id, username, email, status FROM admins");
                $stmt->execute();
                $admins = $stmt->fetchAll();

                if (count($admins) > 0) {
                    echo '<h5 class="mt-4">Administrateurs existants:</h5>';
                    echo '<table class="table table-sm table-bordered">';
                    echo '<thead><tr><th>ID</th><th>Username</th><th>Email</th><th>Statut</th></tr></thead>';
                    echo '<tbody>';
                    foreach ($admins as $admin) {
                        $statusBadge = $admin['status'] === 'active' 
                            ? '<span class="badge bg-success">Actif</span>' 
                            : '<span class="badge bg-danger">Inactif</span>';
                        echo '<tr>';
                        echo '<td>' . htmlspecialchars($admin['id']) . '</td>';
                        echo '<td>' . htmlspecialchars($admin['username']) . '</td>';
                        echo '<td>' . htmlspecialchars($admin['email']) . '</td>';
                        echo '<td>' . $statusBadge . '</td>';
                        echo '</tr>';
                    }
                    echo '</tbody>';
                    echo '</table>';
                }

                echo '<div class="mt-4">';
                echo '<a href="login.php" class="btn btn-primary btn-lg w-100"><i class="bi bi-box-arrow-in-right"></i> Aller à la connexion</a>';
                echo '</div>';
            }

        } catch (Exception $e) {
            echo '<div class="alert alert-danger">
                <strong>Erreur:</strong> ' . htmlspecialchars($e->getMessage()) . '
            </div>';
            
            if (file_exists(__DIR__ . '/../logs/errors.log')) {
                echo '<details class="mt-3">';
                echo '<summary>Voir les détails</summary>';
                echo '<pre class="mt-2">' . htmlspecialchars(file_get_contents(__DIR__ . '/../logs/errors.log')) . '</pre>';
                echo '</details>';
            }
        }
        ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
