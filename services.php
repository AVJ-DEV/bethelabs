<?php 
require_once __DIR__ . '/config/Database.php';
require_once __DIR__ . '/config/ErrorHandler.php';
require_once __DIR__ . '/includes/header.php'; 
?>

<main>
    <!-- Hero Section -->
    <section class="services-hero bg-primary text-white py-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1 class="display-4 fw-bold mb-4">Nos Services Innovants</h1>
                    <p class="lead mb-0">Découvrez notre gamme complète de services technologiques conçus pour transformer votre entreprise et accélérer votre croissance numérique.</p>
                </div>
                <div class="col-lg-6">
                    <img src="images/code4.png" alt="Nos Services" class="img-fluid rounded shadow-lg">
                </div>
            </div>
        </div>
    </section>

    <!-- Services Grid Section -->
    <section class="py-5">
        <div class="container">
            <h2 class="text-center mb-5 display-5 fw-bold">Nos Domaines d'Expertise</h2>
            <div class="row g-4">
                <!-- Service 1: Développement Web & Mobile -->
                <div class="col-lg-4 col-md-6">
                    <div class="service-card">
                        <div class="service-icon">
                            <i class="fas fa-mobile-alt"></i>
                        </div>
                        <h3 class="service-title">Développement Web & Mobile</h3>
                        <p class="service-description">
                            Nous créons des applications web et mobile performantes, scalables et user-friendly. Nos experts maîtrisent les dernières technologies du marché.
                        </p>
                        <ul class="service-features">
                            <li><i class="fas fa-check-circle text-success me-2"></i>Applications Web Réactives</li>
                            <li><i class="fas fa-check-circle text-success me-2"></i>Apps Mobile iOS/Android</li>
                            <li><i class="fas fa-check-circle text-success me-2"></i>Progressive Web Apps</li>
                            <li><i class="fas fa-check-circle text-success me-2"></i>API REST & GraphQL</li>
                        </ul>
                        <a href="contact.php" class="btn btn-primary btn-sm mt-3">Demander un devis</a>
                    </div>
                </div>

                <!-- Service 2: Réseaux & Cybersécurité -->
                <div class="col-lg-4 col-md-6">
                    <div class="service-card">
                        <div class="service-icon">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <h3 class="service-title">Réseaux & Cybersécurité</h3>
                        <p class="service-description">
                            Protégez vos données et infrastructures avec nos solutions de sécurité informatique de pointe. Nous assurons la conformité et la protection maximale.
                        </p>
                        <ul class="service-features">
                            <li><i class="fas fa-check-circle text-success me-2"></i>Audit de Sécurité</li>
                            <li><i class="fas fa-check-circle text-success me-2"></i>Gestion des Réseaux</li>
                            <li><i class="fas fa-check-circle text-success me-2"></i>Firewall & VPN</li>
                            <li><i class="fas fa-check-circle text-success me-2"></i>Conformité RGPD</li>
                        </ul>
                        <a href="contact.php" class="btn btn-primary btn-sm mt-3">Demander un devis</a>
                    </div>
                </div>

                <!-- Service 3: Solutions Hardware & Électronique -->
                <div class="col-lg-4 col-md-6">
                    <div class="service-card">
                        <div class="service-icon">
                            <i class="fas fa-microchip"></i>
                        </div>
                        <h3 class="service-title">Solutions Hardware & Électronique</h3>
                        <p class="service-description">
                            Consultations et intégrations hardware pour vos besoins technologiques. Solutions IoT, électronique embarquée et systèmes intelligents.
                        </p>
                        <ul class="service-features">
                            <li><i class="fas fa-check-circle text-success me-2"></i>Systèmes IoT</li>
                            <li><i class="fas fa-check-circle text-success me-2"></i>Électronique Embarquée</li>
                            <li><i class="fas fa-check-circle text-success me-2"></i>Intégration Hardware</li>
                            <li><i class="fas fa-check-circle text-success me-2"></i>Systèmes Intelligents</li>
                        </ul>
                        <a href="contact.php" class="btn btn-primary btn-sm mt-3">Demander un devis</a>
                    </div>
                </div>

                <!-- Service 4: Consulting & Architecture -->
                <div class="col-lg-4 col-md-6">
                    <div class="service-card">
                        <div class="service-icon">
                            <i class="fas fa-chess"></i>
                        </div>
                        <h3 class="service-title">Consulting & Architecture</h3>
                        <p class="service-description">
                            Nous vous accompagnons dans la définition de votre stratégie technologique et architecture système pour optimiser vos processus.
                        </p>
                        <ul class="service-features">
                            <li><i class="fas fa-check-circle text-success me-2"></i>Audit Technologique</li>
                            <li><i class="fas fa-check-circle text-success me-2"></i>Stratégie Digitale</li>
                            <li><i class="fas fa-check-circle text-success me-2"></i>Architecture Cloud</li>
                            <li><i class="fas fa-check-circle text-success me-2"></i>Transformation Digitale</li>
                        </ul>
                        <a href="contact.php" class="btn btn-primary btn-sm mt-3">Demander un devis</a>
                    </div>
                </div>

                <!-- Service 5: Support & Maintenance -->
                <div class="col-lg-4 col-md-6">
                    <div class="service-card">
                        <div class="service-icon">
                            <i class="fas fa-tools"></i>
                        </div>
                        <h3 class="service-title">Support & Maintenance</h3>
                        <p class="service-description">
                            Support technique continu et maintenance préventive pour assurer la disponibilité et la performance optimale de vos systèmes.
                        </p>
                        <ul class="service-features">
                            <li><i class="fas fa-check-circle text-success me-2"></i>Support 24/7</li>
                            <li><i class="fas fa-check-circle text-success me-2"></i>Maintenance Préventive</li>
                            <li><i class="fas fa-check-circle text-success me-2"></i>Monitoring Continu</li>
                            <li><i class="fas fa-check-circle text-success me-2"></i>SLA Garanti</li>
                        </ul>
                        <a href="contact.php" class="btn btn-primary btn-sm mt-3">Demander un devis</a>
                    </div>
                </div>

                <!-- Service 6: Formation & Training -->
                <div class="col-lg-4 col-md-6">
                    <div class="service-card">
                        <div class="service-icon">
                            <i class="fas fa-graduation-cap"></i>
                        </div>
                        <h3 class="service-title">Formation & Training</h3>
                        <p class="service-description">
                            Programmes de formation personnalisés pour vos équipes. Montez en compétences avec nos experts technologiques certifiés.
                        </p>
                        <ul class="service-features">
                            <li><i class="fas fa-check-circle text-success me-2"></i>Formations Sur-Mesure</li>
                            <li><i class="fas fa-check-circle text-success me-2"></i>Certification Professionnelle</li>
                            <li><i class="fas fa-check-circle text-success me-2"></i>Workshops Pratiques</li>
                            <li><i class="fas fa-check-circle text-success me-2"></i>Documentation Technique</li>
                        </ul>
                        <a href="contact.php" class="btn btn-primary btn-sm mt-3">Demander un devis</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Process Section -->
    <section class="py-5 bg-light">
        <div class="container">
            <h2 class="text-center mb-5 display-5 fw-bold">Notre Approche</h2>
            <div class="row g-4">
                <div class="col-md-3 text-center">
                    <div class="process-step">
                        <div class="step-number">1</div>
                        <h4 class="mt-3 fw-bold">Découverte</h4>
                        <p class="text-muted">Nous analysons vos besoins et vos défis spécifiques.</p>
                    </div>
                </div>
                <div class="col-md-3 text-center">
                    <div class="process-step">
                        <div class="step-number">2</div>
                        <h4 class="mt-3 fw-bold">Stratégie</h4>
                        <p class="text-muted">Élaboration d'une stratégie personnalisée et optimale.</p>
                    </div>
                </div>
                <div class="col-md-3 text-center">
                    <div class="process-step">
                        <div class="step-number">3</div>
                        <h4 class="mt-3 fw-bold">Exécution</h4>
                        <p class="text-muted">Implémentation rigoureuse de la solution avec qualité.</p>
                    </div>
                </div>
                <div class="col-md-3 text-center">
                    <div class="process-step">
                        <div class="step-number">4</div>
                        <h4 class="mt-3 fw-bold">Succès</h4>
                        <p class="text-muted">Support continu et optimisation pour vos résultats.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-5 text-white text-center" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
        <div class="container">
            <h2 class="mb-4 display-5 fw-bold">Prêt à Transformer Votre Entreprise ?</h2>
            <p class="lead mb-4">Contactez-nous pour une consultation gratuite et décourez comment nous pouvons vous aider.</p>
            <a href="contact.php" class="btn btn-light btn-lg px-5 fw-bold me-3">Nous Contacter</a>
            <a href="formations.php" class="btn btn-outline-light btn-lg px-5 fw-bold">Voir les Formations</a>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>