<?php 
require_once __DIR__ . '/config/Database.php';
require_once __DIR__ . '/config/ErrorHandler.php';
require_once __DIR__ . '/includes/header.php'; 
?>

<main>
    <!-- Hero Section -->
    <section class="concours-hero text-white py-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1 class="display-4 fw-bold mb-4">Concours Informatiques</h1>
                    <p class="lead mb-4">Montrez vos talents ! Participez à nos concours technologiques et remportez des prix exceptionnels. Ouverts à tous les niveaux du primaire à l'université.</p>
                    <a href="inscription.php" class="btn btn-light btn-lg px-5 me-3 fw-bold">Participer Maintenant</a>
                    <a href="#concours-list" class="btn btn-outline-light btn-lg px-5 fw-bold">Voir les Concours</a>
                </div>
                <div class="col-lg-6">
                    <img src="images/concours-hero.jpg" alt="Concours" class="img-fluid rounded shadow-lg">
                </div>
            </div>
        </div>
    </section>

    <!-- Concours en Cours Section -->
    <section class="py-5" id="concours-list">
        <div class="container">
            <h2 class="text-center mb-5 display-5 fw-bold">Concours en Cours</h2>
            <div class="row g-4">
                <!-- Concours 1 -->
                <div class="col-lg-4 col-md-6">
                    <div class="concours-card">
                        <div class="concours-badge">ACTUELLEMENT OUVERT</div>
                        <div class="concours-icon">
                            <i class="fas fa-laptop-code"></i>
                        </div>
                        <h3 class="concours-title">Concours National de Programmation</h3>
                        <div class="concours-info">
                            <p class="mb-2"><strong>Thème:</strong> Solutions technologiques pour l'agriculture durable</p>
                            <p class="mb-2"><strong>Catégorie:</strong> Lycée & Universitaire</p>
                            <p class="mb-3"><strong>Récompense:</strong> <span class="badge bg-warning text-dark">500,000 CFA</span></p>
                        </div>
                        <div class="concours-details mb-3">
                            <div class="detail-item">
                                <i class="fas fa-calendar-alt text-primary me-2"></i>
                                <span><strong>Date limite:</strong> 31 mars 2026</span>
                            </div>
                            <div class="detail-item">
                                <i class="fas fa-users text-success me-2"></i>
                                <span><strong>Format:</strong> Équipe de 2-4 personnes</span>
                            </div>
                            <div class="detail-item">
                                <i class="fas fa-gift text-danger me-2"></i>
                                <span><strong>Prix:</strong> 1er: 500k, 2e: 300k, 3e: 150k</span>
                            </div>
                        </div>
                        <a href="inscription.php" class="btn btn-primary btn-sm w-100 fw-bold">Candidater</a>
                    </div>
                </div>

                <!-- Concours 2 -->
                <div class="col-lg-4 col-md-6">
                    <div class="concours-card">
                        <div class="concours-badge">ACTUELLEMENT OUVERT</div>
                        <div class="concours-icon">
                            <i class="fas fa-palette"></i>
                        </div>
                        <h3 class="concours-title">Concours de Web Design</h3>
                        <div class="concours-info">
                            <p class="mb-2"><strong>Thème:</strong> Plateforme e-commerce innovante</p>
                            <p class="mb-2"><strong>Catégorie:</strong> Collège & Lycée</p>
                            <p class="mb-3"><strong>Récompense:</strong> <span class="badge bg-warning text-dark">250,000 CFA</span></p>
                        </div>
                        <div class="concours-details mb-3">
                            <div class="detail-item">
                                <i class="fas fa-calendar-alt text-primary me-2"></i>
                                <span><strong>Date limite:</strong> 15 avril 2026</span>
                            </div>
                            <div class="detail-item">
                                <i class="fas fa-users text-success me-2"></i>
                                <span><strong>Format:</strong> Individuel ou Duo</span>
                            </div>
                            <div class="detail-item">
                                <i class="fas fa-gift text-danger me-2"></i>
                                <span><strong>Prix:</strong> 1er: 250k, 2e: 150k, 3e: 75k</span>
                            </div>
                        </div>
                        <a href="inscription.php" class="btn btn-primary btn-sm w-100 fw-bold">Candidater</a>
                    </div>
                </div>

                <!-- Concours 3 -->
                <div class="col-lg-4 col-md-6">
                    <div class="concours-card">
                        <div class="concours-badge">PROCHAINEMENT</div>
                        <div class="concours-icon">
                            <i class="fas fa-robot"></i>
                        </div>
                        <h3 class="concours-title">Hackathon Innovation</h3>
                        <div class="concours-info">
                            <p class="mb-2"><strong>Thème:</strong> Solutions IA pour l'éducation</p>
                            <p class="mb-2"><strong>Catégorie:</strong> Universitaire</p>
                            <p class="mb-3"><strong>Récompense:</strong> <span class="badge bg-warning text-dark">1,000,000 CFA</span></p>
                        </div>
                        <div class="concours-details mb-3">
                            <div class="detail-item">
                                <i class="fas fa-calendar-alt text-primary me-2"></i>
                                <span><strong>Date:</strong> 20-22 mai 2026</span>
                            </div>
                            <div class="detail-item">
                                <i class="fas fa-users text-success me-2"></i>
                                <span><strong>Format:</strong> Équipe de 3-5 personnes</span>
                            </div>
                            <div class="detail-item">
                                <i class="fas fa-gift text-danger me-2"></i>
                                <span><strong>Prix:</strong> 1er: 1M, 2e: 600k, 3e: 400k</span>
                            </div>
                        </div>
                        <button class="btn btn-secondary btn-sm w-100 fw-bold" disabled>Bientôt disponible</button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Types de Concours Section -->
    <section class="py-5 bg-light">
        <div class="container">
            <h2 class="text-center mb-5 display-5 fw-bold">Types de Concours</h2>
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="concours-type-card">
                        <div class="type-icon">
                            <i class="fas fa-code fa-3x text-primary"></i>
                        </div>
                        <h4 class="fw-bold mb-3">Concours de Programmation</h4>
                        <p class="mb-3">Développez une solution complète pour résoudre un problème réel. Mettez en avant votre créativité et vos compétences de développeur.</p>
                        <ul class="list-unstyled">
                            <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i>Problème défini</li>
                            <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i>Durée: Quelques semaines</li>
                            <li><i class="fas fa-check-circle text-success me-2"></i>Évaluation: Code + Présentation</li>
                        </ul>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="concours-type-card">
                        <div class="type-icon">
                            <i class="fas fa-palette fa-3x text-success"></i>
                        </div>
                        <h4 class="fw-bold mb-3">Concours de Design</h4>
                        <p class="mb-3">Créez l'interface la plus innovante et conviviale. Montrez votre vision UX/UI avec un design exceptionnel.</p>
                        <ul class="list-unstyled">
                            <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i>Thème fourni</li>
                            <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i>Durée: Quelques semaines</li>
                            <li><i class="fas fa-check-circle text-success me-2"></i>Évaluation: Design + Ergonomie</li>
                        </ul>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="concours-type-card">
                        <div class="type-icon">
                            <i class="fas fa-rocket fa-3x text-warning"></i>
                        </div>
                        <h4 class="fw-bold mb-3">Hackathons</h4>
                        <p class="mb-3">Développez une solution en 24-48 heures. Une expérience intensive avec autres développeurs passionnés.</p>
                        <ul class="list-unstyled">
                            <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i>Événement en direct</li>
                            <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i>Durée: 24-48 heures</li>
                            <li><i class="fas fa-check-circle text-success me-2"></i>Évaluation: Prototype final</li>
                        </ul>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="concours-type-card">
                        <div class="type-icon">
                            <i class="fas fa-shield-alt fa-3x text-danger"></i>
                        </div>
                        <h4 class="fw-bold mb-3">Défis Cybersécurité</h4>
                        <p class="mb-3">Testez vos connaissances en sécurité informatique. Trouvez les vulnérabilités et démontrez votre expertise.</p>
                        <ul class="list-unstyled">
                            <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i>Défis techniques</li>
                            <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i>Durée: Flexible</li>
                            <li><i class="fas fa-check-circle text-success me-2"></i>Évaluation: Résolutions</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Winners Section -->
    <section class="py-5">
        <div class="container">
            <h2 class="text-center mb-5 display-5 fw-bold">Nos Talents Gagnants</h2>
            <div class="row g-4">
                <?php
                // Récupérer les gagnants depuis la base de données
                try {
                    $db = Database::getInstance();
                    $pdo = $db->getConnection();
                    // Vérifier si la table winners existe
                    $winnersTableCheck = $pdo->query("SHOW TABLES LIKE 'winners'");
                    $winnersTableExists = $winnersTableCheck->rowCount() > 0;

                    if (!$winnersTableExists) {
                        // Créer la table des gagnants si elle n'existe pas
                        $pdo->exec("
                            CREATE TABLE winners (
                                id INT AUTO_INCREMENT PRIMARY KEY,
                                name VARCHAR(255) NOT NULL,
                                position INT,
                                concours_name VARCHAR(255),
                                achievement TEXT,
                                image VARCHAR(255),
                                prize VARCHAR(255),
                                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                            )
                        ");
                        
                        // Insérer quelques gagnants par défaut
                        $pdo->exec("
                            INSERT INTO winners (name, position, concours_name, achievement, image, prize) VALUES
                            ('Marie K.', 1, 'Programmation 2025', '1ère place - App de suivi agricole innovante', 'winner1.jpg', '500,000 CFA'),
                            ('Alex B.', 2, 'Web Design 2025', '2e place - Design e-commerce exceptionnel', 'winner2.jpg', '300,000 CFA'),
                            ('Sophia L.', 3, 'Hackathon 2025', '3e place - Solution IA pour l\'éducation', 'winner3.jpg', '150,000 CFA'),
                            ('Jean P.', 1, 'Cybersécurité 2025', '1ère place - Découverte de 5 vulnérabilités critiques', 'winner4.jpg', '400,000 CFA')
                        ");
                    }

                    $stmtWinners = $pdo->query("
                        SELECT id, name, position, concours_name, achievement, image, prize 
                        FROM winners 
                        ORDER BY position ASC, created_at DESC
                        LIMIT 4
                    ");
                    $winners = $stmtWinners->fetchAll(PDO::FETCH_ASSOC);

                    if (!empty($winners)) {
                        foreach ($winners as $winner) {
                            $winnerImage = $winner['image'] ? 'images/' . $winner['image'] : 'images/default-avatar.png';
                            $medalColor = $winner['position'] == 1 ? '#FFD700' : ($winner['position'] == 2 ? '#C0C0C0' : '#CD7F32');
                            $medalIcon = $winner['position'] == 1 ? '🥇' : ($winner['position'] == 2 ? '🥈' : '🥉');
                ?>
                <div class="col-lg-3 col-md-6">
                    <div class="winner-card">
                        <div class="winner-medal" style="background-color: <?php echo $medalColor; ?>;">
                            <span><?php echo $medalIcon; ?></span>
                        </div>
                        <img src="<?php echo htmlspecialchars($winnerImage); ?>" alt="<?php echo htmlspecialchars($winner['name']); ?>" class="winner-image">
                        <div class="winner-info p-3">
                            <h5 class="fw-bold mb-1"><?php echo htmlspecialchars($winner['name']); ?></h5>
                            <p class="text-muted small mb-2">Position: <span class="badge bg-primary"><?php echo $winner['position']; ?></span></p>
                            <p class="winner-achievement text-muted small"><?php echo htmlspecialchars($winner['achievement']); ?></p>
                            <p class="mt-2 mb-0"><span class="badge bg-success"><?php echo htmlspecialchars($winner['prize']); ?></span></p>
                        </div>
                    </div>
                </div>
                <?php
                        }
                    } else {
                        echo '<div class="col-12 text-center"><p class="text-muted">Aucun gagnant pour le moment.</p></div>';
                    }
                } catch (Exception $e) {
                    echo '<div class="col-12 text-center"><p class="text-danger">Erreur: ' . htmlspecialchars($e->getMessage()) . '</p></div>';
                }
                ?>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-5 text-white text-center" style="background: linear-gradient(135deg, #FFC107 0%, #FF9800 100%);">
        <div class="container">
            <h2 class="mb-4 display-5 fw-bold">Prêt à Relever le Défi ?</h2>
            <p class="lead mb-4">Participer à nos concours et montrer votre talent technologique au Bénin et à l'Afrique.</p>
            <a href="inscription.php" class="btn btn-light btn-lg px-5 fw-bold me-3">Participer Maintenant</a>
            <a href="contact.php" class="btn btn-outline-light btn-lg px-5 fw-bold">Plus d'Informations</a>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>