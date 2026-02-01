<?php
session_start();
require_once __DIR__ . '/config/Database.php';
require_once __DIR__ . '/config/ErrorHandler.php';
require_once __DIR__ . '/config/MailerConfig.php';
require_once __DIR__ . '/models/Inscription.php';
require_once __DIR__ . '/models/UserLog.php';

ErrorHandler::init();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: profile.php');
    exit();
}

require_once __DIR__ . '/config/Csrf.php';
Csrf::init();

// Validate CSRF
if (!Csrf::validateToken($_POST['csrf_token'] ?? '')) {
    header('Location: profile.php?error=' . urlencode('Jeton CSRF invalide.'));
    exit();
}

if (empty($_SESSION['logged_in_user']) || empty($_SESSION['user_email'])) {
    header('Location: login.php?error=' . urlencode('Veuillez vous connecter pour modifier votre profil.'));
    exit();
}

try {
    $name = ErrorHandler::sanitize($_POST['name'] ?? '');
    $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);

    if (empty($name) || empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Nom et email valides requis.');
    }

    $inscriptionModel = new Inscription();
    $existing = $inscriptionModel->getByEmail($_SESSION['user_email']);

    if (empty($existing)) {
        throw new Exception('Enregistrement introuvable pour l\'email de session.');
    }

    // Use most recent inscription record for this user
    $record = $existing[0];
    $id = $record['id'];

    // If email unchanged, update immediately
    if ($email === $_SESSION['user_email']) {
        $updated = $inscriptionModel->updateProfile($id, ['name' => $name, 'email' => $email]);
        if ($updated) {
            $_SESSION['user_name'] = $name;
            // Log the name change
            $userLog = new UserLog();
            $userLog->log($id, 'profile_update', 'Nom mis à jour', $record['name'], $name);
            header('Location: profile.php?success=1');
            exit();
        } else {
            $errors = $inscriptionModel->getErrors();
            throw new Exception(implode(' | ', $errors));
        }
    }

    // Email changed => create confirmation request and send email
    require_once __DIR__ . '/models/EmailChangeRequest.php';

    $token = bin2hex(random_bytes(32));
    $expiresAt = (new DateTime('+24 hours'))->format('Y-m-d H:i:s');

    $emailChangeModel = new EmailChangeRequest();
    $created = $emailChangeModel->createRequest($id, $email, $token, $expiresAt);

    if (!$created) {
        throw new Exception('Impossible de créer la demande de confirmation d\'email.');
    }

    // Send confirmation email to the NEW email address
    $confirmUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/confirm_email.php?token=' . urlencode($token);

    $subject = 'Confirmation de votre nouvel email - BETHEL LABS';
    $message = "Bonjour " . htmlspecialchars($name) . ",\n\n";
    $message .= "Vous avez demandé à mettre à jour votre adresse email pour BETHEL LABS.\n";
    $message .= "Veuillez confirmer votre nouvelle adresse en cliquant sur le lien suivant :\n\n" . $confirmUrl . "\n\n";
    $message .= "Ce lien expirera dans 24 heures. Si vous n'êtes pas à l'origine de cette demande, ignorez cet email.\n\nMerci,\nBETHEL LABS";

    // Use MailerConfig for sending
    $mailer = MailerConfig::getInstance();
    $mailSent = $mailer->send($email, $subject, $message, false);

    if ($mailSent) {
        // Log the email change request
        $userLog = new UserLog();
        $userLog->log($id, 'email_change_requested', 'Demande de changement d\'email en attente de confirmation', $_SESSION['user_email'], $email);
        header('Location: profile.php?pending=1');
        exit();
    } else {
        throw new Exception('Impossible d\'envoyer l\'email de confirmation. Veuillez contacter l\'administrateur.');
    }

} catch (Exception $e) {
    header('Location: profile.php?error=' . urlencode($e->getMessage()));
    exit();
}
