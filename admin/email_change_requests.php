<?php
session_start();
require_once __DIR__ . '/config/Database.php';
require_once __DIR__ . '/config/ErrorHandler.php';
require_once __DIR__ . '/models/EmailChangeRequest.php';
require_once __DIR__ . '/models/Inscription.php';

ErrorHandler::init();

$error = '';

// Initialize CSRF
require_once __DIR__ . '/config/Csrf.php';
Csrf::init();

// Validate CSRF for POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!Csrf::validateToken($_POST['csrf_token'] ?? '')) {
        $error = 'Jeton CSRF invalide. Opération annulée.';
    }
}

// Check if user is admin (simplified check - in production, use proper role system)
// For now, redirect non-admins to homepage
if (empty($_SESSION['admin_id']) && empty($_SESSION['is_admin'])) {
    header('Location: index.php');
    exit();
} 

try {
    $db = Database::getInstance()->getConnection();
    
    // Get all pending email change requests
    $sql = "SELECT ecr.*, i.name, i.email as old_email FROM email_change_requests ecr
            JOIN inscriptions i ON ecr.inscription_id = i.id
            WHERE ecr.expires_at > NOW()
            ORDER BY ecr.created_at DESC";
    
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $requests = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (Exception $e) {
    ErrorHandler::logError($e);
    $requests = [];
    $error = $e->getMessage();
}

// Handle revocation
if (empty($error) && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['revoke_id'])) {
    try {
        $revokeId = intval($_POST['revoke_id']);
        $model = new EmailChangeRequest();
        $model->deleteById($revokeId);
        $success = 'Demande révoquée avec succès.';
    } catch (Exception $e) {
        $error = 'Erreur lors de la révocation: ' . $e->getMessage();
    }
} 
?>
<?php include 'includes/header.php'; ?>

<main class="container py-5">
    <h1>Gestion des demandes de changement d'email</h1>
    <p class="text-muted">Affiche les demandes de changement d'email en attente de confirmation.</p>

    <?php if (!empty($success)): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>
    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <?php if (!empty($requests)): ?>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Utilisateur</th>
                    <th>Email actuel</th>
                    <th>Nouvel email</th>
                    <th>Date de demande</th>
                    <th>Expire le</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($requests as $request): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($request['name']); ?></td>
                        <td><?php echo htmlspecialchars($request['old_email']); ?></td>
                        <td><?php echo htmlspecialchars($request['new_email']); ?></td>
                        <td><?php echo date('d/m/Y H:i', strtotime($request['created_at'])); ?></td>
                        <td><?php echo date('d/m/Y H:i', strtotime($request['expires_at'])); ?></td>
                        <td>
                            <form method="post" style="display: inline;">
                                <?php echo \Csrf::inputField(); ?>
                                <input type="hidden" name="revoke_id" value="<?php echo $request['id']; ?>">
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir révoquer cette demande ?');">Révoquer</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="alert alert-info">Aucune demande de changement d'email en attente.</div>
    <?php endif; ?>

    <a href="admin/dashboard.php" class="btn btn-secondary mt-3">Retour à l'admin</a>
</main>

<?php include 'includes/footer.php'; ?>
