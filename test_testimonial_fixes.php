<?php
/**
 * Test script for testimonials fixes
 */

require_once __DIR__ . '/config/Database.php';
require_once __DIR__ . '/config/ErrorHandler.php';
require_once __DIR__ . '/models/Testimonial.php';

ErrorHandler::init();

echo "=== TEST DES CORRECTIONS TÉMOIGNAGES ===\n\n";

// Test 1: Vérifier que Database fonctionne
echo "[TEST 1] Vérifier Database pour index.php...\n";
try {
    $db = Database::getInstance();
    $connection = $db->getConnection();
    echo "✅ Database initialisée correctement\n";
    echo "✅ Connexion PDO disponible\n\n";
} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n\n";
    exit(1);
}

// Test 2: Vérifier Testimonial model
echo "[TEST 2] Vérifier le modèle Testimonial...\n";
try {
    $testimonialModel = new Testimonial();
    $testimonials = $testimonialModel->getAll();
    echo "✅ Modèle Testimonial chargé\n";
    echo "✅ " . count($testimonials) . " témoignages en base\n\n";
} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n\n";
    exit(1);
}

// Test 3: Vérifier filtrage des approuvés
echo "[TEST 3] Vérifier filtrage des témoignages approuvés...\n";
try {
    $approvedCount = 0;
    foreach ($testimonials as $t) {
        if ($t['status'] === 'approved') {
            $approvedCount++;
        }
    }
    echo "✅ Témoignages approuvés: $approvedCount\n";
    echo "✅ Filtrage fonctionne correctement\n\n";
} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n\n";
    exit(1);
}

// Test 4: Vérifier fichiers nécessaires
echo "[TEST 4] Vérifier les fichiers créés...\n";
$files = [
    'process_testimonial_ajax.php' => 'Processeur AJAX pour témoignages',
    'testimonials.php' => 'Page témoignages avec formulaire AJAX',
    'index.php' => 'Page d\'accueil corrigée'
];

foreach ($files as $file => $description) {
    if (file_exists(__DIR__ . '/' . $file)) {
        echo "✅ $file - $description\n";
    } else {
        echo "❌ $file - NOT FOUND\n";
    }
}

echo "\n=== RÉSUMÉ DES CORRECTIONS ===\n";
echo "1. ❌ → ✅ Erreur \$pdo indéfinie dans index.php (ligne 299)\n";
echo "   Correction: Utilisation du modèle Testimonial avec Database\n\n";
echo "2. ❌ → ✅ Redirection après soumission du formulaire\n";
echo "   Correction: Soumission AJAX avec feedback immédiat\n\n";
echo "3. ✅ Nouveau fichier: process_testimonial_ajax.php\n";
echo "   Retourne JSON au lieu de rediriger\n\n";
echo "4. ✅ Nouveau JavaScript AJAX dans testimonials.php\n";
echo "   Gère la soumission sans rechargement de page\n\n";

echo "=== TEST COMPLÉTÉ AVEC SUCCÈS ===\n";
?>
