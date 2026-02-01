<?php
session_start();
require_once __DIR__ . '/config/Database.php';
require_once __DIR__ . '/config/ErrorHandler.php';
ErrorHandler::init();
?>
<?php include 'includes/header.php'; ?>

<main class="container py-5">
    <h1>Connexion</h1>
    <p>Connectez-vous avec l'email utilisé lors de votre inscription.</p>
    <form action="process_login.php" method="post" id="loginForm">
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>
        <?php echo Csrf::inputField(); ?>
        <button type="submit" class="btn btn-primary">Se connecter</button>
    </form>
</main>

<?php include 'includes/footer.php'; ?>
