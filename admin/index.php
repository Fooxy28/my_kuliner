<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/helpers.php';

requireRole('admin');
$pageTitle = 'Dashboard Admin';

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';

$stats = [
    'users' => 0,
    'restaurants' => 0,
    'reviews' => 0,
    'menus' => 0,
];

$stats['users'] = (int)(mysqli_query($conn, 'SELECT COUNT(*) AS total FROM users')->fetch_assoc()['total'] ?? 0);
$stats['restaurants'] = (int)(mysqli_query($conn, 'SELECT COUNT(*) AS total FROM restaurants')->fetch_assoc()['total'] ?? 0);
$stats['reviews'] = (int)(mysqli_query($conn, 'SELECT COUNT(*) AS total FROM reviews')->fetch_assoc()['total'] ?? 0);
$stats['menus'] = (int)(mysqli_query($conn, 'SELECT COUNT(*) AS total FROM menus')->fetch_assoc()['total'] ?? 0);
?>
<main class="container py-4">
    <div class="row g-4">
        <div class="col-lg-3">
            <?php require __DIR__ . '/../includes/sidebar.php'; ?>
        </div>
        <div class="col-lg-9">
            <h1 class="h3 mb-3">Dashboard Admin</h1>
            <div class="row g-3">
                <div class="col-md-6 col-xl-3">
                    <div class="card card-body border-0 shadow-sm">
                        <small>Total Users</small>
                        <h2 class="h4 mb-0"><?= $stats['users'] ?></h2>
                    </div>
                </div>
                <div class="col-md-6 col-xl-3">
                    <div class="card card-body border-0 shadow-sm">
                        <small>Total Restoran</small>
                        <h2 class="h4 mb-0"><?= $stats['restaurants'] ?></h2>
                    </div>
                </div>
                <div class="col-md-6 col-xl-3">
                    <div class="card card-body border-0 shadow-sm">
                        <small>Total Review</small>
                        <h2 class="h4 mb-0"><?= $stats['reviews'] ?></h2>
                    </div>
                </div>
                <div class="col-md-6 col-xl-3">
                    <div class="card card-body border-0 shadow-sm">
                        <small>Total Menu</small>
                        <h2 class="h4 mb-0"><?= $stats['menus'] ?></h2>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
