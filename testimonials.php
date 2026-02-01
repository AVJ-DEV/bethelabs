<?php
session_start();
require_once __DIR__ . '/config/Database.php';
require_once __DIR__ . '/config/ErrorHandler.php';
require_once __DIR__ . '/config/MediaManager.php';
require_once __DIR__ . '/models/Testimonial.php';

ErrorHandler::init();

$testimonialModel = new Testimonial();
$mediaManager = new MediaManager();
$error = '';
$success = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $data = ErrorHandler::sanitize($_POST);
        
        // Gérer l'upload d'image si fourni
        if (!empty($_FILES['image']['tmp_name'])) {
            $imagePath = $mediaManager->uploadImage($_FILES['image']);
            if ($imagePath) {
                $data['image'] = $imagePath;
            } else {
                throw new Exception('Erreur upload image: ' . implode(', ', $mediaManager->getErrors()));
            }
        }
        
        // Validation
        if (empty($data['name'])) {
            throw new Exception('Le nom est requis.');
        }
        if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Email valide requis.');
        }
        if (empty($data['comment'])) {
            throw new Exception('Le commentaire est requis.');
        }
        if (empty($data['rating']) || $data['rating'] < 1 || $data['rating'] > 5) {
            throw new Exception('Une note entre 1 et 5 est requise.');
        }

        // Ajouter le témoignage avec statut "en attente" par défaut
        $data['status'] = 'pending';
        $id = $testimonialModel->create($data);

        if ($id) {
            $success = 'Merci pour votre témoignage! Il sera affiché après approbation de notre équipe.';
            $_POST = [];
        } else {
            $error = implode('<br>', $testimonialModel->getErrors());
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// Get approved testimonials only for display
$approvedTestimonials = $testimonialModel->getAll('created_at DESC');
$approvedTestimonials = array_filter($approvedTestimonials, function($t) {
    return $t['status'] === 'approved';
});
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Témoignages - BetheLabs</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .testimonials-section {
            padding: 60px 0;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        }

        .testimonial-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
        }

        .testimonial-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.12);
        }

        .testimonial-header {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }

        .testimonial-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 1.2rem;
            margin-right: 15px;
            overflow: hidden;
            object-fit: cover;
        }

        .testimonial-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .testimonial-info h6 {
            margin: 0;
            font-weight: 600;
        }

        .testimonial-info small {
            color: #999;
        }

        .rating-stars {
            color: #ffc107;
            font-size: 1.1rem;
            margin: 10px 0;
        }

        .testimonial-text {
            color: #555;
            font-style: italic;
            margin: 15px 0;
        }

        .form-section {
            background: white;
            border-radius: 15px;
            padding: 40px;
            margin-bottom: 40px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        }

        .form-section h4 {
            margin-bottom: 30px;
            color: #333;
            font-weight: 600;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }

        .btn-submit {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 12px 30px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
            color: white;
        }

        .rating-input {
            display: flex;
            gap: 10px;
            margin: 15px 0;
        }

        .rating-selector {
            display: flex;
            gap: 8px;
            font-size: 2.5rem;
            cursor: pointer;
        }

        .rating-selector .star {
            opacity: 0.3;
            transition: all 0.2s ease;
            cursor: pointer;
            filter: grayscale(100%);
        }

        .rating-selector .star:hover,
        .rating-selector .star.hover {
            opacity: 1;
            transform: scale(1.1);
            filter: grayscale(0%);
        }

        .rating-selector .star.selected {
            opacity: 1;
            color: #ffc107;
            filter: grayscale(0%);
        }

        .rating-input input[type="radio"] {
            display: none;
        }

        .rating-input label {
            cursor: pointer;
            font-size: 2rem;
            color: #ddd;
            transition: all 0.2s ease;
            margin: 0;
        }

        .rating-input input[type="radio"]:checked ~ label,
        .rating-input label:hover,
        .rating-input label:hover ~ label {
            color: #ffc107;
        }

        .no-testimonials {
            text-align: center;
            padding: 40px;
            color: #999;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <?php include 'includes/header.php'; ?>

    <!-- Testimonials Section -->
    <div class="testimonials-section">
        <div class="container">
            <h2 class="text-center mb-5">Ce que nos clients disent</h2>

            <!-- Form Section -->
            <div class="form-section">
                <h4><i class="bi bi-pencil-square me-2"></i>Partager votre expérience</h4>

                <?php if ($error): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        <?php echo htmlspecialchars($error); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if ($success): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        <i class="bi bi-check-circle-fill me-2"></i>
                        <?php echo htmlspecialchars($success); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Alert messages for AJAX submission -->
                <div id="form-alert" style="display: none;"></div>

                <form id="testimonial-form" enctype="multipart/form-data" class="needs-validation" novalidate>
                    <?php echo Csrf::inputField(); ?>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Votre nom *</label>
                                <input type="text" name="name" class="form-control" required 
                                       placeholder="Votre nom complet">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Votre email *</label>
                                <input type="email" name="email" class="form-control" required 
                                       placeholder="votre.email@exemple.com">
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Votre avis *</label>
                        <textarea name="comment" class="form-control" rows="5" required 
                                  placeholder="Partagez votre expérience avec nos services..."></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Note (1 à 5 étoiles) *</label>
                                <input type="hidden" name="rating" id="rating-value" value="0" required>
                                <div class="rating-selector" id="rating-selector">
                                    <span class="star" data-rating="1" title="Très insatisfait">⭐</span>
                                    <span class="star" data-rating="2" title="Insatisfait">⭐</span>
                                    <span class="star" data-rating="3" title="Moyen">⭐</span>
                                    <span class="star" data-rating="4" title="Satisfait">⭐</span>
                                    <span class="star" data-rating="5" title="Très satisfait">⭐</span>
                                </div>
                                <small class="text-muted d-block mt-2">Cliquez sur une étoile pour évaluer</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Votre photo (optionnel)</label>
                                <input type="file" name="image" class="form-control" accept="image/*">
                                <small class="text-muted">JPG, PNG - max 5MB</small>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-submit btn-lg w-100" id="submit-btn">
                        <i class="bi bi-send me-2"></i>Envoyer mon témoignage
                    </button>
                </form>
            </div>

            <!-- Testimonials Display -->
            <div class="row">
                <div class="col-12">
                    <h4 class="mb-4">Témoignages publiés</h4>
                </div>
                <?php if (!empty($approvedTestimonials)): ?>
                    <?php foreach ($approvedTestimonials as $testimonial): ?>
                        <div class="col-md-6 col-lg-4">
                            <div class="testimonial-card">
                                <div class="testimonial-header">
                                    <div class="testimonial-avatar">
                                        <?php if (!empty($testimonial['image'])): ?>
                                            <img src="<?php echo htmlspecialchars($testimonial['image']); ?>" alt="<?php echo htmlspecialchars($testimonial['name']); ?>">
                                        <?php else: ?>
                                            <?php echo strtoupper(substr($testimonial['name'], 0, 1)); ?>
                                        <?php endif; ?>
                                    </div>
                                    <div class="testimonial-info">
                                        <h6><?php echo htmlspecialchars($testimonial['name']); ?></h6>
                                        <small><?php echo date('d/m/Y', strtotime($testimonial['created_at'])); ?></small>
                                    </div>
                                </div>
                                <div class="rating-stars">
                                    <?php echo str_repeat('⭐', $testimonial['rating']); ?>
                                </div>
                                <p class="testimonial-text">
                                    "<?php echo htmlspecialchars($testimonial['comment']); ?>"
                                </p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12">
                        <div class="no-testimonials">
                            <i class="bi bi-chat-quote" style="font-size: 3rem; opacity: 0.5;"></i>
                            <p>Aucun témoignage pour le moment. Soyez les premiers à partager votre expérience!</p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Interactive star rating selector
        document.addEventListener('DOMContentLoaded', function() {
            const ratingSelector = document.getElementById('rating-selector');
            const ratingValue = document.getElementById('rating-value');
            const stars = ratingSelector.querySelectorAll('.star');

            // Initialize selected stars based on current value
            const currentRating = parseInt(ratingValue.value) || 0;
            if (currentRating > 0) {
                stars.forEach((star, index) => {
                    if (index < currentRating) {
                        star.classList.add('selected');
                    }
                });
            }

            // Handle star hover effect
            stars.forEach((star) => {
                star.addEventListener('mouseenter', function() {
                    const rating = parseInt(this.dataset.rating);
                    stars.forEach((s, index) => {
                        if (index < rating) {
                            s.classList.add('hover');
                        } else {
                            s.classList.remove('hover');
                        }
                    });
                });
            });

            // Handle star click selection
            stars.forEach((star) => {
                star.addEventListener('click', function(e) {
                    e.preventDefault();
                    const rating = parseInt(this.dataset.rating);
                    ratingValue.value = rating;
                    
                    stars.forEach((s, index) => {
                        s.classList.remove('hover');
                        if (index < rating) {
                            s.classList.add('selected');
                        } else {
                            s.classList.remove('selected');
                        }
                    });
                });
            });

            // Remove hover effect when mouse leaves rating selector
            ratingSelector.addEventListener('mouseleave', function() {
                stars.forEach((star) => {
                    star.classList.remove('hover');
                });
            });

            // Handle testimonial form AJAX submission
            const form = document.getElementById('testimonial-form');
            const submitBtn = document.getElementById('submit-btn');
            const formAlert = document.getElementById('form-alert');

            form.addEventListener('submit', async function(e) {
                e.preventDefault();

                // Validate rating
                const rating = parseInt(ratingValue.value);
                if (rating < 1 || rating > 5) {
                    showAlert('error', 'Veuillez sélectionner une note entre 1 et 5 étoiles.');
                    return;
                }

                // Show loading state
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Envoi en cours...';

                try {
                    // Create FormData for file upload
                    const formData = new FormData(form);

                    // Send AJAX request
                    const response = await fetch('process_testimonial_ajax.php', {
                        method: 'POST',
                        body: formData
                    });

                    const data = await response.json();

                    if (data.success) {
                        showAlert('success', data.message);
                        form.reset();
                        ratingValue.value = '0';
                        stars.forEach(s => s.classList.remove('selected'));
                        
                        // Reload testimonials after 2 seconds
                        setTimeout(() => {
                            location.reload();
                        }, 2000);
                    } else {
                        showAlert('error', data.message || 'Une erreur est survenue.');
                    }
                } catch (error) {
                    showAlert('error', 'Erreur de connexion: ' + error.message);
                } finally {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="bi bi-send me-2"></i>Envoyer mon témoignage';
                }
            });

            // Helper function to display alerts
            function showAlert(type, message) {
                const alertDiv = document.getElementById('form-alert');
                const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
                
                alertDiv.innerHTML = `
                    <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                        ${message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                `;
                alertDiv.style.display = 'block';
                
                // Auto-close error alerts after 5 seconds
                if (type === 'error') {
                    setTimeout(() => {
                        alertDiv.style.display = 'none';
                    }, 5000);
                }
            }
        });
    </script>
</body>
</html>
