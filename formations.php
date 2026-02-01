<?php 
require_once __DIR__ . '/config/Database.php';
require_once __DIR__ . '/config/ErrorHandler.php';
require_once __DIR__ . '/includes/header.php'; 
?>

<main>
    <!-- Hero Section -->
    <section class="formations-hero text-white py-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1 class="display-4 fw-bold mb-4">Formations Technologiques</h1>
                    <p class="lead mb-4">Des formations adaptées à tous les niveaux, du primaire à l'université. Apprenez les technologies de demain avec nos experts.</p>
                    <a href="inscription.php" class="btn btn-light btn-lg px-5 me-3 fw-bold">S'inscrire Maintenant</a>
                    <a href="#programmes" class="btn btn-outline-light btn-lg px-5 fw-bold">Voir les Programmes</a>
                </div>
                <div class="col-lg-6">
                    <img src="images/code6.png" alt="Formations" class="img-fluid rounded shadow-lg">
                </div>
            </div>
        </div>
    </section>

    <!-- Formation Levels Section -->
    <section class="py-5 bg-light" id="programmes">
        <div class="container">
            <h2 class="text-center mb-5 display-5 fw-bold">Nos Niveaux de Formation</h2>
            <div class="row g-4">
                <!-- Niveau 1: Primaire -->
                <div class="col-lg-6">
                    <div class="formation-level-card">
                        <div class="level-header bg-info text-white">
                            <i class="fas fa-child fa-2x mb-3"></i>
                            <h3 class="fw-bold mb-0">Primaire</h3>
                            <p class="mb-0 mt-2">Classes 3-6</p>
                        </div>
                        <div class="level-content p-4">
                            <p class="text-muted mb-3">Introduction ludique aux concepts informatiques de base. Apprentissage par projets créatifs et interactifs.</p>
                            <h5 class="fw-bold mb-3">Programme:</h5>
                            <ul class="formation-list">
                                <li><i class="fas fa-laptop text-info me-2"></i>Initiation à l'informatique</li>
                                <li><i class="fas fa-cubes text-info me-2"></i>Programmation avec Scratch</li>
                                <li><i class="fas fa-robot text-info me-2"></i>Robotique éducative</li>
                                <li><i class="fas fa-pencil-alt text-info me-2"></i>Création numérique</li>
                            </ul>
                            <div class="mt-4">
                                <p class="mb-2"><strong>Durée:</strong> 3-6 mois</p>
                                <p class="mb-2"><strong>Fréquence:</strong> 2-3 séances/semaine</p>
                                <p><strong>Âge:</strong> 8-12 ans</p>
                            </div>
                            <a href="inscription.php" class="btn btn-info mt-4 w-100 fw-bold">S'inscrire</a>
                        </div>
                    </div>
                </div>

                <!-- Niveau 2: Collège -->
                <div class="col-lg-6">
                    <div class="formation-level-card">
                        <div class="level-header bg-success text-white">
                            <i class="fas fa-users fa-2x mb-3"></i>
                            <h3 class="fw-bold mb-0">Collège</h3>
                            <p class="mb-0 mt-2">Classes 4-3</p>
                        </div>
                        <div class="level-content p-4">
                            <p class="text-muted mb-3">Approfondissement des connaissances informatiques. Bases de programmation et web design.</p>
                            <h5 class="fw-bold mb-3">Programme:</h5>
                            <ul class="formation-list">
                                <li><i class="fas fa-code text-success me-2"></i>HTML5 & CSS3</li>
                                <li><i class="fas fa-brands fa-js text-success me-2"></i>JavaScript Basique</li>
                                <li><i class="fas fa-database text-success me-2"></i>Introduction aux Bases de Données</li>
                                <li><i class="fas fa-globe text-success me-2"></i>Création de Sites Web</li>
                            </ul>
                            <div class="mt-4">
                                <p class="mb-2"><strong>Durée:</strong> 6-12 mois</p>
                                <p class="mb-2"><strong>Fréquence:</strong> 2-3 séances/semaine</p>
                                <p><strong>Âge:</strong> 12-16 ans</p>
                            </div>
                            <a href="inscription.php" class="btn btn-success mt-4 w-100 fw-bold">S'inscrire</a>
                        </div>
                    </div>
                </div>

                <!-- Niveau 3: Lycée -->
                <div class="col-lg-6">
                    <div class="formation-level-card">
                        <div class="level-header bg-warning text-dark">
                            <i class="fas fa-graduation-cap fa-2x mb-3"></i>
                            <h3 class="fw-bold mb-0">Lycée</h3>
                            <p class="mb-0 mt-2">Classes 2-Terminale</p>
                        </div>
                        <div class="level-content p-4">
                            <p class="text-muted mb-3">Formation avancée en développement. Préparation aux études supérieures.</p>
                            <h5 class="fw-bold mb-3">Programme:</h5>
                            <ul class="formation-list">
                                <li><i class="fas fa-react text-warning me-2"></i>Frontend Modern (React, Vue.js)</li>
                                <li><i class="fas fa-server text-warning me-2"></i>Backend et Serveurs</li>
                                <li><i class="fas fa-database text-warning me-2"></i>Gestion de Bases de Données</li>
                                <li><i class="fas fa-mobile-alt text-warning me-2"></i>Développement Mobile</li>
                            </ul>
                            <div class="mt-4">
                                <p class="mb-2"><strong>Durée:</strong> 9-18 mois</p>
                                <p class="mb-2"><strong>Fréquence:</strong> 3-4 séances/semaine</p>
                                <p><strong>Âge:</strong> 16-18 ans</p>
                            </div>
                            <a href="inscription.php" class="btn btn-warning mt-4 w-100 fw-bold">S'inscrire</a>
                        </div>
                    </div>
                </div>

                <!-- Niveau 4: Universitaire -->
                <div class="col-lg-6">
                    <div class="formation-level-card">
                        <div class="level-header bg-primary text-white">
                            <i class="fas fa-university fa-2x mb-3"></i>
                            <h3 class="fw-bold mb-0">Universitaire & Pro</h3>
                            <p class="mb-0 mt-2">Étudiants & Professionnels</p>
                        </div>
                        <div class="level-content p-4">
                            <p class="text-muted mb-3">Formations spécialisées et certifications professionnelles reconnues internationalement.</p>
                            <h5 class="fw-bold mb-3">Programme:</h5>
                            <ul class="formation-list">
                                <li><i class="fas fa-code-branch text-primary me-2"></i>Full Stack Development</li>
                                <li><i class="fas fa-shield-alt text-primary me-2"></i>Cybersécurité</li>
                                <li><i class="fas fa-cloud text-primary me-2"></i>Cloud Computing & DevOps</li>
                                <li><i class="fas fa-brain text-primary me-2"></i>Intelligence Artificielle & Data Science</li>
                            </ul>
                            <div class="mt-4">
                                <p class="mb-2"><strong>Durée:</strong> 3-12 mois</p>
                                <p class="mb-2"><strong>Fréquence:</strong> Flexible (Temps plein/Partiel)</p>
                                <p><strong>Certification:</strong> Oui (Reconnue)</p>
                            </div>
                            <a href="inscription.php" class="btn btn-primary mt-4 w-100 fw-bold">S'inscrire</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Avantages Section -->
    <section class="py-5">
        <div class="container">
            <h2 class="text-center mb-5 display-5 fw-bold">Pourquoi Choisir BETHEL LABS ?</h2>
            <div class="row g-4">
                <div class="col-md-6 col-lg-3">
                    <div class="advantage-card text-center">
                        <div class="advantage-icon">
                            <i class="fas fa-chalkboard-user fa-3x text-primary"></i>
                        </div>
                        <h4 class="fw-bold mt-3 mb-2">Formateurs Experts</h4>
                        <p class="text-muted">Formateurs certifiés avec expérience industrie réelle.</p>
                    </div>
                </div>

                <div class="col-md-6 col-lg-3">
                    <div class="advantage-card text-center">
                        <div class="advantage-icon">
                            <i class="fas fa-laptop fa-3x text-success"></i>
                        </div>
                        <h4 class="fw-bold mt-3 mb-2">Pratique Intensive</h4>
                        <p class="text-muted">80% pratique, 20% théorie. Projets réels.</p>
                    </div>
                </div>

                <div class="col-md-6 col-lg-3">
                    <div class="advantage-card text-center">
                        <div class="advantage-icon">
                            <i class="fas fa-certificate fa-3x text-warning"></i>
                        </div>
                        <h4 class="fw-bold mt-3 mb-2">Certifications</h4>
                        <p class="text-muted">Certifications reconnues à l'issue de la formation.</p>
                    </div>
                </div>

                <div class="col-md-6 col-lg-3">
                    <div class="advantage-card text-center">
                        <div class="advantage-icon">
                            <i class="fas fa-handshake fa-3x text-danger"></i>
                        </div>
                        <h4 class="fw-bold mt-3 mb-2">Opportunités</h4>
                        <p class="text-muted">Accès à nos offres d'emploi et stages.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing Section -->
    <section class="py-5 bg-light">
        <div class="container">
            <h2 class="text-center mb-5 display-5 fw-bold">Tarifs & Bourses</h2>
            <div class="row">
                <div class="col-lg-8 mx-auto">
                    <div class="pricing-table">
                        <table class="table table-striped">
                            <thead class="table-primary">
                                <tr>
                                    <th>Formation</th>
                                    <th>Niveau</th>
                                    <th>Durée</th>
                                    <th>Tarif</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Initiation Informatique</td>
                                    <td>Primaire</td>
                                    <td>3-6 mois</td>
                                    <td><span class="badge bg-success">30,000 CFA</span></td>
                                </tr>
                                <tr>
                                    <td>Web & Mobile Basique</td>
                                    <td>Collège</td>
                                    <td>6-12 mois</td>
                                    <td><span class="badge bg-success">50,000 CFA</span></td>
                                </tr>
                                <tr>
                                    <td>Développement Avancé</td>
                                    <td>Lycée</td>
                                    <td>9-18 mois</td>
                                    <td><span class="badge bg-success">80,000 CFA</span></td>
                                </tr>
                                <tr>
                                    <td>Certification Professionnelle</td>
                                    <td>Uni & Pro</td>
                                    <td>3-12 mois</td>
                                    <td><span class="badge bg-info">Sur devis</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="alert alert-info mt-4" role="alert">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Bourses disponibles :</strong> Réductions de 20-50% pour les étudiants méritants et familles à revenus limités.
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-5 text-white text-center" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
        <div class="container">
            <h2 class="mb-4 display-5 fw-bold">Prêt à Commencer Votre Apprentissage ?</h2>
            <p class="lead mb-4">Inscrivez-vous dès maintenant et rejoignez notre communauté d'apprenants passionnés.</p>
            <a href="inscription.php" class="btn btn-light btn-lg px-5 fw-bold me-3">S'inscrire Maintenant</a>
            <a href="contact.php" class="btn btn-outline-light btn-lg px-5 fw-bold">Poser une Question</a>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>