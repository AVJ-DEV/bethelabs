<?php
require_once __DIR__ . '/../config/ErrorHandler.php';
require_once __DIR__ . '/../controllers/AuthController.php';
require_once __DIR__ . '/../models/Contact.php';
require_once __DIR__ . '/../models/News.php';
require_once __DIR__ . '/../models/Formation.php';

ErrorHandler::init();
AuthController::requireAuth();

$currentAdmin = AuthController::getCurrentAdmin();

// Get statistics
try {
    $contactModel = new Contact();
    $newsModel = new News();
    $formationModel = new Formation();

    $totalContacts = $contactModel->count();
    $totalNews = $newsModel->count();
    $totalFormations = $formationModel->count();
    $publishedNews = $newsModel->count(['status' => 'published']);
    
    $recentContacts = $contactModel->getRecent(5);
    $popularNews = $newsModel->getPopular(5);
} catch (Exception $e) {
    $error = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - BetheLabs Admin</title>
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

        .sidebar-header h4 {
            margin: 0;
            font-weight: 700;
        }

        .sidebar-menu {
            padding: 20px 0;
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

        .sidebar-menu .nav-link i {
            margin-right: 10px;
            font-size: 1.1rem;
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

        .stats-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.07);
            transition: transform 0.3s;
            overflow: hidden;
        }

        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0,0,0,0.15);
        }

        .stats-card .card-body {
            padding: 25px;
        }

        .stats-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
        }

        .gradient-blue {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .gradient-green {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        }

        .gradient-orange {
            background: linear-gradient(135deg, #f2994a 0%, #f2c94c 100%);
        }

        .gradient-red {
            background: linear-gradient(135deg, #eb3349 0%, #f45c43 100%);
        }

        .table-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.07);
            overflow: hidden;
        }

        .table-card .card-header {
            background: white;
            border-bottom: 2px solid #f0f0f0;
            padding: 20px 25px;
        }

        .badge-status {
            padding: 6px 12px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 0.75rem;
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
            <a href="dashboard.php" class="nav-link active">
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
            <a href="admins.php" class="nav-link">
                <i class="bi bi-shield-lock"></i> Administrateurs
            </a>
        </nav>

        <div class="px-3 mt-auto pb-4">
            <div class="card bg-white bg-opacity-10 border-0 text-white">
                <div class="card-body p-3">
                    <h6 class="mb-1"><?php echo htmlspecialchars($currentAdmin['username']); ?></h6>
                    <small class="opacity-75"><?php echo htmlspecialchars($currentAdmin['role']); ?></small>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Navbar -->
        <nav class="top-navbar d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-0">Dashboard</h5>
                <small class="text-muted">Bienvenue, <?php echo htmlspecialchars($currentAdmin['username']); ?></small>
            </div>
            <div>
                <a href="logout.php" class="btn btn-outline-danger btn-sm">
                    <i class="bi bi-box-arrow-right"></i> Déconnexion
                </a>
            </div>
        </nav>

        <!-- Content -->
        <div class="content-wrapper">
            <?php if (isset($error)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <?php echo htmlspecialchars($error); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Statistics Cards -->
            <div class="row g-4 mb-4">
                <div class="col-md-3">
                    <div class="stats-card card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <p class="text-muted mb-1">Total Contacts</p>
                                    <h3 class="mb-0 fw-bold"><?php echo $totalContacts ?? 0; ?></h3>
                                </div>
                                <div class="stats-icon gradient-blue text-white">
                                    <i class="bi bi-envelope-fill"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="stats-card card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <p class="text-muted mb-1">Actualités</p>
                                    <h3 class="mb-0 fw-bold"><?php echo $totalNews ?? 0; ?></h3>
                                    <small class="text-success">
                                        <i class="bi bi-check-circle-fill"></i> 
                                        <?php echo $publishedNews ?? 0; ?> publiées
                                    </small>
                                </div>
                                <div class="stats-icon gradient-green text-white">
                                    <i class="bi bi-newspaper"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="stats-card card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <p class="text-muted mb-1">Formations</p>
                                    <h3 class="mb-0 fw-bold"><?php echo $totalFormations ?? 0; ?></h3>
                                </div>
                                <div class="stats-icon gradient-orange text-white">
                                    <i class="bi bi-book-fill"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="stats-card card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <p class="text-muted mb-1">Vues totales</p>
                                    <h3 class="mb-0 fw-bold">
                                        <?php 
                                        $totalViews = array_sum(array_column($popularNews ?? [], 'views'));
                                        echo number_format($totalViews); 
                                        ?>
                                    </h3>
                                </div>
                                <div class="stats-icon gradient-red text-white">
                                    <i class="bi bi-eye-fill"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="table-card card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="bi bi-clock-history text-primary me-2"></i>
                                Contacts récents
                            </h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Nom</th>
                                            <th>Email</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($recentContacts)): ?>
                                            <?php foreach ($recentContacts as $contact): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($contact['name']); ?></td>
                                                    <td><small><?php echo htmlspecialchars($contact['email']); ?></small></td>
                                                    <td>
                                                        <small class="text-muted">
                                                            <?php echo date('d/m/Y', strtotime($contact['date'])); ?>
                                                        </small>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="3" class="text-center text-muted py-4">
                                                    Aucun contact récent
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="table-card card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="bi bi-fire text-danger me-2"></i>
                                Actualités populaires
                            </h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Titre</th>
                                            <th>Vues</th>
                                            <th>Statut</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($popularNews)): ?>
                                            <?php foreach ($popularNews as $news): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars(substr($news['title'], 0, 30)) . '...'; ?></td>
                                                    <td>
                                                        <span class="badge bg-primary">
                                                            <?php echo $news['views']; ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <?php
                                                        $statusClass = [
                                                            'published' => 'success',
                                                            'draft' => 'warning',
                                                            'archived' => 'secondary'
                                                        ];
                                                        $statusText = [
                                                            'published' => 'Publié',
                                                            'draft' => 'Brouillon',
                                                            'archived' => 'Archivé'
                                                        ];
                                                        ?>
                                                        <span class="badge-status bg-<?php echo $statusClass[$news['status']]; ?> text-white">
                                                            <?php echo $statusText[$news['status']]; ?>
                                                        </span>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="3" class="text-center text-muted py-4">
                                                    Aucune actualité disponible
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
