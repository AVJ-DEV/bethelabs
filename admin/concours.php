<?php
require_once __DIR__ . '/../config/ErrorHandler.php';
require_once __DIR__ . '/../config/MediaManager.php';
require_once __DIR__ . '/../controllers/AuthController.php';
require_once __DIR__ . '/../models/Concours.php';

ErrorHandler::init();
AuthController::requireAuth();

require_once __DIR__ . '/../config/Csrf.php';
Csrf::init();

$concoursModel = new Concours();
$mediaManager = new MediaManager();
$error = '';
$success = ''; 

// Validate CSRF for POST actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!Csrf::validateToken($_POST['csrf_token'] ?? '')) {
        $error = 'Jeton CSRF invalide. Opération annulée.';
    }
} 

// Handle CREATE
if (empty($error) && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create') {
    try {
        $data = ErrorHandler::sanitize($_POST);
        
        // Gérer l'upload d'image
        if (!empty($_FILES['image']['tmp_name'])) {
            $imagePath = $mediaManager->uploadImage($_FILES['image']);
            if ($imagePath) {
                $data['image'] = $imagePath;
            } else {
                throw new Exception('Erreur upload image: ' . implode(', ', $mediaManager->getErrors()));
            }
        }
        
        // Gérer l'upload de vidéo
        if (!empty($_FILES['video']['tmp_name'])) {
            $videoPath = $mediaManager->uploadVideo($_FILES['video']);
            if ($videoPath) {
                $data['video'] = $videoPath;
            } else {
                throw new Exception('Erreur upload vidéo: ' . implode(', ', $mediaManager->getErrors()));
            }
        }
        
        $id = $concoursModel->create($data);
        
        if ($id) {
            AuthController::log('create', 'concours', 'Nouveau concours créé ID: ' . $id);
            $success = 'Concours créé avec succès.';
        } else {
            $error = implode('<br>', $concoursModel->getErrors());
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// Handle UPDATE
if (empty($error) && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update') {
    try {
        $id = $_POST['id'];
        $data = ErrorHandler::sanitize($_POST);
        unset($data['action']);
        unset($data['id']);
        
        // Gérer l'upload d'image
        if (!empty($_FILES['image']['tmp_name'])) {
            $imagePath = $mediaManager->uploadImage($_FILES['image']);
            if ($imagePath) {
                $data['image'] = $imagePath;
            } else {
                throw new Exception('Erreur upload image: ' . implode(', ', $mediaManager->getErrors()));
            }
        }
        
        // Gérer l'upload de vidéo
        if (!empty($_FILES['video']['tmp_name'])) {
            $videoPath = $mediaManager->uploadVideo($_FILES['video']);
            if ($videoPath) {
                $data['video'] = $videoPath;
            } else {
                throw new Exception('Erreur upload vidéo: ' . implode(', ', $mediaManager->getErrors()));
            }
        }
        
        if ($concoursModel->update($id, $data)) {
            AuthController::log('update', 'concours', 'Concours modifié ID: ' . $id);
            $success = 'Concours modifié avec succès.';
        } else {
            $error = implode('<br>', $concoursModel->getErrors());
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// Handle DELETE
if (empty($error) && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    try {
        $id = $_POST['id'];
        $concoursModel->delete($id);
        AuthController::log('delete', 'concours', 'Concours supprimé ID: ' . $id);
        $success = 'Concours supprimé avec succès.';
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
} 

// Get all concours
$allConcours = $concoursModel->getAll('created_at DESC');
$editingConcours = null;

// Check if editing
if (isset($_GET['edit'])) {
    $editingConcours = $concoursModel->getById($_GET['edit']);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Concours - BetheLabs Admin</title>
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
            padding: 12px 20px;
            margin: 4px 10px;
            border-radius: 8px;
            transition: all 0.3s;
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
            <a href="concours.php" class="nav-link active">
                <i class="bi bi-trophy"></i> Concours
            </a>
            <a href="testimonials.php" class="nav-link">
                <i class="bi bi-chat-quote"></i> Témoignages
            </a>
            <a href="team.php" class="nav-link">
                <i class="bi bi-people"></i> Équipe
            </a>
            <a href="admins.php" class="nav-link">
                <i class="bi bi-shield-lock"></i> Administrateurs
            </a>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Navbar -->
        <nav class="top-navbar d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-0"><i class="bi bi-trophy me-2"></i>Gestion des Concours</h5>
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
                    <?php echo $editingConcours ? 'Modifier le concours' : 'Ajouter un concours'; ?>
                </h5>
                
                <form method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
                    <?php echo \Csrf::inputField(); ?>
                    <input type="hidden" name="action" value="<?php echo $editingConcours ? 'update' : 'create'; ?>">
                    <?php if ($editingConcours): ?> 
                        <input type="hidden" name="id" value="<?php echo $editingConcours['id']; ?>">
                    <?php endif; ?>

                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label class="form-label">Titre *</label>
                                <input type="text" name="title" class="form-control" required 
                                       value="<?php echo $editingConcours ? htmlspecialchars($editingConcours['title']) : ''; ?>">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Description *</label>
                                <textarea name="description" class="form-control" rows="3" required><?php echo $editingConcours ? htmlspecialchars($editingConcours['description']) : ''; ?></textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Règles</label>
                                <textarea name="rules" class="form-control" rows="4"><?php echo $editingConcours ? htmlspecialchars($editingConcours['rules']) : ''; ?></textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Prix/Récompenses</label>
                                <textarea name="prizes" class="form-control" rows="3"><?php echo $editingConcours ? htmlspecialchars($editingConcours['prizes']) : ''; ?></textarea>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Date de début *</label>
                                <input type="datetime-local" name="start_date" class="form-control" required
                                       value="<?php echo $editingConcours ? str_replace(' ', 'T', substr($editingConcours['start_date'], 0, 16)) : ''; ?>">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Date de fin *</label>
                                <input type="datetime-local" name="end_date" class="form-control" required
                                       value="<?php echo $editingConcours ? str_replace(' ', 'T', substr($editingConcours['end_date'], 0, 16)) : ''; ?>">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Nombre max de participants</label>
                                <input type="number" name="max_participants" class="form-control"
                                       value="<?php echo $editingConcours ? $editingConcours['max_participants'] : ''; ?>">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Image (JPG, PNG - max 5MB)</label>
                                <input type="file" name="image" class="form-control" accept="image/*">
                                <?php if ($editingConcours && !empty($editingConcours['image'])): ?>
                                    <small class="text-muted d-block mt-2">
                                        <i class="bi bi-check-circle text-success"></i> Image actuelle: <a href="<?php echo htmlspecialchars($editingConcours['image']); ?>" target="_blank">voir</a>
                                    </small>
                                <?php endif; ?>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Vidéo (MP4, WebM - max 5MB)</label>
                                <input type="file" name="video" class="form-control" accept="video/*">
                                <?php if ($editingConcours && !empty($editingConcours['video'])): ?>
                                    <small class="text-muted d-block mt-2">
                                        <i class="bi bi-check-circle text-success"></i> Vidéo actuelle: <a href="<?php echo htmlspecialchars($editingConcours['video']); ?>" target="_blank">voir</a>
                                    </small>
                                <?php endif; ?>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Statut</label>
                                <select name="status" class="form-select">
                                    <option value="upcoming" <?php echo ($editingConcours && $editingConcours['status'] === 'upcoming') ? 'selected' : ''; ?>>À venir</option>
                                    <option value="active" <?php echo ($editingConcours && $editingConcours['status'] === 'active') ? 'selected' : ''; ?>>Actif</option>
                                    <option value="closed" <?php echo ($editingConcours && $editingConcours['status'] === 'closed') ? 'selected' : ''; ?>>Fermé</option>
                                    <option value="completed" <?php echo ($editingConcours && $editingConcours['status'] === 'completed') ? 'selected' : ''; ?>>Terminé</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-2"></i>
                            <?php echo $editingConcours ? 'Mettre à jour' : 'Ajouter'; ?>
                        </button>
                        <?php if ($editingConcours): ?>
                            <a href="concours.php" class="btn btn-secondary">
                                <i class="bi bi-x-circle me-2"></i>Annuler
                            </a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <!-- Table Section -->
            <div class="table-card">
                <div class="card-header bg-light p-4">
                    <h5 class="mb-0"><i class="bi bi-list me-2"></i>Liste des concours (<?php echo count($allConcours); ?>)</h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Titre</th>
                                <th>Dates</th>
                                <th>Participants</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($allConcours)): ?>
                                <?php foreach ($allConcours as $concours): ?>
                                    <tr>
                                        <td>
                                            <strong><?php echo htmlspecialchars(substr($concours['title'], 0, 40)); ?></strong>
                                        </td>
                                        <td>
                                            <small><?php echo date('d/m/Y', strtotime($concours['start_date'])); ?> - <?php echo date('d/m/Y', strtotime($concours['end_date'])); ?></small>
                                        </td>
                                        <td>
                                            <span class="badge bg-primary"><?php echo $concours['current_participants']; ?>/<?php echo $concours['max_participants'] ?? '∞'; ?></span>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?php 
                                                echo ($concours['status'] === 'active') ? 'success' : 
                                                     (($concours['status'] === 'upcoming') ? 'warning' : 
                                                     (($concours['status'] === 'completed') ? 'secondary' : 'danger'));
                                            ?>">
                                                <?php echo ucfirst($concours['status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="?edit=<?php echo $concours['id']; ?>" class="btn btn-sm btn-primary" title="Modifier">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <form method="POST" style="display: inline;" onsubmit="return confirm('Confirmer la suppression?');">
                                                <?php echo \Csrf::inputField(); ?>
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="id" value="<?php echo $concours['id']; ?>">
                                                <button type="submit" class="btn btn-sm btn-danger" title="Supprimer">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">
                                        Aucun concours trouvé
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
