<?php session_start(); include 'includes/header.php'; ?>

<main>
    <!-- Portfolio Hero Section -->
    <section class="portfolio-hero py-5 text-white text-center">
        <div class="container">
            <h1 class="display-4 fw-bold mb-3">Notre Portfolio</h1>
            <p class="lead mb-0">Découvrez nos projets innovants et réalisations exceptionnelles</p>
        </div>
    </section>

    <!-- Filter Section -->
    <div class="container py-4">
        <div class="filter-container text-center">
            <button class="filter-btn active" data-filter="all">
                <i class="fas fa-th"></i> Tous les projets
            </button>
            <button class="filter-btn" data-filter="web">
                <i class="fas fa-globe"></i> Web
            </button>
            <button class="filter-btn" data-filter="mobile">
                <i class="fas fa-mobile-alt"></i> Mobile
            </button>
            <button class="filter-btn" data-filter="security">
                <i class="fas fa-shield-alt"></i> Sécurité
            </button>
        </div>
    </div>

    <!-- Projects Grid Section -->
    <section class="py-5">
        <div class="container">
            <div class="row g-4">
                <!-- Project 1 -->
                <div class="col-lg-4 col-md-6 project-item" data-category="web">
                    <div class="portfolio-card">
                        <div class="portfolio-image">
                            <img src="images/project1.jpg" alt="Site web e-commerce" class="img-fluid">
                            <div class="portfolio-overlay">
                                <div class="overlay-content">
                                    <button class="btn-view" data-bs-toggle="modal" data-bs-target="#projectModal1">
                                        <i class="fas fa-eye"></i> Voir plus
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="portfolio-info">
                            <span class="project-category">Web</span>
                            <h5 class="project-title">Site web e-commerce</h5>
                            <p class="project-desc">Plateforme de vente en ligne complète avec panier, paiement et gestion d'inventaire.</p>
                            <div class="project-meta">
                                <span class="tech-tag">PHP</span>
                                <span class="tech-tag">MySQL</span>
                                <span class="tech-tag">Bootstrap</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Project 2 -->
                <div class="col-lg-4 col-md-6 project-item" data-category="mobile">
                    <div class="portfolio-card">
                        <div class="portfolio-image">
                            <img src="images/project2.jpg" alt="Application mobile éducative" class="img-fluid">
                            <div class="portfolio-overlay">
                                <div class="overlay-content">
                                    <button class="btn-view" data-bs-toggle="modal" data-bs-target="#projectModal2">
                                        <i class="fas fa-eye"></i> Voir plus
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="portfolio-info">
                            <span class="project-category">Mobile</span>
                            <h5 class="project-title">App mobile éducative</h5>
                            <p class="project-desc">Application d'apprentissage des mathématiques avec jeux interactifs et suivi de progression.</p>
                            <div class="project-meta">
                                <span class="tech-tag">React Native</span>
                                <span class="tech-tag">Firebase</span>
                                <span class="tech-tag">Animations</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Project 3 -->
                <div class="col-lg-4 col-md-6 project-item" data-category="security">
                    <div class="portfolio-card">
                        <div class="portfolio-image">
                            <img src="images/project3.jpg" alt="Système de cybersécurité" class="img-fluid">
                            <div class="portfolio-overlay">
                                <div class="overlay-content">
                                    <button class="btn-view" data-bs-toggle="modal" data-bs-target="#projectModal3">
                                        <i class="fas fa-eye"></i> Voir plus
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="portfolio-info">
                            <span class="project-category">Sécurité</span>
                            <h5 class="project-title">Système de cybersécurité</h5>
                            <p class="project-desc">Solutions de sécurité avancées pour entreprises avec chiffrement et authentification multi-facteurs.</p>
                            <div class="project-meta">
                                <span class="tech-tag">Python</span>
                                <span class="tech-tag">Cryptographie</span>
                                <span class="tech-tag">Linux</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Events/Success Stories Section -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="section-title">Nos Événements & Succès</h2>
                <p class="section-subtitle">Moments forts et réalisations marquantes</p>
            </div>

            <div class="row g-4">
                <!-- Event 1 -->
                <div class="col-lg-6">
                    <div class="event-card">
                        <div class="event-image">
                            <img src="images/event1.jpg" alt="Atelier de programmation 2025" class="img-fluid">
                            <div class="event-badge">
                                <i class="fas fa-graduation-cap"></i>
                                Formation
                            </div>
                        </div>
                        <div class="event-content">
                            <h4 class="event-title">Atelier de programmation 2025</h4>
                            <div class="event-details">
                                <span><i class="fas fa-users"></i> 50 jeunes développeurs</span>
                                <span><i class="fas fa-map-marker-alt"></i> Parakou</span>
                            </div>
                            <p class="event-desc">Formation intensive couvrant les technologies web modernes et les bonnes pratiques de développement.</p>
                            <a href="#" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-arrow-right"></i> En savoir plus
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Event 2 -->
                <div class="col-lg-6">
                    <div class="event-card">
                        <div class="event-image">
                            <img src="images/event2.jpg" alt="Concours national d'innovation" class="img-fluid">
                            <div class="event-badge">
                                <i class="fas fa-trophy"></i>
                                Concours
                            </div>
                        </div>
                        <div class="event-content">
                            <h4 class="event-title">Concours national d'innovation</h4>
                            <div class="event-details">
                                <span><i class="fas fa-users"></i> 200+ participants</span>
                                <span><i class="fas fa-trophy"></i> 15 prix</span>
                            </div>
                            <p class="event-desc">Un événement fédérateur réunissant les talents de la région autour de l'innovation technologique.</p>
                            <a href="#" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-arrow-right"></i> En savoir plus
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="portfolio-cta py-5 text-white text-center">
        <div class="container">
            <h3 class="display-5 fw-bold mb-3">Prêt à démarrer votre projet ?</h3>
            <p class="lead mb-4">Contactez-nous pour discuter de vos besoins et transformer votre idée en réalité</p>
            <a href="contact.php" class="btn btn-light btn-lg">
                <i class="fas fa-envelope"></i> Nous contacter
            </a>
        </div>
    </section>
