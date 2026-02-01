<?php
// Démarrer la session si elle n'est pas démarrée
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../controllers/AuthController.php';

// Log the logout action before destroying session
if (isset($_SESSION['admin_id'])) {
    try {
        // Use AuthController static logger so we don't rely on Admin having a log method
        AuthController::log('logout', 'auth', 'Déconnexion de ' . ($_SESSION['admin_username'] ?? $_SESSION['username'] ?? ''));
    } catch (Exception $e) {
        // Silently fail logging
    }
}

// Détruire la session
session_unset();
if (session_status() === PHP_SESSION_ACTIVE) {
    session_destroy();
}

// Rediriger vers la page de connexion
header('Location: login.php');
exit();
