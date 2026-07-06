<?php

declare(strict_types=1);

$pageTitle = 'Beranda - Kuliner Lombok';

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';

$featuredQuery = "
    SELECT r.*, COALESCE(AVG(rv.rating), 0) AS avg_rating, COUNT(rv.id) AS total_reviews
    FROM restaurants r
    LEFT JOIN reviews rv ON rv.restaurant_id = r.id AND rv.status = 'approved'
    WHERE r.status = 'approved'
    GROUP BY r.id
    ORDER BY avg_rating DESC, r.created_at DESC
    LIMIT 6
";
$featuredResult = mysqli_query($conn, $featuredQuery);

$latestQuery = "
    SELECT r.*, COALESCE(AVG(rv.rating), 0) AS avg_rating, COUNT(rv.id) AS total_reviews
    FROM restaurants r
    LEFT JOIN reviews rv ON rv.restaurant_id = r.id AND rv.status = 'approved'
    WHERE r.status = 'approved'
    GROUP BY r.id
    ORDER BY r.created_at DESC
    LIMIT 6
";
$latestResult = mysqli_query($conn, $latestQuery);
?>

<main>
    <section class="hero-section text-white py-5">
        <div class="hero-overlay"></div>
        <div class="container py-5 position-relative hero-content">
            <div class="row justify-content-center text-center">
                <div class="col-lg-8">
                    <h1 class="display-4 fw-bold mb-4">Jelajahi Kuliner Bersejarah di Pulau Lombok</h1>
                    <p class="lead mb-5 text-light opacity-75">Direktori restoran bernilai budaya, cerita sejarah, menu lokal, dan pengalaman wisata kuliner autentik.</p>
                    
                    <form action="<?= e(baseUrl('restaurants.php')) ?>" method="get" class="glass-panel p-3 mx-auto" style="max-width: 800px;">
                        <div class="row g-2">
                            <div class="col-md-9">
                                <input type="text" name="q" class="form-control form-control-lg border-0 shadow-none bg-white" placeholder="Cari restoran atau makanan lokal...">
                            </div>
                            <div class="col-md-3 d-grid">
                                <button type="submit" class="btn btn-success btn-lg">Cari Sekarang</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <section class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="h4 mb-0">Restoran Unggulan</h2>
            <a href="<?= e(baseUrl('restaurants.php')) ?>" class="btn btn-outline-success btn-sm">Lihat Semua</a>
        </div>
        <div class="row g-4">
            <?php while ($row = mysqli_fetch_assoc($featuredResult)): ?>
                <div class="col-md-6 col-lg-4">
                    <a href="<?= e(baseUrl('restaurant_detail.php?id=' . (int)$row['id'])) ?>" class="text-decoration-none">
                        <div class="card h-100">
                            <div class="position-relative">
                                <img src="<?= e(restaurantImageUrl($row['main_image'] ?? null)) ?>" class="card-img-top object-fit-cover" style="height:240px" alt="<?= e($row['name']) ?>">
                                <?php if(!empty($row['category'])): ?>
                                    <span class="position-absolute top-0 start-0 m-3 badge bg-success shadow-sm"><?= e($row['category']) ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="card-body">
                                <h3 class="h5 mb-1 text-dark"><?= e($row['name']) ?></h3>
                                <p class="small text-muted mb-3">
                                    <i class="bi bi-geo-alt-fill me-1"></i><?= e($row['district'] ?? '-') ?>
                                </p>
                                <p class="small text-secondary mb-3"><?= e(mb_substr((string)($row['description'] ?? ''), 0, 100)) ?>...</p>
                                <div class="d-flex justify-content-between align-items-center border-top pt-3">
                                    <div class="small text-warning fw-bold">
                                        <i class="bi bi-star-fill me-1"></i><?= number_format((float)$row['avg_rating'], 1) ?>
                                    </div>
                                    <div class="small text-muted"><?= (int)$row['total_reviews'] ?> ulasan</div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endwhile; ?>
        </div>
    </section>

    <section class="container pb-5">
        <h2 class="h4 mb-3">Restoran Terbaru</h2>
        <div class="row g-4">
            <?php while ($row = mysqli_fetch_assoc($latestResult)): ?>
                <div class="col-md-6 col-lg-4">
                    <a href="<?= e(baseUrl('restaurant_detail.php?id=' . (int)$row['id'])) ?>" class="text-decoration-none">
                        <div class="card h-100">
                            <div class="position-relative">
                                <img src="<?= e(restaurantImageUrl($row['main_image'] ?? null)) ?>" class="card-img-top object-fit-cover" style="height:240px" alt="<?= e($row['name']) ?>">
                            </div>
                            <div class="card-body">
                                <h3 class="h5 mb-1 text-dark"><?= e($row['name']) ?></h3>
                                <p class="small text-muted mb-0">
                                    <i class="bi bi-geo-alt-fill me-1"></i><?= e($row['district'] ?? '-') ?>
                                </p>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endwhile; ?>
        </div>
    </section>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
