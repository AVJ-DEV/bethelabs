<?php
require_once __DIR__ . '/../config/ErrorHandler.php';
require_once __DIR__ . '/../controllers/AuthController.php';
require_once __DIR__ . '/../models/Contact.php';

ErrorHandler::init();
AuthController::requireAuth();

require_once __DIR__ . '/../config/Csrf.php';
Csrf::init();

$currentAdmin = AuthController::getCurrentAdmin();
$contactModel = new Contact();

$success = '';
$error = '';

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!Csrf::validateToken($_POST['csrf_token'] ?? '')) {
        $error = 'Jeton CSRF invalide. Opération annulée.';
    }

    $action = $_POST['action'] ?? '';

    try {
        if (empty($error) && $action === 'delete' && isset($_POST['id'])) {
            AuthController::requirePermission('manage_contact');
            
            $contactModel->delete($_POST['id']);
            AuthController::log('delete', 'contacts', 'Contact ID: ' . $_POST['id']);
            $success = 'Contact supprimé avec succès.';
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
} 

// Get all contacts with pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 10;
$search = $_GET['search'] ?? '';

try {
    if ($search) {
        $contacts = $contactModel->searchContacts($search);
        $pagination = [
            'data' => $contacts,
            'current_page' => 1,
            'total' => count($contacts),
            'last_page' => 1
        ];
    } else {
        $pagination = $contactModel->paginate($page, $perPage, 'date DESC');
        $contacts = $pagination['data'];
    }
} catch (Exception $e) {
    $error = $e->getMessage();
    $contacts = [];
    $pagination = ['current_page' => 1, 'last_page' => 1];
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Contacts - BetheLabs Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        :root {
            --sidebar-width: 260px;
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        body { background-color: #f8f9fa; }
        .sidebar {
            position: fixed; top: 0; left: 0; height: 100vh;
            width: var(--sidebar-width); background: var(--primary-gradient);
            color: white; overflow-y: auto; z-index: 1000;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
        }
        .sidebar-header { padding: 25px 20px; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .sidebar-header h4 { margin: 0; font-weight: 700; }
        .sidebar-menu { padding: 20px 0; }
        .sidebar-menu .nav-link {
            color: rgba(255,255,255,0.8); padding: 12px 20px;
            margin: 4px 10px; border-radius: 8px; transition: all 0.3s;
            display: flex; align-items: center;
        }
        .sidebar-menu .nav-link:hover, .sidebar-menu .nav-link.active {
            background: rgba(255,255,255,0.2); color: white;
        }
        .sidebar-menu .nav-link i { margin-right: 10px; font-size: 1.1rem; }
        .main-content { margin-left: var(--sidebar-width); padding: 0; }
        .top-navbar {
            background: white; box-shadow: 0 2px 4px rgba(0,0,0,0.08);
            padding: 15px 30px; position: sticky; top: 0; z-index: 999;
        }
        .content-wrapper { padding: 30px; }
        .action-btn { padding: 5px 10px; margin: 0 2px; }
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
            <a href="dashboard.php" class="nav-link"><i class="bi bi-speedometer2"></i> Dashboard</a>
            <a href="contacts.php" class="nav-link active"><i class="bi bi-envelope"></i> Contacts</a>
            <a href="news.php" class="nav-link"><i class="bi bi-newspaper"></i> Actualités</a>
            <a href="formations.php" class="nav-link"><i class="bi bi-book"></i> Formations</a>
            <a href="concours.php" class="nav-link"><i class="bi bi-trophy"></i> Concours</a>
            <a href="testimonials.php" class="nav-link"><i class="bi bi-chat-quote"></i> Témoignages</a>
            <a href="team.php" class="nav-link"><i class="bi bi-people"></i> Équipe</a>
            <a href="admins.php" class="nav-link"><i class="bi bi-shield-lock"></i> Administrateurs</a>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <nav class="top-navbar d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-0">Gestion des Contacts</h5>
                <small class="text-muted">Gérez les messages de contact</small>
            </div>
            <a href="logout.php" class="btn btn-outline-danger btn-sm">
                <i class="bi bi-box-arrow-right"></i> Déconnexion
            </a>
        </nav>

        <div class="content-wrapper">
            <?php if ($success): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="bi bi-check-circle-fill me-2"></i><?php echo htmlspecialchars($success); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i><?php echo htmlspecialchars($error); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3">
                    <div class="row align-items-center">
                        <div class="col">
                            <h5 class="mb-0"><i class="bi bi-envelope text-primary me-2"></i>Liste des Contacts</h5>
                        </div>
                        <div class="col-auto">
                            <form method="GET" class="d-flex">
                                <input type="text" name="search" class="form-control form-control-sm me-2" 
                                       placeholder="Rechercher..." value="<?php echo htmlspecialchars($search); ?>">
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <i class="bi bi-search"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Nom</th>
                                    <th>Email</th>
                                    <th>Message</th>
                                    <th>Date</th>
                                    <th width="100">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($contacts)): ?>
                                    <?php foreach ($contacts as $contact): ?>
                                        <tr>
                                            <td><?php echo $contact['id']; ?></td>
                                            <td><?php echo htmlspecialchars($contact['name']); ?></td>
                                            <td><small><?php echo htmlspecialchars($contact['email']); ?></small></td>
                                            <td>
                                                <button class="btn btn-sm btn-link p-0" data-bs-toggle="modal" 
                                                        data-bs-target="#viewModal<?php echo $contact['id']; ?>">
                                                    Voir le message
                                                </button>
                                            </td>
                                            <td><small><?php echo date('d/m/Y H:i', strtotime($contact['date'])); ?></small></td>
                                            <td>
                                                <form method="POST" style="display: inline;" 
                                                      onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce contact ?');">
                                                    <?php echo \Csrf::inputField(); ?>
                                                    <input type="hidden" name="action" value="delete">
                                                    <input type="hidden" name="id" value="<?php echo $contact['id']; ?>">
                                                    <button type="submit" class="btn btn-danger action-btn btn-sm" title="Supprimer">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>

                                        <!-- View Modal -->
                                        <div class="modal fade" id="viewModal<?php echo $contact['id']; ?>" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Message de <?php echo htmlspecialchars($contact['name']); ?></h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p><strong>Email:</strong> <?php echo htmlspecialchars($contact['email']); ?></p>
                                                        <p><strong>Date:</strong> <?php echo date('d/m/Y H:i', strtotime($contact['date'])); ?></p>
                                                        <hr>
                                                        <p><strong>Message:</strong></p>
                                                        <p><?php echo nl2br(htmlspecialchars($contact['message'])); ?></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">Aucun contact trouvé</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <?php if ($pagination['last_page'] > 1): ?>
                    <div class="card-footer bg-white">
                        <nav>
                            <ul class="pagination pagination-sm mb-0 justify-content-center">
                                <?php for ($i = 1; $i <= $pagination['last_page']; $i++): ?>
                                    <li class="page-item <?php echo $i === $pagination['current_page'] ? 'active' : ''; ?>">
                                        <a class="page-link" href="?page=<?php echo $i; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>">
                                            <?php echo $i; ?>
                                        </a>
                                    </li>
                                <?php endfor; ?>
                            </ul>
                        </nav>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
