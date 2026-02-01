<?php
require_once __DIR__ . '/config/Database.php';
require_once __DIR__ . '/config/ErrorHandler.php';
require_once __DIR__ . '/models/Contact.php';

ErrorHandler::init();
require_once __DIR__ . '/config/Csrf.php';
Csrf::init();

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: contact.php");
    exit();
}

// Validate CSRF
$token = $_POST['csrf_token'] ?? '';
if (!Csrf::validateToken($token)) {
    header('Location: contact.php?error=' . urlencode('Jeton CSRF invalide. Veuillez réessayer.'));
    exit();
}

try {
    $data = ErrorHandler::sanitize($_POST);
    
    // Create contact using model
    $contactModel = new Contact();
    
    if (!$contactModel->validate($data)) {
        throw new Exception(implode(', ', $contactModel->getErrors()));
    }
    
    $id = $contactModel->create($data);
    
    if ($id) {
        header('Location: contact.php?success=1');
        exit();
    } else {
        throw new Exception('Erreur lors de l\'envoi du message.');
    }
} catch (Exception $e) {
    header('Location: contact.php?error=' . urlencode($e->getMessage()));
    exit();
}
?>
