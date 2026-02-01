<?php
require_once __DIR__ . '/../config/ErrorHandler.php';
require_once __DIR__ . '/../config/MediaManager.php';
require_once __DIR__ . '/../controllers/AuthController.php';
require_once __DIR__ . '/../models/News.php';

ErrorHandler::init();
Require_once __DIR__ . '/../config/Csrf.php';
Csrf::init();
AuthController::requireAuth();

// Validate CSRF for POST actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!Csrf::validateToken($_POST['csrf_token'] ?? '')) {
        $error = 'Jeton CSRF invalide. Opération annulée.';
    }
}

$newsModel = new News();
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
        
        $id = $newsModel->create($data);
        
        if ($id) {
            AuthController::log('create', 'news', 'Nouvelle actualité créée ID: ' . $id);
            $success = 'Actualité créée avec succès.';
        } else {
            $error = implode('<br>', $newsModel->getErrors());
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
        
        if ($newsModel->update($id, $data)) {
            AuthController::log('update', 'news', 'Actualité modifiée ID: ' . $id);
            $success = 'Actualité modifiée avec succès.';
        } else {
            $error = implode('<br>', $newsModel->getErrors());
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// Handle DELETE
if (empty($error) && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    try {
        $id = $_POST['id'];
        $newsModel->delete($id);
        AuthController::log('delete', 'news', 'Actualité supprimée ID: ' . $id);
        $success = 'Actualité supprimée avec succès.';
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// Get all news
$allNews = $newsModel->getAll('created_at DESC');
$editingNews = null;

// Check if editing
if (isset($_GET['edit'])) {
    $editingNews = $newsModel->getById($_GET['edit']);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actualités - BetheLabs Admin</title>
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
            <a href="news.php" class="nav-link active">
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
                <h5 class="mb-0"><i class="bi bi-newspaper me-2"></i>Gestion des Actualités</h5>
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
                    <?php echo $editingNews ? 'Modifier l\'actualité' : 'Ajouter une actualité'; ?>
                </h5>
                
                <form method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
                    <?php echo \Csrf::inputField(); ?>
                    <input type="hidden" name="action" value="<?php echo $editingNews ? 'update' : 'create'; ?>">
                    <?php if ($editingNews): ?>
                        <input type="hidden" name="id" value="<?php echo $editingNews['id']; ?>">
                    <?php endif; ?>

                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label class="form-label">Titre *</label>
                                <input type="text" name="title" class="form-control" required 
                                       value="<?php echo $editingNews ? htmlspecialchars($editingNews['title']) : ''; ?>">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Description courte *</label>
                                <input type="text" name="description" class="form-control" required 
                                       value="<?php echo $editingNews ? htmlspecialchars($editingNews['description']) : ''; ?>">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Contenu *</label>
                                <textarea name="content" class="form-control" rows="6" required><?php echo $editingNews ? htmlspecialchars($editingNews['content']) : ''; ?></textarea>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Auteur</label>
                                <input type="text" name="author" class="form-control" 
                                       value="<?php echo $editingNews ? htmlspecialchars($editingNews['author']) : ''; ?>">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Image (JPG, PNG - max 5MB)</label>
                                <input type="file" name="image" class="form-control" accept="image/*">
                                <?php if ($editingNews && !empty($editingNews['image'])): ?>
                                    <small class="text-muted d-block mt-2">
                                        <i class="bi bi-check-circle text-success"></i> Image actuelle: <a href="<?php echo htmlspecialchars($editingNews['image']); ?>" target="_blank">voir</a>
                                    </small>
                                <?php endif; ?>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Vidéo (MP4, WebM - max 5MB)</label>
                                <input type="file" name="video" class="form-control" accept="video/*">
                                <?php if ($editingNews && !empty($editingNews['video'])): ?>
                                    <small class="text-muted d-block mt-2">
                                        <i class="bi bi-check-circle text-success"></i> Vidéo actuelle: <a href="<?php echo htmlspecialchars($editingNews['video']); ?>" target="_blank">voir</a>
                                    </small>
                                <?php endif; ?>>
                                <small class="text-muted">Chemin de l'image</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Vidéo</label>
                                <input type="text" name="video" class="form-control" 
                                       value="<?php echo $editingNews ? htmlspecialchars($editingNews['video']) : ''; ?>">
                                <small class="text-muted">URL de la vidéo</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Statut</label>
                                <select name="status" class="form-select">
                                    <option value="draft" <?php echo ($editingNews && $editingNews['status'] === 'draft') ? 'selected' : ''; ?>>Brouillon</option>
                                    <option value="published" <?php echo ($editingNews && $editingNews['status'] === 'published') ? 'selected' : ''; ?>>Publié</option>
                                    <option value="archived" <?php echo ($editingNews && $editingNews['status'] === 'archived') ? 'selected' : ''; ?>>Archivé</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-2"></i>
                            <?php echo $editingNews ? 'Mettre à jour' : 'Ajouter'; ?>
                        </button>
                        <?php if ($editingNews): ?>
                            <a href="news.php" class="btn btn-secondary">
                                <i class="bi bi-x-circle me-2"></i>Annuler
                            </a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <!-- Table Section -->
            <div class="table-card">
                <div class="card-header bg-light p-4">
                    <h5 class="mb-0"><i class="bi bi-list me-2"></i>Liste des actualités (<?php echo count($allNews); ?>)</h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Titre</th>
                                <th>Auteur</th>
                                <th>Statut</th>
                                <th>Vues</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($allNews)): ?>
                                <?php foreach ($allNews as $news): ?>
                                    <tr>
                                        <td>
                                            <strong><?php echo htmlspecialchars(substr($news['title'], 0, 40)); ?></strong>
                                            <?php if (strlen($news['title']) > 40) echo '...'; ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($news['author'] ?? 'N/A'); ?></td>
                                        <td>
                                            <span class="badge bg-<?php 
                                                echo ($news['status'] === 'published') ? 'success' : 
                                                     (($news['status'] === 'draft') ? 'warning' : 'secondary');
                                            ?>">
                                                <?php echo ucfirst($news['status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-info"><?php echo $news['views']; ?></span>
                                        </td>
                                        <td><small><?php echo date('d/m/Y', strtotime($news['created_at'])); ?></small></td>
                                        <td>
                                            <a href="?edit=<?php echo $news['id']; ?>" class="btn btn-sm btn-primary" title="Modifier">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <form method="POST" style="display: inline;" onsubmit="return confirm('Confirmer la suppression?');">
                                                <?php echo \Csrf::inputField(); ?>
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="id" value="<?php echo $news['id']; ?>">
                                                <button type="submit" class="btn btn-sm btn-danger" title="Supprimer">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">
                                        Aucune actualité trouvée
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