</main>

<!-- Project Modals -->
<div class="modal fade" id="projectModal1" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Site web e-commerce</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <img src="images/project1.jpg" class="img-fluid mb-3" alt="Site web e-commerce">
                <h6>Description complète</h6>
                <p>Développement d'une plateforme de vente en ligne pour une entreprise locale, incluant :</p>
                <ul>
                    <li>Catalogue de produits avec filtrage avancé</li>
                    <li>Système de panier et gestion des commandes</li>
                    <li>Intégration de paiement sécurisée</li>
                    <li>Panneau d'administration complet</li>
                </ul>
                <p><strong>Technologies:</strong> PHP, MySQL, Bootstrap, JavaScript</p>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="projectModal2" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Application mobile éducative</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <img src="images/project2.jpg" class="img-fluid mb-3" alt="Application mobile éducative">
                <h6>Description complète</h6>
                <p>Application mobile pour l'apprentissage interactif des mathématiques, proposant :</p>
                <ul>
                    <li>Exercices progressifs et adaptatifs</li>
                    <li>Gamification avec récompenses et badges</li>
                    <li>Suivi détaillé de la progression</li>
                    <li>Mode hors ligne disponible</li>
                </ul>
                <p><strong>Technologies:</strong> React Native, Firebase, Animations CSS</p>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="projectModal3" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Système de cybersécurité</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <img src="images/project3.jpg" class="img-fluid mb-3" alt="Système de cybersécurité">
                <h6>Description complète</h6>
                <p>Mise en place de solutions de sécurité avancées pour une institution bancaire, incluant :</p>
                <ul>
                    <li>Chiffrement end-to-end des données</li>
                    <li>Authentification multi-facteurs</li>
                    <li>Monitoring et alertes en temps réel</li>
                    <li>Conformité aux normes ISO 27001</li>
                </ul>
                <p><strong>Technologies:</strong> Python, Cryptographie, Linux, Kubernetes</p>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>