<?php
session_start();
require_once __DIR__ . '/config/Database.php';
require_once __DIR__ . '/config/ErrorHandler.php';
ErrorHandler::init();

if (empty($_SESSION['logged_in_user'])) {
    header('Location: login.php?error=' . urlencode('Veuillez vous connecter pour accéder à votre profil.'));
    exit();
}

// Feedback messages
$success = isset($_GET['success']);
$error = isset($_GET['error']) ? $_GET['error'] : null;
$pending = isset($_GET['pending']);

?>
<?php include 'includes/header.php'; ?>

<main class="container py-5">
    <h1>Mon profil</h1>

    <?php if ($success): ?>
        <div class="alert alert-success">Profil mis à jour avec succès.</div>
    <?php endif; ?>
    <?php if ($pending): ?>
        <div class="alert alert-info">Un email de confirmation a été envoyé à votre nouvelle adresse. Veuillez confirmer pour terminer la mise à jour.</div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form action="process_profile.php" method="post" id="profileForm">
        <div class="mb-3">
            <label for="name" class="form-label">Nom complet</label>
            <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($_SESSION['user_name'] ?? ''); ?>" required>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($_SESSION['user_email'] ?? ''); ?>" required>
        </div>
        <?php echo Csrf::inputField(); ?>
        <button type="submit" class="btn btn-primary">Enregistrer</button>
    </form>
</main>

<?php include 'includes/footer.php'; ?>
