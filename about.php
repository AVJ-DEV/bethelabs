<?php 
require_once __DIR__ . '/config/Database.php';
require_once __DIR__ . '/config/ErrorHandler.php';
require_once __DIR__ . '/includes/header.php'; 
?>
<main>
    <!-- Hero Section -->
    <section class="about-hero bg-primary text-white py-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1 class="display-4 fw-bold mb-4">À propos de BETHEL LABS</h1>
                    <p class="lead mb-0">Nous transformons les rêves en réalité grâce à la technologie et l'innovation au Bénin.</p>
                </div>
                <div class="col-lg-6">
                    <img src="images/code2.png" alt="À propos de BETHEL LABS" class="img-fluid rounded shadow-lg">
                </div>
            </div>
        </div>
    </section>

    <!-- Mission & Vision Section -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="row g-4">
                <!-- Mission -->
                <div class="col-lg-6">
                    <div class="card border-0 shadow-lg h-100">
                        <div class="card-body p-4">
                            <div class="icon-box mb-3">
                                <i class="fas fa-target fa-3x text-primary"></i>
                            </div>
                            <h3 class="card-title mb-3 fw-bold">Notre Mission</h3>
                            <p class="card-text text-muted">
                                Chez BETHEL LABS, notre mission est de promouvoir l'innovation technologique et l'éducation numérique au Bénin. Nous croyons que la technologie peut transformer des vies et contribuer au développement durable de notre pays.
                            </p>
                            <ul class="list-unstyled mt-3">
                                <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i> Démocratiser l'accès à la technologie</li>
                                <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i> Former les talents de demain</li>
                                <li><i class="fas fa-check-circle text-success me-2"></i> Créer des solutions innovantes</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Vision -->
                <div class="col-lg-6">
                    <div class="card border-0 shadow-lg h-100">
                        <div class="card-body p-4">
                            <div class="icon-box mb-3">
                                <i class="fas fa-eye fa-3x text-success"></i>
                            </div>
                            <h3 class="card-title mb-3 fw-bold">Notre Vision</h3>
                            <p class="card-text text-muted">
                                Devenir le leader régional en matière de formation technologique et de développement de solutions innovantes, contribuant ainsi à la création d'emplois et à l'amélioration de la qualité de vie au Bénin.
                            </p>
                            <ul class="list-unstyled mt-3">
                                <li class="mb-2"><i class="fas fa-rocket text-warning me-2"></i> Innover constamment</li>
                                <li class="mb-2"><i class="fas fa-rocket text-warning me-2"></i> Créer de l'impact social</li>
                                <li><i class="fas fa-rocket text-warning me-2"></i> Devenir une référence régionale</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Valeurs Section -->
    <section class="py-5">
        <div class="container">
            <h2 class="text-center mb-5 display-5 fw-bold">Nos Valeurs Fondamentales</h2>
            <div class="row g-4">
                <!-- Valeur 1 -->
                <div class="col-lg-3 col-md-6">
                    <div class="value-card text-center">
                        <div class="value-icon mb-3">
                            <i class="fas fa-lightbulb fa-2x text-primary"></i>
                        </div>
                        <h4 class="fw-bold mb-3">Innovation</h4>
                        <p class="text-muted">Toujours à la recherche de nouvelles solutions pour résoudre les problèmes complexes.</p>
                    </div>
                </div>

                <!-- Valeur 2 -->
                <div class="col-lg-3 col-md-6">
                    <div class="value-card text-center">
                        <div class="value-icon mb-3">
                            <i class="fas fa-book fa-2x text-success"></i>
                        </div>
                        <h4 class="fw-bold mb-3">Éducation</h4>
                        <p class="text-muted">Accès à la formation technologique de qualité pour tous, quel que soit le niveau.</p>
                    </div>
                </div>

                <!-- Valeur 3 -->
                <div class="col-lg-3 col-md-6">
                    <div class="value-card text-center">
                        <div class="value-icon mb-3">
                            <i class="fas fa-heart fa-2x text-danger"></i>
                        </div>
                        <h4 class="fw-bold mb-3">Impact Social</h4>
                        <p class="text-muted">Contribuer positivement à la société et améliorer la qualité de vie dans la communauté.</p>
                    </div>
                </div>

                <!-- Valeur 4 -->
                <div class="col-lg-3 col-md-6">
                    <div class="value-card text-center">
                        <div class="value-icon mb-3">
                            <i class="fas fa-star fa-2x text-warning"></i>
                        </div>
                        <h4 class="fw-bold mb-3">Excellence</h4>
                        <p class="text-muted">Qualité dans tout ce que nous faisons, des services à la formation et au support.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Statistics Section -->
    <section class="py-5 bg-primary text-white">
        <div class="container">
            <div class="row text-center">
                <div class="col-md-3 mb-4 mb-md-0">
                    <h3 class="display-6 fw-bold mb-2">500+</h3>
                    <p class="lead">Apprenants Formés</p>
                </div>
                <div class="col-md-3 mb-4 mb-md-0">
                    <h3 class="display-6 fw-bold mb-2">50+</h3>
                    <p class="lead">Projets Réalisés</p>
                </div>
                <div class="col-md-3 mb-4 mb-md-0">
                    <h3 class="display-6 fw-bold mb-2">10+</h3>
                    <p class="lead">Partenaires</p>
                </div>
                <div class="col-md-3">
                    <h3 class="display-6 fw-bold mb-2">8</h3>
                    <p class="lead">Services Offerts</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Team Section -->
    <section class="py-5 bg-light">
        <div class="container">
            <h2 class="text-center mb-5 display-5 fw-bold">Notre Équipe</h2>
            <div class="row g-4">
                <?php
                // Récupérer l'équipe depuis la base de données
                try {
                    $db = Database::getInstance();
                    $pdo = $db->getConnection();
                    // Vérifier si la table team existe
                    $teamTableCheck = $pdo->query("SHOW TABLES LIKE 'team'");
                    $teamTableExists = $teamTableCheck->rowCount() > 0;

                    if (!$teamTableExists) {
                        // Créer la table de l'équipe si elle n'existe pas
                        $pdo->exec("
                            CREATE TABLE team (
                                id INT AUTO_INCREMENT PRIMARY KEY,
                                name VARCHAR(255) NOT NULL,
                                position VARCHAR(255),
                                bio TEXT,
                                image VARCHAR(255),
                                speciality VARCHAR(255),
                                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                            )
                        ");
                        
                        // Insérer les membres de l'équipe par défaut
                        $pdo->exec("
                            INSERT INTO team (name, position, bio, image, speciality) VALUES
                            ('Fondateur', 'PDG & Co-Fondateur', 'Expert en développement web et mobile avec 10 ans d\'expérience', 'team1.jpg', 'Développement Web & Mobile'),
                            ('CTO', 'Directeur Technique', 'Spécialiste en architecture logicielle et cloud computing', 'team2.jpg', 'Infrastructure Cloud'),
                            ('Formateur', 'Lead Formateur', 'Passionné par l\'enseignement des technologies modernes', 'team3.jpg', 'Formation Tech'),
                            ('Designer', 'Responsable UX/UI', 'Expert en design d\'interfaces utilisateur innovantes', 'team4.jpg', 'Design & UX')
                        ");
                    }

                    $stmtTeam = $pdo->query("
                        SELECT id, name, position, bio, image, speciality 
                        FROM team 
                        ORDER BY created_at ASC
                    ");
                    $teamMembers = $stmtTeam->fetchAll(PDO::FETCH_ASSOC);

                    if (!empty($teamMembers)) {
                        foreach ($teamMembers as $member) {
                            $memberImage = $member['image'] ? 'images/' . $member['image'] : 'images/default-avatar.png';
                ?>
                <div class="col-lg-3 col-md-6">
                    <div class="team-card">
                        <div class="team-image-wrapper">
                            <img src="<?php echo htmlspecialchars($memberImage); ?>" alt="<?php echo htmlspecialchars($member['name']); ?>" class="team-image">
                        </div>
                        <div class="team-info p-3">
                            <h5 class="fw-bold mb-1"><?php echo htmlspecialchars($member['name']); ?></h5>
                            <p class="text-primary fw-500 mb-2"><?php echo htmlspecialchars($member['position']); ?></p>
                            <p class="text-muted small mb-2"><?php echo htmlspecialchars($member['bio']); ?></p>
                            <div class="team-specialty">
                                <span class="badge bg-info"><?php echo htmlspecialchars($member['speciality']); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
                        }
                    } else {
                        echo '<div class="col-12 text-center"><p class="text-muted">Aucun membre d\'équipe disponible.</p></div>';
                    }
                } catch (Exception $e) {
                    echo '<div class="col-12 text-center"><p class="text-danger">Erreur: ' . htmlspecialchars($e->getMessage()) . '</p></div>';
                }
                ?>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-5 bg-gradient text-white text-center">
        <div class="container">
            <h2 class="mb-4 display-5 fw-bold">Rejoignez Notre Communauté</h2>
            <p class="lead mb-4">Devenez acteur du changement technologique au Bénin</p>
            <div class="d-flex gap-3 justify-content-center">
                <a href="formations.php" class="btn btn-light btn-lg px-5 fw-bold">Nos Formations</a>
                <a href="contact.php" class="btn btn-outline-light btn-lg px-5 fw-bold">Nous Contacter</a>
            </div>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>