<?php
require_once __DIR__ . '/config/Database.php';
require_once __DIR__ . '/config/ErrorHandler.php';
require_once __DIR__ . '/models/EmailChangeRequest.php';
require_once __DIR__ . '/models/Inscription.php';
require_once __DIR__ . '/models/UserLog.php';

ErrorHandler::init();

try {
    $token = $_GET['token'] ?? '';
    if (empty($token)) {
        throw new Exception('Token manquant.');
    }

    $model = new EmailChangeRequest();
    $request = $model->getByToken($token);

    if (empty($request)) {
        throw new Exception('Demande introuvable ou invalide.');
    }

    // Check expiry
    $now = new DateTime();
    $expires = new DateTime($request['expires_at']);
    if ($now > $expires) {
        // remove expired request
        $model->deleteById($request['id']);
        throw new Exception('Le lien a expiré. Veuillez demander une nouvelle modification depuis votre profil.');
    }

    // Update inscription record
    $inscription = new Inscription();
    $updated = $inscription->updateProfile($request['inscription_id'], ['name' => $inscription->getById($request['inscription_id'])['name'], 'email' => $request['new_email']]);

    if (!$updated) {
        $errors = $inscription->getErrors();
        throw new Exception(implode(' | ', $errors));
    }

    // Remove request after successful update
    $model->deleteById($request['id']);

    // Log the email confirmation
    $userLog = new UserLog();
    $userLog->log($request['inscription_id'], 'email_confirmed', 'Email confirmé avec succès', $inscription->getById($request['inscription_id'])['email'], $request['new_email']);

    // If the user is logged in and it's their session, refresh session email
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (!empty($_SESSION['logged_in_user']) && !empty($_SESSION['user_email']) && $_SESSION['user_email'] === $inscription->getById($request['inscription_id'])['email']) {
        // This case unlikely because we just updated email; update session to new email
        $_SESSION['user_email'] = $request['new_email'];
    }

    header('Location: profile.php?success=1');
    exit();
} catch (Exception $e) {
    header('Location: profile.php?error=' . urlencode($e->getMessage()));
    exit();
}
