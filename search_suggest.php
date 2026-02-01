<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/config/Database.php';
require_once __DIR__ . '/config/ErrorHandler.php';

ErrorHandler::init();

$q = trim($_GET['q'] ?? '');
if ($q === '') {
    echo json_encode([]);
    exit;
}

try {
    $db = Database::getInstance()->getConnection();
    $term = '%' . $q . '%';
    $results = [];

    // News
    try {
        $stmt = $db->prepare("SELECT id, title, SUBSTRING(content,1,200) AS excerpt, 'news' AS type
                              FROM news WHERE title LIKE :t OR content LIKE :t LIMIT 5");
        $stmt->execute([':t' => $term]);
        $news = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($news as $n) {
            $results[] = ['label' => $n['title'], 'type' => 'news', 'id' => $n['id'], 'excerpt' => $n['excerpt']];
        }
    } catch (Exception $e) {
        // ignore
    }

    // Portfolio
    try {
        $stmt = $db->prepare("SELECT id, title, SUBSTRING(description,1,200) AS excerpt, 'portfolio' AS type
                              FROM portfolio WHERE title LIKE :t OR description LIKE :t LIMIT 5");
        $stmt->execute([':t' => $term]);
        $pf = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($pf as $p) {
            $results[] = ['label' => $p['title'], 'type' => 'portfolio', 'id' => $p['id'], 'excerpt' => $p['excerpt']];
        }
    } catch (Exception $e) {
        // ignore
    }

    // Trim to 6 suggestions
    $results = array_slice($results, 0, 6);
    echo json_encode($results);
} catch (Exception $e) {
    ErrorHandler::logError($e);
    echo json_encode([]);
}
