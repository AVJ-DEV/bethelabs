<?php
require_once __DIR__ . '/../config/ErrorHandler.php';
require_once __DIR__ . '/../controllers/AuthController.php';
require_once __DIR__ . '/../models/Admin.php';

ErrorHandler::init();
require_once __DIR__ . '/../config/Csrf.php';
Csrf::init();
AuthController::requireAuth();

// Validate CSRF for POST actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!Csrf::validateToken($_POST['csrf_token'] ?? '')) {
        $error = 'Jeton CSRF invalide. Opération annulée.';
    }
}

// Permission check: only users with 'manage_users' can perform admin management
$currentAdmin = AuthController::getCurrentAdmin();
$canManage = AuthController::hasPermission('manage_users');

$adminModel = new Admin();
$error = '';
$success = '';

// Handle CREATE
if (empty($error) && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create') {
    try {
        if (!$canManage) {
            throw new Exception('Accès refusé : vous n\'avez pas la permission de gérer les administrateurs.');
        }

        $data = ErrorHandler::sanitize($_POST);
        
        if (empty($data['password'])) {
            throw new Exception('Le mot de passe est requis pour un nouvel administrateur.');
        }
        
        $id = $adminModel->create($data);
        
        if ($id) {
            AuthController::log('create', 'admins', 'Administrateur créé ID: ' . $id);
            $success = 'Administrateur créé avec succès.';
            $_POST = [];
        } else {
            $error = implode('<br>', $adminModel->getErrors());
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// Handle UPDATE
if (empty($error) && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update') {
    try {
        if (!$canManage) {
            throw new Exception('Accès refusé : vous n\'avez pas la permission de gérer les administrateurs.');
        }

        $id = $_POST['id'];
        $data = ErrorHandler::sanitize($_POST);
        unset($data['action']);
        unset($data['id']);
        
        if (empty($data['password'])) {
            unset($data['password']);
        }
        
        if ($adminModel->update($id, $data)) {
            AuthController::log('update', 'admins', 'Administrateur modifié ID: ' . $id);
            $success = 'Administrateur modifié avec succès.';
            header('Location: admins.php');
            exit();
        } else {
            $error = implode('<br>', $adminModel->getErrors());
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// Handle DELETE
if (empty($error) && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    try {
        if (!$canManage) {
            throw new Exception('Accès refusé : vous n\'avez pas la permission de gérer les administrateurs.');
        }

        $id = $_POST['id'];
        $allAdmins = $adminModel->getAll();
        if (count($allAdmins) <= 1) {
            throw new Exception('Impossible de supprimer le dernier administrateur.');
        }
        $adminModel->delete($id);
        AuthController::log('delete', 'admins', 'Administrateur supprimé ID: ' . $id);
        $success = 'Administrateur supprimé avec succès.';
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// Get all admins
$allAdmins = $adminModel->getAll('created_at DESC');
$editingAdmin = null;

// Check if editing
if (isset($_GET['edit'])) {
    $editingAdmin = $adminModel->getById($_GET['edit']);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrateurs - BetheLabs Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        :root {
            --sidebar-width: 260px;
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        body {
            background-color: #f8f9fa;
        }

        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: var(--sidebar-width);
            background: var(--primary-gradient);
            color: white;
            overflow-y: auto;
            z-index: 1000;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
        }

        .sidebar-header {
            padding: 25px 20px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .sidebar-menu .nav-link {
            color: rgba(255,255,255,0.8);
            font-size: 0.92rem; /* slightly smaller */
            padding: 8px 12px; /* reduced padding */
            margin: 3px 6px; /* reduced margin */
            border-radius: 8px;
            transition: all 0.2s;
            display: flex;
            align-items: center;
        }

        .sidebar-menu .nav-link:hover,
        .sidebar-menu .nav-link.active {
            background: rgba(255,255,255,0.2);
            color: white;
        }

        .main-content {
            margin-left: var(--sidebar-width);
            padding: 0;
        }

        .top-navbar {
            background: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.08);
            padding: 15px 30px;
            position: sticky;
            top: 0;
            z-index: 999;
        }

        .content-wrapper {
            padding: 30px;
        }

        .form-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.07);
            padding: 25px;
            margin-bottom: 30px;
        }

        .table-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.07);
            overflow: hidden;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <i class="bi bi-grid-fill" style="font-size: 2rem;"></i>
            <h4 class="mt-2">BetheLabs</h4>
            <small class="opacity-75">Panneau d'administration</small>
        </div>

        <nav class="sidebar-menu">
            <a href="dashboard.php" class="nav-link">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
            <a href="contacts.php" class="nav-link">
                <i class="bi bi-envelope"></i> Contacts
            </a>
            <a href="news.php" class="nav-link">
                <i class="bi bi-newspaper"></i> Actualités
            </a>
            <a href="formations.php" class="nav-link">
                <i class="bi bi-book"></i> Formations
            </a>
            <a href="concours.php" class="nav-link">
                <i class="bi bi-trophy"></i> Concours
            </a>
            <a href="testimonials.php" class="nav-link">
                <i class="bi bi-chat-quote"></i> Témoignages
            </a>
            <a href="team.php" class="nav-link">
                <i class="bi bi-people"></i> Équipe
            </a>
            <a href="admins.php" class="nav-link active">
                <i class="bi bi-shield-lock"></i> Administrateurs
            </a>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Navbar -->
        <nav class="top-navbar d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-0"><i class="bi bi-shield-lock me-2"></i>Gestion des Administrateurs</h5>
            </div>
            <div>
                <a href="logout.php" class="btn btn-outline-danger btn-sm">
                    <i class="bi bi-box-arrow-right"></i> Déconnexion
                </a>
            </div>
        </nav>

        <!-- Content -->
        <div class="content-wrapper">
            <?php if ($error): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <?php echo htmlspecialchars($error); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    <?php echo htmlspecialchars($success); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Form Section -->
            <div class="form-card">
                <h5 class="mb-4">
                    <?php echo $editingAdmin ? 'Modifier l\'administrateur' : 'Ajouter un administrateur'; ?>
                </h5>

                <?php if (!$canManage): ?>
                    <div class="alert alert-warning">Vous n'avez pas la permission de gérer les administrateurs.</div>
                <?php else: ?>
                    <form method="POST" class="needs-validation" novalidate>
                        <?php echo \Csrf::inputField(); ?>
                        <input type="hidden" name="action" value="<?php echo $editingAdmin ? 'update' : 'create'; ?>">
                        <?php if ($editingAdmin): ?>
                            <input type="hidden" name="id" value="<?php echo $editingAdmin['id']; ?>">
                        <?php endif; ?>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Nom d'utilisateur *</label>
                                    <input type="text" name="username" class="form-control" required 
                                           value="<?php echo $editingAdmin ? htmlspecialchars($editingAdmin['username']) : ''; ?>">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Email *</label>
                                    <input type="email" name="email" class="form-control" required 
                                           value="<?php echo $editingAdmin ? htmlspecialchars($editingAdmin['email']) : ''; ?>">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Prénom</label>
                                    <input type="text" name="first_name" class="form-control" 
                                           value="<?php echo $editingAdmin ? htmlspecialchars($editingAdmin['first_name']) : ''; ?>">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Nom</label>
                                    <input type="text" name="last_name" class="form-control" 
                                           value="<?php echo $editingAdmin ? htmlspecialchars($editingAdmin['last_name']) : ''; ?>">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">
                                        <?php echo $editingAdmin ? 'Nouveau mot de passe (laisser vide pour conserver)' : 'Mot de passe *'; ?>
                                    </label>
                                    <input type="password" name="password" class="form-control"
                                           <?php echo !$editingAdmin ? 'required' : ''; ?>>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Rôle</label>
                                    <select name="role_id" class="form-select">
                                        <option value="1" <?php echo ($editingAdmin && $editingAdmin['role_id'] == 1) ? 'selected' : ''; ?>>Super Administrateur</option>
                                        <option value="2" <?php echo ($editingAdmin && $editingAdmin['role_id'] == 2) ? 'selected' : ''; ?>>Administrateur</option>
                                        <option value="3" <?php echo ($editingAdmin && $editingAdmin['role_id'] == 3) ? 'selected' : ''; ?>>Modérateur</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Statut</label>
                                    <select name="status" class="form-select">
                                        <option value="active" <?php echo ($editingAdmin && $editingAdmin['status'] === 'active') ? 'selected' : ''; ?>>Actif</option>
                                        <option value="inactive" <?php echo ($editingAdmin && $editingAdmin['status'] === 'inactive') ? 'selected' : ''; ?>>Inactif</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle me-2"></i>
                                <?php echo $editingAdmin ? 'Mettre à jour' : 'Ajouter'; ?>
                            </button>
                            <?php if ($editingAdmin): ?>
                                <a href="admins.php" class="btn btn-secondary">
                                    <i class="bi bi-x-circle me-2"></i>Annuler
                                </a>
                            <?php endif; ?>
                        </div>
                    </form>
                <?php endif; ?>
            </div>

            <!-- Table Section -->
            <div class="table-card">
                <div class="card-header bg-light p-4">
                    <h5 class="mb-0"><i class="bi bi-list me-2"></i>Liste des administrateurs (<?php echo count($allAdmins); ?>)</h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Nom d'utilisateur</th>
                                <th>Email</th>
                                <th>Nom complet</th>
                                <th>Rôle</th>
                                <th>Statut</th>
                                <th>Date d'ajout</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($allAdmins)): ?>
                                <?php foreach ($allAdmins as $admin): ?>
                                    <tr>
                                        <td>
                                            <strong><?php echo htmlspecialchars($admin['username']); ?></strong>
                                        </td>
                                        <td><?php echo htmlspecialchars($admin['email']); ?></td>
                                        <td>
                                            <?php echo htmlspecialchars(($admin['first_name'] ?? '') . ' ' . ($admin['last_name'] ?? '')); ?>
                                        </td>
                                        <td>
                                            <span class="badge bg-primary">
                                                <?php 
                                                    $roles = [1 => 'Super Admin', 2 => 'Admin', 3 => 'Modérateur'];
                                                    echo $roles[$admin['role_id']] ?? 'N/A';
                                                ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?php echo ($admin['status'] === 'active') ? 'success' : 'secondary'; ?>">
                                                <?php echo ($admin['status'] === 'active') ? 'Actif' : 'Inactif'; ?>
                                            </span>
                                        </td>
                                        <td><small><?php echo date('d/m/Y', strtotime($admin['created_at'])); ?></small></td>
                                        <td>
                                            <?php if ($canManage): ?>
                                                <a href="?edit=<?php echo $admin['id']; ?>" class="btn btn-sm btn-primary" title="Modifier">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <?php if (count($allAdmins) > 1): ?>
                                                    <form method="POST" style="display: inline;" onsubmit="return confirm('Confirmer la suppression?');">
                                                        <?php echo \Csrf::inputField(); ?>
                                                        <input type="hidden" name="action" value="delete">
                                                        <input type="hidden" name="id" value="<?php echo $admin['id']; ?>">
                                                        <button type="submit" class="btn btn-sm btn-danger" title="Supprimer">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <span class="text-muted small">Aucune action disponible</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        Aucun administrateur trouvé
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
