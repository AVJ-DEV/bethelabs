<?php
session_start();
require_once __DIR__ . '/config/Database.php';
require_once __DIR__ . '/config/ErrorHandler.php';
require_once __DIR__ . '/models/UserLog.php';
require_once __DIR__ . '/models/Inscription.php';

ErrorHandler::init();

// Check if user is admin (simplified check)
if (empty($_SESSION['admin_id']) && empty($_SESSION['is_admin'])) {
    header('Location: index.php');
    exit();
}

try {
    $userLog = new UserLog();
    
    // Get filter parameters
    $action = isset($_GET['action']) ? $_GET['action'] : null;
    $user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : null;
    
    // Get logs
    if ($user_id) {
        $logs = $userLog->getUserLogs($user_id, 100);
    } else {
        $logs = $userLog->getAllLogs(100, $action);
    }
    
} catch (Exception $e) {
    ErrorHandler::logError($e);
    $logs = [];
    $error = $e->getMessage();
}
?>
<?php include 'includes/header.php'; ?>

<main class="container py-5">
    <h1>Logs des modifications de profil utilisateur</h1>
    
    <div class="row mb-4">
        <div class="col-md-8">
            <form method="get" class="form-inline">
                <div class="me-3">
                    <label for="action" class="form-label me-2">Action :</label>
                    <select name="action" id="action" class="form-select" onchange="this.form.submit()">
                        <option value="">Toutes les actions</option>
                        <option value="profile_update" <?php echo $action === 'profile_update' ? 'selected' : ''; ?>>Mise à jour du profil</option>
                        <option value="email_change_requested" <?php echo $action === 'email_change_requested' ? 'selected' : ''; ?>>Demande de changement d'email</option>
                        <option value="email_confirmed" <?php echo $action === 'email_confirmed' ? 'selected' : ''; ?>>Email confirmé</option>
                    </select>
                </div>
            </form>
        </div>
    </div>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <?php if (!empty($logs)): ?>
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Date</th>
                        <th>Utilisateur</th>
                        <th>Email</th>
                        <th>Action</th>
                        <th>Description</th>
                        <th>Ancien</th>
                        <th>Nouveau</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($logs as $log): ?>
                        <tr>
                            <td>
                                <small><?php echo date('d/m/Y H:i:s', strtotime($log['created_at'])); ?></small>
                            </td>
                            <td>
                                <?php if (isset($log['name'])): ?>
                                    <a href="?user_id=<?php echo $log['inscription_id']; ?>">
                                        <?php echo htmlspecialchars($log['name']); ?>
                                    </a>
                                <?php else: ?>
                                    <em>Utilisateur supprimé</em>
                                <?php endif; ?>
                            </td>
                            <td>
                                <small><?php echo isset($log['email']) ? htmlspecialchars($log['email']) : '<em>N/A</em>'; ?></small>
                            </td>
                            <td>
                                <span class="badge bg-info">
                                    <?php 
                                    $action_labels = [
                                        'profile_update' => 'Profil',
                                        'email_change_requested' => 'Demande changement',
                                        'email_confirmed' => 'Email confirmé'
                                    ];
                                    echo $action_labels[$log['action']] ?? htmlspecialchars($log['action']);
                                    ?>
                                </span>
                            </td>
                            <td>
                                <small><?php echo htmlspecialchars($log['description'] ?? ''); ?></small>
                            </td>
                            <td>
                                <?php if ($log['old_value']): ?>
                                    <code class="bg-light p-1"><?php echo htmlspecialchars(substr($log['old_value'], 0, 50)); ?></code>
                                <?php else: ?>
                                    <em class="text-muted">-</em>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($log['new_value']): ?>
                                    <code class="bg-light p-1"><?php echo htmlspecialchars(substr($log['new_value'], 0, 50)); ?></code>
                                <?php else: ?>
                                    <em class="text-muted">-</em>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="alert alert-info">Aucun log disponible.</div>
    <?php endif; ?>

    <div class="mt-4">
        <a href="admin/dashboard.php" class="btn btn-secondary">Retour à l'admin</a>
    </div>
</main>

<?php include 'includes/footer.php'; ?>
