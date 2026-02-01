<?php
header('Content-Type: application/json');

require_once __DIR__ . '/config/Database.php';
require_once __DIR__ . '/config/ErrorHandler.php';
require_once __DIR__ . '/config/MediaManager.php';
require_once __DIR__ . '/models/Testimonial.php';

ErrorHandler::init();
require_once __DIR__ . '/config/Csrf.php';
Csrf::init();

$response = [
    'success' => false,
    'message' => 'Une erreur est survenue.'
];

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Méthode non autorisée.');
    }

    // Validate CSRF token
    if (!Csrf::validateToken($_POST['csrf_token'] ?? '')) {
        throw new Exception('Jeton CSRF invalide.');
    }

    // Récupérer et nettoyer les données
    $name = ErrorHandler::sanitize($_POST['name'] ?? '');
    $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
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

    if (!empty($errors)) {
        throw new Exception(implode(' ', $errors));
    }

    // Gérer l'upload d'image si fourni
    $mediaManager = new MediaManager();
    if (!empty($_FILES['image']['tmp_name'])) {
        $imagePath = $mediaManager->uploadImage($_FILES['image']);
        if ($imagePath) {
            $image = $imagePath;
        } else {
            throw new Exception('Erreur upload image: ' . implode(', ', $mediaManager->getErrors()));
        }
    }

    // Créer le témoignage avec le modèle
    $testimonialModel = new Testimonial();
    $data = [
        'name' => $name,
        'email' => $email,
        'rating' => $rating,
        'comment' => $comment,
        'status' => 'pending'
    ];
    
    if ($image) {
        $data['image'] = $image;
    }

    $id = $testimonialModel->create($data);

    if ($id) {
        $response['success'] = true;
        $response['message'] = 'Merci pour votre témoignage! Il sera affiché après approbation de notre équipe.';
    } else {
        throw new Exception('Erreur lors de la création du témoignage.');
    }

} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
exit();
?>
