<?php
require_once __DIR__ . '/../config/ErrorHandler.php';
require_once __DIR__ . '/../controllers/AuthController.php';
require_once __DIR__ . '/../models/Partner.php';
require_once __DIR__ . '/../config/MediaManager.php';

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

$currentAdmin = AuthController::getCurrentAdmin();
// Allow Super Admin and Admin to manage partners
$roleName = $currentAdmin['role'] ?? '';
$canManagePartners = in_array($roleName, ['Super Admin', 'Admin']) || AuthController::hasPermission('manage_partners');

$partnerModel = new Partner();
$error = '';
$success = '';

// Handle CREATE
if (empty($error) && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create') {
    try {
        if (!$canManagePartners) throw new Exception('Accès refusé.');

        $data = ErrorHandler::sanitize($_POST);
        $data['status'] = $data['status'] ?? 'draft';

        // Handle image upload
        $media = new MediaManager();
        if (!empty($_FILES['image']['tmp_name'])) {
            $imagePath = $media->uploadImage($_FILES['image']);
            if ($imagePath) $data['image'] = $imagePath;
        }

        $id = $partnerModel->create($data);
        if ($id) {
            AuthController::log('create', 'partners', 'Partenaire créé ID: ' . $id);
            $success = 'Partenaire créé avec succès.';
            $_POST = [];
        } else {
            $error = implode('<br>', $partnerModel->getErrors());
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// Handle UPDATE
if (empty($error) && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update') {
    try {
        if (!$canManagePartners) throw new Exception('Accès refusé.');

        $id = $_POST['id'];
        $data = ErrorHandler::sanitize($_POST);
        unset($data['action']); unset($data['id']);

        $media = new MediaManager();
        if (!empty($_FILES['image']['tmp_name'])) {
            $imagePath = $media->uploadImage($_FILES['image']);
            if ($imagePath) $data['image'] = $imagePath;
        }

        if ($partnerModel->update($id, $data)) {
            AuthController::log('update', 'partners', 'Partenaire modifié ID: ' . $id);
            $success = 'Partenaire modifié avec succès.';
            header('Location: partners.php'); exit();
        } else {
            $error = implode('<br>', $partnerModel->getErrors());
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// Handle DELETE
if (empty($error) && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    try {
        if (!$canManagePartners) throw new Exception('Accès refusé.');
        $id = $_POST['id'];
        $partnerModel->delete($id);
        AuthController::log('delete', 'partners', 'Partenaire supprimé ID: ' . $id);
        $success = 'Partenaire supprimé avec succès.';
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// Handle publish/unpublish toggle
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'toggle_status') {
    try {
        if (!$canManagePartners) throw new Exception('Accès refusé.');
        $id = $_POST['id'];
        $p = $partnerModel->getById($id);
        $new = ($p['status'] === 'published') ? 'draft' : 'published';
        $partnerModel->update($id, ['status' => $new]);
        AuthController::log('update', 'partners', 'Partenaire statut changé ID: ' . $id . ' -> ' . $new);
        $success = 'Statut mis à jour.';
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// Get all partners
$allPartners = $partnerModel->getAll('created_at DESC');
$editingPartner = null;
if (isset($_GET['edit'])) {
    $editingPartner = $partnerModel->getById($_GET['edit']);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Partenaires - BetheLabs Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>
<body>
    <div class="container py-5">
        <h3>Gestion des partenaires</h3>
        <?php if ($error): ?><div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
        <?php if ($success): ?><div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div><?php endif; ?>

        <?php if (!$canManagePartners): ?>
            <div class="alert alert-warning">Vous n'avez pas la permission de gérer les partenaires.</div>
        <?php else: ?>
            <div class="card mb-4">
                <div class="card-body">
                    <h5><?php echo $editingPartner ? 'Modifier le partenaire' : 'Ajouter un partenaire'; ?></h5>
                    <form method="POST" enctype="multipart/form-data">
                        <?php echo \Csrf::inputField(); ?>
                        <input type="hidden" name="action" value="<?php echo $editingPartner ? 'update' : 'create'; ?>">
                        <?php if ($editingPartner): ?><input type="hidden" name="id" value="<?php echo $editingPartner['id']; ?>"><?php endif; ?>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Nom</label>
                                <input type="text" name="name" class="form-control" required value="<?php echo $editingPartner ? htmlspecialchars($editingPartner['name']) : ''; ?>">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Prénom</label>
                                <input type="text" name="firstname" class="form-control" required value="<?php echo $editingPartner ? htmlspecialchars($editingPartner['firstname']) : ''; ?>">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Expertise</label>
                                <input type="text" name="expertise" class="form-control" required value="<?php echo $editingPartner ? htmlspecialchars($editingPartner['expertise']) : ''; ?>">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Image (optionnel)</label>
                            <input type="file" name="image" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Statut</label>
                            <select name="status" class="form-select">
                                <option value="draft" <?php echo ($editingPartner && $editingPartner['status'] === 'draft') ? 'selected' : ''; ?>>Brouillon</option>
                                <option value="published" <?php echo ($editingPartner && $editingPartner['status'] === 'published') ? 'selected' : ''; ?>>Publié</option>
                            </select>
                        </div>

                        <button class="btn btn-primary" type="submit"><?php echo $editingPartner ? 'Mettre à jour' : 'Ajouter'; ?></button>
                        <?php if ($editingPartner): ?><a href="partners.php" class="btn btn-secondary">Annuler</a><?php endif; ?>
                    </form>
                </div>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-header">Liste des partenaires</div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Nom</th>
                                <th>Expertise</th>
                                <th>Image</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($allPartners as $p): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($p['name'] . ' ' . $p['firstname']); ?></td>
                                    <td><?php echo htmlspecialchars($p['expertise']); ?></td>
                                    <td><?php if (!empty($p['image'])): ?><img src="<?php echo htmlspecialchars($p['image']); ?>" alt="" height="40"><?php endif; ?></td>
                                    <td><?php echo htmlspecialchars($p['status'] ?? 'draft'); ?></td>
                                    <td>
                                        <?php if ($canManagePartners): ?>
                                            <a href="?edit=<?php echo $p['id']; ?>" class="btn btn-sm btn-primary">Modifier</a>
                                            <form method="POST" style="display:inline;" onsubmit="return confirm('Confirmer la suppression?')">
                                                <?php echo \Csrf::inputField(); ?>
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="id" value="<?php echo $p['id']; ?>">
                                                <button class="btn btn-sm btn-danger">Supprimer</button>
                                            </form>
                                            <form method="POST" style="display:inline;">
                                                <?php echo \Csrf::inputField(); ?>
                                                <input type="hidden" name="action" value="toggle_status">
                                                <input type="hidden" name="id" value="<?php echo $p['id']; ?>">
                                                <button class="btn btn-sm btn-<?php echo ($p['status'] === 'published') ? 'secondary' : 'success'; ?>">
                                                    <?php echo ($p['status'] === 'published') ? 'Retirer' : 'Publier'; ?>
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <span class="text-muted small">Aucune action</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</body>
</html>