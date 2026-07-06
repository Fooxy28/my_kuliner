<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/helpers.php';

requireRole('restaurant_admin');
$pageTitle = 'Dashboard Admin Restoran';

$user = currentUser();
$userId = (int)($user['id'] ?? 0);

$restaurantStmt = mysqli_prepare($conn, 'SELECT * FROM restaurants WHERE user_id = ? ORDER BY id ASC LIMIT 1');
mysqli_stmt_bind_param($restaurantStmt, 'i', $userId);
mysqli_stmt_execute($restaurantStmt);
$restaurant = mysqli_stmt_get_result($restaurantStmt)->fetch_assoc();

$stats = ['menus' => 0, 'gallery' => 0, 'reviews' => 0];
if ($restaurant) {
    $restaurantId = (int)$restaurant['id'];
    $stats['menus'] = (int)(mysqli_query($conn, "SELECT COUNT(*) total FROM menus WHERE restaurant_id = $restaurantId")->fetch_assoc()['total'] ?? 0);
    $stats['gallery'] = (int)(mysqli_query($conn, "SELECT COUNT(*) total FROM gallery WHERE restaurant_id = $restaurantId")->fetch_assoc()['total'] ?? 0);
    $stats['reviews'] = (int)(mysqli_query($conn, "SELECT COUNT(*) total FROM reviews WHERE restaurant_id = $restaurantId")->fetch_assoc()['total'] ?? 0);
}

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>
<main class="container py-4">
    <div class="row g-4">
        <div class="col-lg-3"><?php require __DIR__ . '/../includes/sidebar.php'; ?></div>
        <div class="col-lg-9">
            <h1 class="h4 mb-3">Dashboard Admin Restoran</h1>
            <?php if (!$restaurant): ?>
                <div class="alert alert-warning">Akun Anda belum memiliki data restoran. Hubungi admin sistem.</div>
            <?php else: ?>
                <p class="text-muted">Restoran: <strong><?= e($restaurant['name']) ?></strong> (Status: <?= e($restaurant['status']) ?>)</p>
                <div class="row g-3">
                    <div class="col-md-4"><div class="card card-body shadow-sm border-0"><small>Total Menu</small><h2 class="h4 mb-0"><?= $stats['menus'] ?></h2></div></div>
                    <div class="col-md-4"><div class="card card-body shadow-sm border-0"><small>Total Galeri</small><h2 class="h4 mb-0"><?= $stats['gallery'] ?></h2></div></div>
                    <div class="col-md-4"><div class="card card-body shadow-sm border-0"><small>Total Review</small><h2 class="h4 mb-0"><?= $stats['reviews'] ?></h2></div></div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</main>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
