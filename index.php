<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceuil</title>
</head>
<body>
    <?php 
    require_once __DIR__ . '/config/Database.php';
    require_once __DIR__ . '/config/ErrorHandler.php';
    require_once __DIR__ . '/includes/header.php'; 
    ?>

<main>
    <!-- Slider/Banner -->
    <section class="hero bg-primary text-white py-5">
        <div class="container">
            <div id="heroCarousel" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-inner">
                    <div class="carousel-item active">
                        <div class="row align-items-center">
                            <div class="col-lg-6">
                                <h1 class="display-4">Innovation, Formation, Impact Social</h1>
                                <p class="lead">BETHEL LABS : Technologie et formation pour un avenir meilleur au Bénin.</p>
                                <a href="contact.php" class="btn btn-warning btn-lg">Contactez-nous</a>
                                <a href="inscription.php" class="btn btn-outline-light btn-lg ml-2">S'inscrire</a>
                            </div>
                            <div class="col-lg-6">
                                <img src="images/slider1.jpg" alt="Innovation" class="img-fluid">
                            </div>
                        </div>
                    </div>
                    <div class="carousel-item">
                        <div class="row align-items-center">
                            <div class="col-lg-6">
                                <h1 class="display-4">Formations pour Tous</h1>
                                <p class="lead">Du primaire à l'université, développez vos compétences en technologie.</p>
                                <a href="formations.php" class="btn btn-warning btn-lg">Voir les Formations</a>
                            </div>
                            <div class="col-lg-6">
                                <img src="images/slider2.png" alt="Formations" class="img-fluid">
                            </div>
                        </div>
                    </div>
                    <div class="carousel-item">
                        <div class="row align-items-center">
                            <div class="col-lg-6">
                                <h1 class="display-4">Projets Sociaux</h1>
                                <p class="lead">Impact positif sur la communauté à travers nos initiatives.</p>
                                <a href="portfolio.php" class="btn btn-warning btn-lg">Voir nos Projets</a>
                            </div>
                            <div class="col-lg-6">
                                <img src="images/slider3.jpg" alt="Projets Sociaux" class="img-fluid">
                            </div>
                        </div>
                    </div>
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                </button>
            </div>
        </div>
    </section>

    <!-- Quick Sections -->
    <section class="py-5">
        <div class="container">
            <div class="row text-center">
                <div class="col-md-3">
                    <h3>Services</h3>
                    <p>Développement web, cybersécurité, hardware.</p>
                    <a href="services.php" class="btn btn-primary">En savoir plus</a>
                </div>
                <div class="col-md-3">
                    <h3>Formations</h3>
                    <p>Pour primaire, collège, université.</p>
                    <a href="formations.php" class="btn btn-success">S'inscrire</a>
                </div>
                <div class="col-md-3">
                    <h3>Concours</h3>
                    <p>Découvrez nos talents.</p>
                    <a href="concours.php" class="btn btn-warning">Participer</a>
                </div>
                <div class="col-md-3">
                    <h3>Actualités</h3>
                    <p>Restez informés.</p>
                    <a href="news.php" class="btn btn-info">Lire</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Détaillés -->
    <section class="py-5 bg-white">
        <div class="container">
            <h2 class="text-center mb-5 display-5">Nos Services</h2>

            <!-- Développement d'Applications -->
            <div class="row align-items-center mb-5 pb-5 border-bottom">
                <div class="col-lg-6 mb-4 mb-lg-0">
                    <h3 class="mb-4 text-primary">Développement d'Applications</h3>
                    <p class="lead mb-3">
                        BETHEL LABS excelle dans le développement d'applications modernes et innovantes pour tous les secteurs.
                    </p>
                    <p class="mb-3">
                        <strong>Nos domaines d'expertise :</strong>
                    </p>
                    <ul class="list-unstyled mb-4">
                        <li class="mb-2">
                            <i class="fas fa-check-circle text-success"></i>
                            <strong>Applications Web :</strong> Sites dynamiques, e-commerce, plateformes collaboratives
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check-circle text-success"></i>
                            <strong>Applications Mobile :</strong> Apps iOS et Android performantes et user-friendly
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check-circle text-success"></i>
                            <strong>Applications Desktop :</strong> Logiciels robustes pour Windows, macOS et Linux
                        </li>
                    </ul>
                    <p>
                        Nous utilisons les technologies les plus récentes pour créer des solutions adaptées à vos besoins.
                    </p>
                    <a href="services.php" class="btn btn-primary btn-lg">Découvrir nos projets</a>
                </div>
                <div class="col-lg-6">
                    <img src="images/service-dev.jpg" alt="Développement d'applications" class="img-fluid rounded shadow mb-3">
                    <div class="ratio ratio-16x9">
                        <iframe src="https://www.youtube.com/embed/dQw4w9WgXcQ" title="Développement d'applications" allowfullscreen="" loading="lazy"></iframe>
                    </div>
                </div>
            </div>

            <!-- Formations Technologiques -->
            <div class="row align-items-center mb-5 pb-5 border-bottom flex-row-reverse">
                <div class="col-lg-6 mb-4 mb-lg-0">
                    <h3 class="mb-4 text-success">Formations Technologiques</h3>
                    <p class="lead mb-3">
                        Montez en compétences avec nos formations adaptées à tous les niveaux, du primaire à l'université.
                    </p>
                    <p class="mb-3">
                        <strong>Technologies enseignées :</strong>
                    </p>
                    <ul class="list-unstyled mb-4">
                        <li class="mb-2">
                            <i class="fas fa-check-circle text-success"></i>
                            <strong>Web :</strong> HTML5, CSS3, JavaScript, React, Vue.js, Angular, Node.js
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check-circle text-success"></i>
                            <strong>Mobile :</strong> Flutter, React Native, Swift, Kotlin
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check-circle text-success"></i>
                            <strong>Backend :</strong> Python, Java, C#, PHP, SQL, Firebase
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check-circle text-success"></i>
                            <strong>Outils :</strong> Git, Docker, AWS, Figma, et bien d'autres...
                        </li>
                    </ul>
                    <p>
                        Nos formateurs expérimentés vous accompagnent pour maîtriser les technologies de demain.
                    </p>
                    <a href="formations.php" class="btn btn-success btn-lg">S'inscrire aux formations</a>
                </div>
                <div class="col-lg-6">
                    <img src="images/service-formation.jpg" alt="Formations technologiques" class="img-fluid rounded shadow mb-3">
                    <div class="ratio ratio-16x9">
                        <iframe src="https://www.youtube.com/embed/dQw4w9WgXcQ" title="Formations technologiques" allowfullscreen="" loading="lazy"></iframe>
                    </div>
                </div>
            </div>

            <!-- Concours Informatiques -->
            <div class="row align-items-center">
                <div class="col-lg-6 mb-4 mb-lg-0">
                    <h3 class="mb-4 text-warning">Concours Informatiques</h3>
                    <p class="lead mb-3">
                        Testez vos compétences et gagnez des prix dans nos concours exclusivement dédiés aux technologies informatiques.
                    </p>
                    <p class="mb-3">
                        <strong>Nos concours couvrent :</strong>
                    </p>
                    <ul class="list-unstyled mb-4">
                        <li class="mb-2">
                            <i class="fas fa-check-circle text-warning"></i>
                            <strong>Hackathons :</strong> Développez des solutions innovantes en équipe en 24-48 heures
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check-circle text-warning"></i>
                            <strong>Coding Competitions :</strong> Résolvez des problèmes algorithmiques complexes
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check-circle text-warning"></i>
                            <strong>Web Design Challenges :</strong> Créez les plus beaux designs web
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check-circle text-warning"></i>
                            <strong>Cybersécurité :</strong> Relevez les défis de sécurité informatique
                        </li>
                    </ul>
                    <p>
                        Participez, apprenez, gagnez ! Tous les concours sont ouverts aux jeunes talents du Bénin.
                    </p>
                    <a href="concours.php" class="btn btn-warning btn-lg">Participer aux concours</a>
                </div>
                <div class="col-lg-6">
                    <img src="images/service-concours.jpg" alt="Concours informatiques" class="img-fluid rounded shadow mb-3">
                    <div class="ratio ratio-16x9">
                        <iframe src="https://www.youtube.com/embed/dQw4w9WgXcQ" title="Concours informatiques" allowfullscreen="" loading="lazy"></iframe>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Ajouter un Témoignage -->
    <section class="py-5" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
        <div class="container">
            <h2 class="text-center mb-5 text-white">Partagez votre expérience</h2>
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card shadow-lg border-0 rounded-lg">
                            <div class="card-body p-5">
                                <?php if (isset($_GET['testimonial_success'])): ?>
                                    <div class="alert alert-success">Merci — votre témoignage a bien été envoyé et sera modéré avant publication.</div>
                                <?php endif; ?>
                                <?php if (!empty($_GET['testimonial_error'])): ?>
                                    <div class="alert alert-danger"><?php echo htmlspecialchars($_GET['testimonial_error']); ?></div>
                                <?php endif; ?>
                                <form id="testimonialForm" method="POST" action="process_testimonial.php" enctype="multipart/form-data">
                                    <?php echo Csrf::inputField(); ?>
                                    <?php if (!empty($_SESSION['logged_in_user'])): ?>
                                        <p class="small text-muted">Connecté en tant que <strong><?php echo htmlspecialchars($_SESSION['user_name'] ?? $_SESSION['user_email']); ?></strong></p>
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <label class="form-label">Nom</label>
                                                <input type="text" class="form-control" value="<?php echo htmlspecialchars($_SESSION['user_name'] ?? ''); ?>" disabled>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Email</label>
                                                <input type="email" class="form-control" value="<?php echo htmlspecialchars($_SESSION['user_email'] ?? ''); ?>" disabled>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <div class="alert alert-info">
                                            <strong>Important :</strong> Vous devez être connecté pour envoyer un témoignage. <a href="login.php" class="alert-link">Se connecter</a> ou <a href="inscription.php?type=client" class="alert-link">s'inscrire</a>.
                                        </div>
                                        <p class="small text-muted">Vous ne pourrez pas envoyer le témoignage tant que vous n'êtes pas connecté.</p>
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <label for="name" class="form-label">Votre nom</label>
                                                <input type="text" class="form-control" id="name" name="name" placeholder="Entrez votre nom">
                                            </div>
                                            <div class="col-md-6">
                                                <label for="email" class="form-label">Votre email</label>
                                                <input type="email" class="form-control" id="email" name="email" placeholder="Entrez votre email">
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                <!-- Photo de Profil -->
                                <div class="mb-4">
                                    <label for="photo" class="form-label fw-bold">Votre photo (optionnel)</label>
                                    <input type="file" class="form-control form-control-lg" id="photo" name="photo" accept="image/*">
                                    <small class="text-muted d-block mt-2">Format: JPG, PNG, GIF (Max 2MB). La photo sera affichée en rond.</small>
                                </div>

                                <!-- Note avec Étoiles -->
                                <div class="mb-4">
                                    <label class="form-label fw-bold mb-3">Votre note</label>
                                    <div class="star-rating" id="starRating">
                                        <input type="hidden" id="ratingValue" name="rating" value="0">
                                        <span class="star" data-value="1">★</span>
                                        <span class="star" data-value="2">★</span>
                                        <span class="star" data-value="3">★</span>
                                        <span class="star" data-value="4">★</span>
                                        <span class="star" data-value="5">★</span>
                                    </div>
                                    <small class="d-block mt-2 text-muted" id="ratingText">Cliquez sur les étoiles pour noter</small>
                                </div>

                                <!-- Commentaire -->
                                <div class="mb-4">
                                    <label for="comment" class="form-label fw-bold">Votre témoignage</label>
                                    <textarea class="form-control form-control-lg" id="comment" name="comment" rows="5" placeholder="Partagez votre expérience avec BETHEL LABS..." required></textarea>
                                    <small class="text-muted d-block mt-2">Vous pouvez ajouter jusqu'à 1000 caractères</small>
                                </div>

                                <!-- Bouton Soumettre -->
                                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                    <button type="reset" class="btn btn-outline-secondary btn-lg px-5">Réinitialiser</button>
                                    <?php if (!empty($_SESSION['logged_in_user'])): ?>
                                        <button type="submit" class="btn btn-primary btn-lg px-5">Envoyer mon témoignage</button>
                                    <?php else: ?>
                                        <button type="button" class="btn btn-primary btn-lg px-5" disabled>Se connecter pour envoyer</button>
                                    <?php endif; ?>
                                </form>
                                </div>
                        </div>
                    </div>
                    <p class="text-center mt-4 text-white">
                        <small>Votre témoignage sera modéré avant sa publication. Merci de votre confiance !</small>
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials/Partners -->
    <section class="bg-light py-5">
        <div class="container">
            <h2 class="text-center mb-5">Nos Témoignages</h2>
            <div class="row mb-3">
                <div class="col-12 d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <label for="perPageSelect" class="form-label mb-0 me-2">Témoignages par page :</label>
                        <select id="perPageSelect" class="form-select d-inline-block" style="width: auto;">
                            <option value="4">4</option>
                            <option value="8">8</option>
                            <option value="12">12</option>
                        </select>
                    </div>
                    <div id="testimonials-info" class="text-muted small"></div>
                </div>
            </div>

            <div id="testimonials-container" class="row mb-5">
                <!-- Testimonials will be loaded here via AJAX -->
                <div class="col-12 text-center py-4">
                    <div class="spinner-border text-primary" role="status"><span class="visually-hidden">Chargement...</span></div>
                </div>
            </div>

            <div id="testimonials-pagination" class="row">
                <div class="col-12 d-flex justify-content-center">
                    <!-- Pagination controls will be injected here -->
                </div>
            </div>

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const container = document.getElementById('testimonials-container');
                    const paginationWrap = document.getElementById('testimonials-pagination');
                    const perPageSelect = document.getElementById('perPageSelect');
                    const info = document.getElementById('testimonials-info');

                    let currentPage = 1;
                    let perPage = parseInt(perPageSelect.value, 10);

                    async function loadTestimonials(page = 1, per = perPage) {
                        container.innerHTML = '<div class="col-12 text-center py-4"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Chargement...</span></div></div>';
                        paginationWrap.querySelector('.col-12').innerHTML = '';

                        try {
                            const resp = await fetch('fetch_testimonials_ajax.php?page=' + page + '&per_page=' + per);
                            const data = await resp.json();

                            if (data.success) {
                                // Prefer client-side rendering using data when available
                                if (data.data && Array.isArray(data.data)) {
                                    // Render cards via template JS
                                    container.innerHTML = '';
                                    data.data.forEach(function(t) {
                                        const col = document.createElement('div');
                                        col.className = 'col-lg-3 col-md-6 mb-4';

                                        const card = document.createElement('div');
                                        card.className = 'card testimonial-card shadow-sm border-0 h-100';

                                        const body = document.createElement('div');
                                        body.className = 'card-body text-center';

                                        const img = document.createElement('img');
                                        img.className = 'testimonial-avatar mb-3';
                                        img.src = t.image;
                                        img.alt = t.name;

                                        const starsDiv = document.createElement('div');
                                        starsDiv.className = 'rating-stars mb-3';
                                        starsDiv.innerHTML = '<span style="color: #ffc107; font-size: 1.2rem;">' + '★'.repeat(t.rating) + '☆'.repeat(5 - t.rating) + '</span>';

                                        const shortP = document.createElement('p');
                                        shortP.className = 'card-text text-muted testimonial-comment';
                                        shortP.id = 'comment-' + t.id;
                                        shortP.innerText = t.shortComment;

                                        const fullP = document.createElement('p');
                                        fullP.className = 'card-text text-muted testimonial-full-comment';
                                        fullP.id = 'full-comment-' + t.id;
                                        fullP.style.display = 'none';
                                        fullP.innerText = t.comment;

                                        const btn = document.createElement('button');
                                        btn.className = 'btn btn-sm btn-primary read-more-btn';
                                        btn.dataset.id = t.id;
                                        btn.innerText = (t.shortComment.length < t.comment.length) ? 'Lire plus' : '';
                                        if (btn.innerText === '') btn.style.display = 'none';

                                        const nameP = document.createElement('p');
                                        nameP.className = 'mt-3 mb-0';
                                        nameP.innerHTML = '<strong>' + t.name + '</strong>';

                                        body.appendChild(img);
                                        body.appendChild(starsDiv);
                                        body.appendChild(shortP);
                                        body.appendChild(fullP);
                                        body.appendChild(btn);
                                        body.appendChild(nameP);

                                        card.appendChild(body);
                                        col.appendChild(card);
                                        container.appendChild(col);
                                    });

                                    // Attach read-more handlers
                                    attachReadMore();
                                } else {
                                    // Fallback to server-rendered HTML
                                    container.innerHTML = data.html;
                                    attachReadMore();
                                }

                                paginationWrap.querySelector('.col-12').innerHTML = data.pagination;
                                info.textContent = `Page ${data.page} / ${data.totalPages} — ${data.total} témoignage(s)`;
                                currentPage = data.page;

                                // Attach pagination click handlers
                                const pageLinks = paginationWrap.querySelectorAll('.page-link');
                                pageLinks.forEach(link => {
                                    link.addEventListener('click', function(e) {
                                        e.preventDefault();
                                        const p = parseInt(this.getAttribute('data-page'), 10);
                                        if (!isNaN(p)) {
                                            loadTestimonials(p, perPage);
                                            // update URL param
                                            const params = new URLSearchParams(window.location.search);
                                            params.set('testimonials_page', p);
                                            params.set('testimonials_per_page', perPage);
                                            history.replaceState({}, '', `${location.pathname}?${params.toString()}`);
                                        }
                                    });
                                });
                            } else {
                                container.innerHTML = '<div class="col-12 text-center text-danger">Erreur: ' + (data.message || 'Impossible de charger les témoignages.') + '</div>';
                            }
                        } catch (err) {
                            container.innerHTML = '<div class="col-12 text-center text-danger">Erreur de connexion : ' + err.message + '</div>';
                        }
                    }

                    function attachReadMore() {
                        const readMoreButtons = document.querySelectorAll('.read-more-btn');
                        readMoreButtons.forEach(btn => {
                            btn.addEventListener('click', function() {
                                const id = this.getAttribute('data-id');
                                const full = document.getElementById('full-comment-' + id);
                                const short = document.getElementById('comment-' + id);
                                if (full && short) {
                                    if (full.style.display === 'none') {
                                        short.style.display = 'none';
                                        full.style.display = 'block';
                                        this.textContent = 'Afficher moins';
                                    } else {
                                        full.style.display = 'none';
                                        short.style.display = 'block';
                                        this.textContent = 'Lire plus';
                                    }
                                }
                            });
                        });
                    }

                    // per-page change
                    perPageSelect.addEventListener('change', function() {
                        perPage = parseInt(this.value, 10);
                        loadTestimonials(1, perPage);
                        const params = new URLSearchParams(window.location.search);
                        params.set('testimonials_per_page', perPage);
                        params.delete('testimonials_page');
                        history.replaceState({}, '', `${location.pathname}?${params.toString()}`);
                    });

                    // Initialize from URL params if present
                    const params = new URLSearchParams(window.location.search);
                    const urlPage = parseInt(params.get('testimonials_page'));
                    const urlPer = parseInt(params.get('testimonials_per_page'));
                    if (!isNaN(urlPer)) {
                        perPageSelect.value = urlPer;
                        perPage = urlPer;
                    }
                    if (!isNaN(urlPage)) {
                        currentPage = Math.max(1, urlPage);
                    }

                    // Initial load
                    loadTestimonials(currentPage, perPage);
                });
            </script>

            <h3 class="text-center mt-5 mb-4">Nos Partenaires</h3>
            <div class="row justify-content-center">
                <?php
                // Récupérer les partenaires publiés depuis la base de données
                try {
                    require_once __DIR__ . '/models/Partner.php';
                    $partnerModel = new Partner();
                    $partners = $partnerModel->getByStatus('published', 'created_at DESC', 6);
                ?>
                <?php if (!empty($partners)): ?>
                    <?php foreach ($partners as $partner): ?>
                        <div class="col-md-2 col-6 text-center mb-4">
                            <?php if (!empty($partner['image'])): ?>
                                <img src="<?php echo htmlspecialchars($partner['image']); ?>" class="img-fluid mb-2" alt="<?php echo htmlspecialchars($partner['name']); ?>">
                            <?php else: ?>
                                <img src="images/partner-placeholder.png" class="img-fluid mb-2" alt="partner">
                            <?php endif; ?>
                            <div><strong><?php echo htmlspecialchars($partner['name'] . ' ' . $partner['firstname']); ?></strong></div>
                            <small class="text-muted"><?php echo htmlspecialchars($partner['expertise']); ?></small>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12 text-center"><p class="text-muted">Aucun partenaire pour le moment.</p></div>
                <?php endif; ?>
                <?php
                } catch (Exception $e) {
                    echo '<div class="col-12 text-center"><p class="text-danger">Erreur lors du chargement des partenaires: ' . htmlspecialchars($e->getMessage()) . '</p></div>';
                }
                ?>

            </div>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>
</body>
</html>