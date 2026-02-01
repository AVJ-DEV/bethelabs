<?php
require_once __DIR__ . '/config/Database.php';
require_once __DIR__ . '/config/ErrorHandler.php';
require_once __DIR__ . '/config/Csrf.php';
ErrorHandler::init();
Csrf::init();

// Simple search page: validate CSRF only for POST (modal), allow GET for normal searches
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$token = '';
if ($method === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
}
$query = trim($_POST['q'] ?? $_GET['q'] ?? '');


// Basic HTML header
require_once __DIR__ . '/includes/header.php';
?>
<main class="py-5">
    <div class="container">
    <?php
    if ($method === 'POST' && !Csrf::validateToken($token)) {
        echo '<div class="alert alert-danger">Jeton CSRF invalide. Veuillez réessayer.</div>';
        echo '</div></main>'; include 'includes/footer.php'; exit;
    }

    if (empty($query)) {
        echo '<div class="alert alert-warning">Veuillez saisir un terme pour la recherche.</div>';
        echo '</div></main>'; include 'includes/footer.php'; exit;
    }

    // Run simple search across possible tables
    try {
        $db = Database::getInstance();
        $pdo = $db->getConnection();

        $term = '%' . $query . '%';
        $results = [];

        // Search news table if present
        try {
            $stmt = $pdo->prepare("SELECT id, title, SUBSTRING(content,1,200) AS excerpt, 'news' AS type FROM news WHERE title LIKE :t OR content LIKE :t LIMIT 10");
            $stmt->execute([':t' => $term]);
            $news = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $results = array_merge($results, $news);
        } catch (Exception $e) {
            // table may not exist; swallow
        }

        // Search portfolio table if present
        try {
            $stmt = $pdo->prepare("SELECT id, title, SUBSTRING(description,1,200) AS excerpt, 'portfolio' AS type FROM portfolio WHERE title LIKE :t OR description LIKE :t LIMIT 10");
            $stmt->execute([':t' => $term]);
            $portfolio = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $results = array_merge($results, $portfolio);
        } catch (Exception $e) {
            // table may not exist; swallow
        }

        echo '<h2 class="mb-4">Résultats de la recherche pour: <em>' . htmlspecialchars($query) . '</em></h2>';

        if (empty($results)) {
            echo '<div class="alert alert-info">Aucun résultat trouvé pour ce terme.</div>';
        } else {
            echo '<div class="list-group">';
            foreach ($results as $r) {
                $title = htmlspecialchars($r['title']);
                $excerpt = htmlspecialchars($r['excerpt']);
                $type = htmlspecialchars($r['type']);
                $link = '#';
                if ($type === 'news') $link = 'news.php?id=' . urlencode($r['id']);
                if ($type === 'portfolio') $link = 'portfolio.php?id=' . urlencode($r['id']);

                echo '<a href="' . $link . '" class="list-group-item list-group-item-action">';
                echo '<h5 class="mb-1">' . $title . ' <small class="text-muted">(' . $type . ')</small></h5>';
                echo '<p class="mb-1 small text-muted">' . $excerpt . '...</p>';
                echo '</a>';
            }
            echo '</div>';
        }

    } catch (Exception $e) {
        echo '<div class="alert alert-danger">Erreur lors de la recherche. Veuillez réessayer plus tard.</div>';
    }
    ?>
    </div>
</main>
<?php include 'includes/footer.php'; ?>