<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/config/ErrorHandler.php';
require_once __DIR__ . '/models/News.php';

ErrorHandler::init();

$newsModel = new News();

try {
    if (isset($_GET['id'])) {
        $id = (int)$_GET['id'];
        $news = $newsModel->getById($id);
        if ($news) {
            $newsModel->incrementViews($id);
        }
    } else {
        $allNews = $newsModel->getPublished(10);
    }
} catch (Exception $e) {
    $error = $e->getMessage();
}
?>

<?php include 'includes/header.php'; ?>

<div class="container my-5">
    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <?php if (isset($news)): ?>
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <h1><?php echo htmlspecialchars($news['title']); ?></h1>
                <p class="text-muted">Par <?php echo htmlspecialchars($news['author'] ?? 'Équipe'); ?> - <?php echo date('d/m/Y', strtotime($news['created_at'])); ?> - <?php echo intval($news['views']); ?> vue(s)</p>
                <?php if (!empty($news['image'])): ?>
                    <img src="uploads/images/<?php echo htmlspecialchars($news['image']); ?>" class="img-fluid mb-4" alt="">
                <?php endif; ?>
                <p><?php echo nl2br(htmlspecialchars($news['content'] ?? $news['description'])); ?></p>
                <a href="news.php" class="btn btn-secondary mt-3">Retour aux actualités</a>
            </div>
        </div>
    <?php else: ?>
        <div class="row">
            <div class="col-md-10 offset-md-1">
                <h2 class="mb-4">Actualités</h2>

                <?php if (!empty($allNews)): ?>
                    <div class="row">
                        <?php foreach ($allNews as $item): ?>
                            <div class="col-md-6 mb-4">
                                <div class="card h-100">
                                    <?php if (!empty($item['image'])): ?>
                                        <img src="uploads/images/<?php echo htmlspecialchars($item['image']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($item['title']); ?>">
                                    <?php endif; ?>
                                    <div class="card-body">
                                        <h5 class="card-title"><?php echo htmlspecialchars($item['title']); ?></h5>
                                        <p class="card-text text-muted"><?php echo date('d/m/Y', strtotime($item['created_at'])); ?> • <?php echo intval($item['views']); ?> vue(s)</p>
                                        <p class="card-text"><?php echo htmlspecialchars(mb_strimwidth($item['description'], 0, 150, '...')); ?></p>
                                        <a href="news.php?id=<?php echo $item['id']; ?>" class="btn btn-primary">Lire la suite</a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">Aucune actualité publiée pour le moment.</div>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
