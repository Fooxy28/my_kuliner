<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/helpers.php';

requireRole('user');
$pageTitle = 'Ulasan Saya';

$user = currentUser();
$userId = (int)($user['id'] ?? 0);

$stmt = mysqli_prepare($conn, "
    SELECT rv.*, r.name AS restaurant_name
    FROM reviews rv
    INNER JOIN restaurants r ON r.id = rv.restaurant_id
    WHERE rv.user_id = ?
    ORDER BY rv.created_at DESC
");
mysqli_stmt_bind_param($stmt, 'i', $userId);
mysqli_stmt_execute($stmt);
$reviews = mysqli_stmt_get_result($stmt);

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>
<main class="container py-4" style="max-width: 860px;">
    <h1 class="h4 mb-3">Riwayat Ulasan Saya</h1>
    <?php while ($rv = mysqli_fetch_assoc($reviews)): ?>
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body">
                <div class="d-flex justify-content-between mb-1">
                    <strong><?= e($rv['restaurant_name']) ?></strong>
                    <span class="badge text-bg-secondary"><?= e($rv['status']) ?></span>
                </div>
                <div class="small text-warning mb-1">Rating: <?= (int)$rv['rating'] ?>/5</div>
                <p class="mb-1"><?= e($rv['comment'] ?? '-') ?></p>
                <small class="text-muted"><?= e($rv['created_at']) ?></small>
            </div>
        </div>
    <?php endwhile; ?>
</main>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
