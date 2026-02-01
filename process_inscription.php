<?php
require_once __DIR__ . '/config/Database.php';
require_once __DIR__ . '/config/ErrorHandler.php';
require_once __DIR__ . '/models/Inscription.php';

ErrorHandler::init();
require_once __DIR__ . '/config/Csrf.php';
Csrf::init();

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: inscription.php");
    exit();
}

// Validate CSRF
if (!Csrf::validateToken($_POST['csrf_token'] ?? '')) {
    header('Location: inscription.php?error=' . urlencode('Jeton CSRF invalide.'));
    exit();
}

try {
    $data = ErrorHandler::sanitize($_POST);
    
    // Create inscription using model (create() runs validation internally)
    $inscriptionModel = new Inscription();
    
    $id = $inscriptionModel->create($data);
    
    if (!$id) {
        throw new Exception(implode(', ', $inscriptionModel->getErrors()));
    }
    
    if ($id) {
        // Auto-login the user after successful inscription
        session_start();
        $_SESSION['user_email'] = $data['email'];
        $_SESSION['user_name'] = $data['name'];
        $_SESSION['logged_in_user'] = true;

        header('Location: index.php?registered=1');
        exit();
    } else {
        throw new Exception('Erreur lors de l\'inscription.');
    }
} catch (Exception $e) {
    header('Location: inscription.php?error=' . urlencode($e->getMessage()));
    exit();
}
?>
