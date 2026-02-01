<?php
header('Content-Type: application/json');

require_once __DIR__ . '/config/Database.php';
require_once __DIR__ . '/config/ErrorHandler.php';
require_once __DIR__ . '/models/Testimonial.php';

ErrorHandler::init();

$response = ['success' => false, 'html' => '', 'pagination' => '', 'total' => 0, 'page' => 1, 'totalPages' => 1];

try {
    $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
    $perPage = isset($_GET['per_page']) ? max(1, intval($_GET['per_page'])) : 4;

    $testimonialModel = new Testimonial();
    $total = $testimonialModel->countApproved();
    $totalPages = ($total > 0) ? ceil($total / $perPage) : 1;

    $offset = ($page - 1) * $perPage;
    $testimonials = $testimonialModel->getApprovedPaginated($perPage, $offset);

    ob_start();

    if (!empty($testimonials)) {
        foreach ($testimonials as $testimonial) {
            $shortComment = substr($testimonial['comment'], 0, 150);
            $isLonger = strlen($testimonial['comment']) > 150;
            $defaultImage = 'images/default-avatar.png';
            $image = $testimonial['image'] ? $testimonial['image'] : $defaultImage;
            $stars = str_repeat('★', $testimonial['rating']) . str_repeat('☆', 5 - $testimonial['rating']);
            ?>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card testimonial-card shadow-sm border-0 h-100">
                    <div class="card-body text-center">
                        <img src="<?php echo htmlspecialchars($image); ?>" alt="<?php echo htmlspecialchars($testimonial['name']); ?>" class="testimonial-avatar mb-3">
                        <div class="rating-stars mb-3">
                            <span style="color: #ffc107; font-size: 1.2rem;"><?php echo $stars; ?></span>
                        </div>
                        <p class="card-text text-muted testimonial-comment" id="comment-<?php echo $testimonial['id']; ?>">
                            <?php echo htmlspecialchars($shortComment); ?>
                            <?php if ($isLonger) echo '...'; ?>
                        </p>
                        <p class="card-text text-muted testimonial-full-comment" id="full-comment-<?php echo $testimonial['id']; ?>" style="display: none;">
                            <?php echo htmlspecialchars($testimonial['comment']); ?>
                        </p>
                        <?php if ($isLonger) { ?>
                        <button class="btn btn-sm btn-primary read-more-btn" data-id="<?php echo $testimonial['id']; ?>">
                            Lire plus
                        </button>
                        <?php } ?>
                        <p class="mt-3 mb-0">
                            <strong><?php echo htmlspecialchars($testimonial['name']); ?></strong>
                        </p>
                    </div>
                </div>
            </div>
            <?php
        }
    } else {
        echo '<div class="col-12 text-center"><p class="text-muted">Aucun témoignage disponible pour le moment.</p></div>';
    }

    $html = ob_get_clean();

    // Build pagination HTML (data attributes used for AJAX)
    $pagination = '';
    if ($totalPages > 1) {
        $pagination .= '<nav aria-label="Témoignages pagination"><ul class="pagination justify-content-center">';

        $disabledPrev = $page == 1 ? ' disabled' : '';
        $pagination .= '<li class="page-item' . $disabledPrev . '"><a class="page-link" href="#" data-page="' . max(1, $page - 1) . '">Précédent</a></li>';

        for ($p = 1; $p <= $totalPages; $p++) {
            $active = $p == $page ? ' active' : '';
            $pagination .= '<li class="page-item' . $active . '"><a class="page-link" href="#" data-page="' . $p . '">' . $p . '</a></li>';
        }

        $disabledNext = $page == $totalPages ? ' disabled' : '';
        $pagination .= '<li class="page-item' . $disabledNext . '"><a class="page-link" href="#" data-page="' . min($totalPages, $page + 1) . '">Suivant</a></li>';

        $pagination .= '</ul></nav>';
    }

    // Build data array for client-side rendering
    $dataList = [];
    foreach ($testimonials as $t) {
        $dataList[] = [
            'id' => (int)$t['id'],
            'name' => $t['name'],
            'comment' => $t['comment'],
            'shortComment' => mb_strimwidth($t['comment'], 0, 150, '...'),
            'rating' => (int)$t['rating'],
            'image' => $t['image'] ? $t['image'] : 'images/default-avatar.png',
            'created_at' => $t['created_at'] ?? ''
        ];
    }

    $response = [
        'success' => true,
        'html' => $html,
        'data' => $dataList,
        'pagination' => $pagination,
        'total' => $total,
        'page' => $page,
        'totalPages' => $totalPages
    ];

} catch (Exception $e) {
    $response['success'] = false;
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
exit();
?>