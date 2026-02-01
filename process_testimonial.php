<?php
require_once __DIR__ . '/config/Database.php';
require_once __DIR__ . '/config/ErrorHandler.php';
require_once __DIR__ . '/config/MediaManager.php';
require_once __DIR__ . '/models/Testimonial.php';
require_once __DIR__ . '/models/Inscription.php';

ErrorHandler::init();

// Vérifier si la requête est POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: testimonials.php');
    exit();
}

try {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    require_once __DIR__ . '/config/Csrf.php';
    Csrf::init();

    // Validate CSRF
    if (!Csrf::validateToken($_POST['csrf_token'] ?? '')) {
        header('Location: index.php?testimonial_error=' . urlencode('Jeton CSRF invalide.'));
        exit();
    }

    // Require user to be logged in; if not, return to home with message
    if (empty($_SESSION['logged_in_user']) || empty($_SESSION['user_email'])) {
        header('Location: index.php?testimonial_error=' . urlencode('Vous devez vous connecter pour envoyer un témoignage.'));
        exit();
    }

    // Récupérer et nettoyer les données — utiliser les informations de session pour l'utilisateur connecté
    $name = ErrorHandler::sanitize($_SESSION['user_name'] ?? ($_POST['name'] ?? ''));
    $email = filter_var(trim($_SESSION['user_email'] ?? ($_POST['email'] ?? '')), FILTER_SANITIZE_EMAIL);
    $rating = intval($_POST['rating'] ?? 0);
    $comment = ErrorHandler::sanitize($_POST['comment'] ?? '');
    $image = null;

    // Validation
    $errors = [];

    if (empty($name)) {
        $errors[] = 'Le nom est requis.';
    }

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Un email valide est requis.';
    }

    if ($rating < 1 || $rating > 5) {
        $errors[] = 'Une note entre 1 et 5 est requise.';
    }

    if (empty($comment)) {
        $errors[] = 'Le commentaire est requis.';
    }

    if (strlen($comment) > 1000) {
        $errors[] = 'Le commentaire ne peut pas dépasser 1000 caractères.';
    }

    // Gérer l'upload d'image si fourni (champ `photo` envoyé depuis index.php)
    if (!empty($_FILES['photo']['tmp_name'])) {
        $mediaManager = new MediaManager();
        $imagePath = $mediaManager->uploadImage($_FILES['photo']);
        if ($imagePath) {
            $image = $imagePath;
        } else {
            $errors[] = 'Erreur lors du téléchargement de l\'image: ' . implode(', ', $mediaManager->getErrors());
        }
    }


    // Si pas d'erreurs, insérer le témoignage
    if (empty($errors)) {
        $testimonialModel = new Testimonial();
        $data = [
            'name' => $name,
            'email' => $email,
            'rating' => $rating,
            'comment' => $comment,
            'image' => $image,
            'status' => 'pending'
        ];

        $id = $testimonialModel->create($data);

        if ($id) {
            // Succès - redirection vers page d'accueil
            header('Location: index.php?testimonial_success=1');
            exit();
        } else {
            $errors[] = 'Erreur lors de l\'enregistrement du témoignage.';
        }
    }

    // Si erreurs, redirection avec messages
    if (!empty($errors)) {
        $errorMsg = implode(' | ', $errors);
        header('Location: index.php?testimonial_error=' . urlencode($errorMsg));
        exit();
    }

} catch (Exception $e) {
    ErrorHandler::logError($e);
    header('Location: testimonials.php?error=' . urlencode('Une erreur est survenue'));
    exit();
}

