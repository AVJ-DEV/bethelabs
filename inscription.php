<?php session_start(); include 'includes/header.php'; ?>

<?php
$isClient = isset($_GET['type']) && $_GET['type'] === 'client';
?>

<main class="container py-5">
    <h1><?php echo $isClient ? 'Inscription Client' : 'Inscription aux Formations et Concours'; ?></h1>
    <form action="process_inscription.php" method="post" id="inscriptionForm">
        <?php if ($isClient): ?>
            <input type="hidden" name="formation" value="client">
        <?php endif; ?>
        <div class="mb-3">
            <label for="name" class="form-label">Nom complet</label>
            <input type="text" class="form-control" id="name" name="name" required>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>
        <div class="mb-3">
            <label for="phone" class="form-label">Téléphone</label>
            <input type="tel" class="form-control" id="phone" name="phone" required>
        </div>
        <?php if (!$isClient): ?>
        <div class="mb-3">
            <label for="formation" class="form-label">Formation/Concours</label>
            <select class="form-control" id="formation" name="formation" required>
                <option value="">Sélectionnez</option>
                <option value="primaire">Primaire & Collège</option>
                <option value="universitaire">Universitaire</option>
                <option value="concours">Concours</option>
            </select>
        </div>
        <?php endif; ?>
        <?php echo Csrf::inputField(); ?>
        <button type="submit" class="btn btn-success"><?php echo $isClient ? "S'inscrire en tant que client" : "S'inscrire"; ?></button>
    </form>
</main>

<?php include 'includes/footer.php'; ?>