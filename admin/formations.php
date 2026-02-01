<?php
require_once __DIR__ . '/../config/ErrorHandler.php';
require_once __DIR__ . '/../config/MediaManager.php';
require_once __DIR__ . '/../controllers/AuthController.php';
require_once __DIR__ . '/../models/Formation.php';

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

$formationModel = new Formation();
$mediaManager = new MediaManager();
$error = '';
$success = '';

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
        
        $id = $formationModel->create($data);
        
        if ($id) {
            AuthController::log('create', 'formations', 'Nouvelle formation créée ID: ' . $id);
            $success = 'Formation créée avec succès.';
        } else {
            $error = implode('<br>', $formationModel->getErrors());
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
        
        if ($formationModel->update($id, $data)) {
            AuthController::log('update', 'formations', 'Formation modifiée ID: ' . $id);
            $success = 'Formation modifiée avec succès.';
        } else {
            $error = implode('<br>', $formationModel->getErrors());
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// Handle DELETE
if (empty($error) && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    try {
        $id = $_POST['id'];
        $formationModel->delete($id);
        AuthController::log('delete', 'formations', 'Formation supprimée ID: ' . $id);
        $success = 'Formation supprimée avec succès.';
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// Get all formations
$allFormations = $formationModel->getAll('created_at DESC');
$editingFormation = null;

// Check if editing
if (isset($_GET['edit'])) {
    $editingFormation = $formationModel->getById($_GET['edit']);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formations - BetheLabs Admin</title>
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
            <a href="formations.php" class="nav-link active">
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
                <h5 class="mb-0"><i class="bi bi-book me-2"></i>Gestion des Formations</h5>
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
                    <?php echo $editingFormation ? 'Modifier la formation' : 'Ajouter une formation'; ?>
                </h5>
                
                <form method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
                    <?php echo \Csrf::inputField(); ?>
                    <input type="hidden" name="action" value="<?php echo $editingFormation ? 'update' : 'create'; ?>">
                    <?php if ($editingFormation): ?>
                        <input type="hidden" name="id" value="<?php echo $editingFormation['id']; ?>">
                    <?php endif; ?>

                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label class="form-label">Titre *</label>
                                <input type="text" name="title" class="form-control" required 
                                       value="<?php echo $editingFormation ? htmlspecialchars($editingFormation['title']) : ''; ?>">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Description courte *</label>
                                <input type="text" name="description" class="form-control" required 
                                       value="<?php echo $editingFormation ? htmlspecialchars($editingFormation['description']) : ''; ?>">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Contenu *</label>
                                <textarea name="content" class="form-control" rows="6" required><?php echo $editingFormation ? htmlspecialchars($editingFormation['content']) : ''; ?></textarea>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Catégorie</label>
                                <input type="text" name="category" class="form-control" 
                                       value="<?php echo $editingFormation ? htmlspecialchars($editingFormation['category']) : ''; ?>">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Niveau</label>
                                <select name="level" class="form-select">
                                    <option value="beginner" <?php echo ($editingFormation && $editingFormation['level'] === 'beginner') ? 'selected' : ''; ?>>Débutant</option>
                                    <option value="intermediate" <?php echo ($editingFormation && $editingFormation['level'] === 'intermediate') ? 'selected' : ''; ?>>Intermédiaire</option>
                                    <option value="advanced" <?php echo ($editingFormation && $editingFormation['level'] === 'advanced') ? 'selected' : ''; ?>>Avancé</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Durée</label>
                                <input type="text" name="duration" class="form-control" placeholder="ex: 2 semaines"
                                       value="<?php echo $editingFormation ? htmlspecialchars($editingFormation['duration']) : ''; ?>">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Prix</label>
                                <input type="number" name="price" class="form-control" step="0.01"
                                       value="<?php echo $editingFormation ? $editingFormation['price'] : ''; ?>">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Instructeur</label>
                                <input type="text" name="instructor" class="form-control" 
                                       value="<?php echo $editingFormation ? htmlspecialchars($editingFormation['instructor']) : ''; ?>">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Image (JPG, PNG - max 5MB)</label>
                                <input type="file" name="image" class="form-control" accept="image/*">
                                <?php if ($editingFormation && !empty($editingFormation['image'])): ?>
                                    <small class="text-muted d-block mt-2">
                                        <i class="bi bi-check-circle text-success"></i> Image actuelle: <a href="<?php echo htmlspecialchars($editingFormation['image']); ?>" target="_blank">voir</a>
                                    </small>
                                <?php endif; ?>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Vidéo (MP4, WebM - max 5MB)</label>
                                <input type="file" name="video" class="form-control" accept="video/*">
                                <?php if ($editingFormation && !empty($editingFormation['video'])): ?>
                                    <small class="text-muted d-block mt-2">
                                        <i class="bi bi-check-circle text-success"></i> Vidéo actuelle: <a href="<?php echo htmlspecialchars($editingFormation['video']); ?>" target="_blank">voir</a>
                                    </small>
                                <?php endif; ?>
                                <label class="form-label">Vidéo</label>
                                <input type="text" name="video" class="form-control" 
                                       value="<?php echo $editingFormation ? htmlspecialchars($editingFormation['video']) : ''; ?>">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Statut</label>
                                <select name="status" class="form-select">
                                    <option value="planned" <?php echo ($editingFormation && $editingFormation['status'] === 'planned') ? 'selected' : ''; ?>>Planifiée</option>
                                    <option value="active" <?php echo ($editingFormation && $editingFormation['status'] === 'active') ? 'selected' : ''; ?>>Active</option>
                                    <option value="completed" <?php echo ($editingFormation && $editingFormation['status'] === 'completed') ? 'selected' : ''; ?>>Complétée</option>
                                    <option value="cancelled" <?php echo ($editingFormation && $editingFormation['status'] === 'cancelled') ? 'selected' : ''; ?>>Annulée</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-2"></i>
                            <?php echo $editingFormation ? 'Mettre à jour' : 'Ajouter'; ?>
                        </button>
                        <?php if ($editingFormation): ?>
                            <a href="formations.php" class="btn btn-secondary">
                                <i class="bi bi-x-circle me-2"></i>Annuler
                            </a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <!-- Table Section -->
            <div class="table-card">
                <div class="card-header bg-light p-4">
                    <h5 class="mb-0"><i class="bi bi-list me-2"></i>Liste des formations (<?php echo count($allFormations); ?>)</h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Titre</th>
                                <th>Catégorie</th>
                                <th>Niveau</th>
                                <th>Instructeur</th>
                                <th>Statut</th>
                                <th>Prix</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($allFormations)): ?>
                                <?php foreach ($allFormations as $formation): ?>
                                    <tr>
                                        <td>
                                            <strong><?php echo htmlspecialchars(substr($formation['title'], 0, 30)); ?></strong>
                                        </td>
                                        <td><?php echo htmlspecialchars($formation['category'] ?? 'N/A'); ?></td>
                                        <td>
                                            <span class="badge bg-info"><?php echo ucfirst($formation['level']); ?></span>
                                        </td>
                                        <td><?php echo htmlspecialchars($formation['instructor'] ?? 'N/A'); ?></td>
                                        <td>
                                            <span class="badge bg-<?php 
                                                echo ($formation['status'] === 'active') ? 'success' : 
                                                     (($formation['status'] === 'planned') ? 'warning' : 
                                                     (($formation['status'] === 'completed') ? 'secondary' : 'danger'));
                                            ?>">
                                                <?php echo ucfirst($formation['status']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo $formation['price'] ? number_format($formation['price'], 2) . ' €' : 'Gratuit'; ?></td>
                                        <td>
                                            <a href="?edit=<?php echo $formation['id']; ?>" class="btn btn-sm btn-primary" title="Modifier">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <form method="POST" style="display: inline;" onsubmit="return confirm('Confirmer la suppression?');">
                                                <?php echo \Csrf::inputField(); ?>
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="id" value="<?php echo $formation['id']; ?>">
                                                <button type="submit" class="btn btn-sm btn-danger" title="Supprimer">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        Aucune formation trouvée
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
