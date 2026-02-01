<?php
session_start();
require_once __DIR__ . '/config/Database.php';
require_once __DIR__ . '/config/ErrorHandler.php';
require_once __DIR__ . '/models/Inscription.php';

ErrorHandler::init();
require_once __DIR__ . '/config/Csrf.php';
Csrf::init();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit();
}

// Validate CSRF
if (!Csrf::validateToken($_POST['csrf_token'] ?? '')) {
    header('Location: login.php?error=' . urlencode('Jeton CSRF invalide.'));
    exit();
}

try {
    $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Veuillez fournir un email valide.');
    }

    $inscription = new Inscription();
    $users = $inscription->getByEmail($email);

    if (empty($users)) {
        // No user found
        header('Location: login.php?error=' . urlencode('Aucun compte trouvé avec cet email.'));
        exit();
    }

    // Use the most recent inscription entry
    $user = $users[0];

    // Set session
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['user_name'] = $user['name'];
    $_SESSION['logged_in_user'] = true;

    header('Location: index.php?login=1');
    exit();

} catch (Exception $e) {
    header('Location: login.php?error=' . urlencode($e->getMessage()));
    exit();
}
