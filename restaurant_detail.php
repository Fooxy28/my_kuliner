<?php

declare(strict_types=1);

$pageTitle = 'Detail Restoran';

require_once __DIR__ . '/config/database.php';

$restaurantId = (int)($_GET['id'] ?? 0);
if ($restaurantId <= 0) {
    http_response_code(404);
    die('Restoran tidak ditemukan.');
}

$stmt = mysqli_prepare($conn, "
    SELECT r.*, COALESCE(AVG(rv.rating),0) AS avg_rating, COUNT(rv.id) AS total_reviews
    FROM restaurants r
    LEFT JOIN reviews rv ON rv.restaurant_id = r.id AND rv.status = 'approved'
    WHERE r.id = ? AND r.status = 'approved'
    GROUP BY r.id
");
mysqli_stmt_bind_param($stmt, 'i', $restaurantId);
mysqli_stmt_execute($stmt);
$restaurant = mysqli_stmt_get_result($stmt)->fetch_assoc();

if (!$restaurant) {
    http_response_code(404);
    die('Restoran tidak ditemukan.');
}

$pageTitle = $restaurant['name'] . ' - Detail Restoran';

$menuStmt = mysqli_prepare($conn, "SELECT * FROM menus WHERE restaurant_id = ? ORDER BY created_at DESC");
mysqli_stmt_bind_param($menuStmt, 'i', $restaurantId);
mysqli_stmt_execute($menuStmt);
$menus = mysqli_stmt_get_result($menuStmt);

$galleryStmt = mysqli_prepare($conn, "SELECT * FROM gallery WHERE restaurant_id = ? ORDER BY created_at DESC");
mysqli_stmt_bind_param($galleryStmt, 'i', $restaurantId);
mysqli_stmt_execute($galleryStmt);
$galleries = mysqli_stmt_get_result($galleryStmt);

$reviewStmt = mysqli_prepare($conn, "
    SELECT rv.*, u.username, u.fullname
    FROM reviews rv
    INNER JOIN users u ON u.id = rv.user_id
    WHERE rv.restaurant_id = ? AND rv.status = 'approved'
    ORDER BY rv.created_at DESC
");
mysqli_stmt_bind_param($reviewStmt, 'i', $restaurantId);
mysqli_stmt_execute($reviewStmt);
$reviews = mysqli_stmt_get_result($reviewStmt);

$mapsEmbed = trim((string)($restaurant['maps_embed'] ?? ''));
if (empty($mapsEmbed)) {
    $mapsQuery = urlencode((string)($restaurant['address'] ?? ''));
}

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<main class="container py-4">
    <div class="row g-4">
        <div class="col-lg-8">
            <img src="<?= e(restaurantImageUrl($restaurant['main_image'] ?? null)) ?>" class="img-fluid w-100 mb-4 detail-hero-img object-fit-cover" style="max-height: 480px;" alt="<?= e($restaurant['name']) ?>">
            
            <div class="d-flex justify-content-between align-items-start mb-2">
                <div>
                    <h1 class="h3 mb-1"><?= e($restaurant['name']) ?></h1>
                    <?php if (!empty($restaurant['category'])): ?>
                        <span class="badge bg-success mb-2"><?= e($restaurant['category']) ?></span>
                    <?php endif; ?>
                    <p class="text-muted mb-2"><?= e(($restaurant['district'] ?? '-') . ', ' . ($restaurant['village'] ?? '-')) ?></p>
                    <div class="mb-2 text-warning">Rating <?= number_format((float)$restaurant['avg_rating'], 1) ?>/5 (<?= (int)$restaurant['total_reviews'] ?> ulasan)</div>
                </div>
                
                <div class="d-flex gap-2">
                    <a href="https://api.whatsapp.com/send?text=<?= urlencode('Cek restoran wisata ini: ' . e($restaurant['name']) . ' - ' . baseUrl('restaurant_detail.php?id='.$restaurantId)) ?>" target="_blank" class="btn btn-sm btn-outline-success">Bagikan ke WA</a>
                    <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode(baseUrl('restaurant_detail.php?id='.$restaurantId)) ?>" target="_blank" class="btn btn-sm btn-outline-primary">Facebook</a>
                    <a href="https://twitter.com/intent/tweet?url=<?= urlencode(baseUrl('restaurant_detail.php?id='.$restaurantId)) ?>&text=<?= urlencode('Cek restoran wisata ini: ' . e($restaurant['name'])) ?>" target="_blank" class="btn btn-sm btn-outline-info">Twitter</a>
                </div>
            </div>

            <?php if (!empty($restaurant['facilities'])): ?>
            <div class="mb-4">
                <?php 
                $facs = array_map('trim', explode(',', $restaurant['facilities']));
                foreach($facs as $fac): 
                    if($fac === '') continue;
                ?>
                    <span class="badge bg-light text-dark border me-1 mb-1"><?= e($fac) ?></span>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <section class="mt-4">
                <h2 class="h5">Sejarah Restoran</h2>
                <p><?= nl2br(e($restaurant['history'] ?? '-')) ?></p>
            </section>

            <section class="mt-4">
                <h2 class="h5">Deskripsi</h2>
                <p><?= nl2br(e($restaurant['description'] ?? '-')) ?></p>
            </section>

            <section class="mt-5">
                <h2 class="h4 mb-4">Daftar Menu</h2>
                <div class="row g-3">
                    <?php while ($menu = mysqli_fetch_assoc($menus)): ?>
                        <div class="col-md-6">
                            <div class="card h-100 border-0 shadow-sm d-flex flex-row p-3 gap-3 align-items-center">
                                <?php if (!empty($menu['image'])): ?>
                                    <img src="<?= e(baseUrl('uploads/menus/' . $menu['image'])) ?>" alt="<?= e($menu['name']) ?>" class="rounded shadow-sm" style="width: 90px; height: 90px; object-fit: cover;">
                                <?php else: ?>
                                    <div class="bg-light rounded shadow-sm d-flex align-items-center justify-content-center text-muted flex-shrink-0" style="width: 90px; height: 90px;">
                                        <i class="bi bi-image fs-3"></i>
                                    </div>
                                <?php endif; ?>
                                <div class="flex-grow-1">
                                    <h3 class="h6 mb-1 text-dark fw-bold"><?= e($menu['name']) ?></h3>
                                    <p class="small text-muted mb-2 line-clamp-2"><?= e($menu['description'] ?? '-') ?></p>
                                    <strong class="text-success fs-5">Rp <?= number_format((float)$menu['price'], 0, ',', '.') ?></strong>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            </section>

            <section class="mt-4">
                <h2 class="h5">Galeri</h2>
                <div class="row g-3">
                    <?php while ($gallery = mysqli_fetch_assoc($galleries)): ?>
                        <div class="col-6 col-md-4">
                            <img src="<?= e(baseUrl('uploads/gallery/' . $gallery['image'])) ?>" class="img-fluid rounded" alt="<?= e($gallery['caption'] ?? 'Galeri') ?>">
                        </div>
                    <?php endwhile; ?>
                </div>
            </section>

            <section class="mt-4">
                <h2 class="h5">Ulasan Pengunjung</h2>
                <?php if (isLoggedIn() && hasRole('user')): ?>
                    <form method="post" action="<?= e(baseUrl('actions/submit_review.php')) ?>" class="border rounded p-3 mb-3">
                        <input type="hidden" name="restaurant_id" value="<?= (int)$restaurant['id'] ?>">
                        <div class="mb-2">
                            <label class="form-label">Rating</label>
                            <select name="rating" class="form-select" required>
                                <option value="">Pilih rating</option>
                                <option value="5">5 - Sangat Baik</option>
                                <option value="4">4 - Baik</option>
                                <option value="3">3 - Cukup</option>
                                <option value="2">2 - Kurang</option>
                                <option value="1">1 - Buruk</option>
                            </select>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Komentar</label>
                            <textarea name="comment" class="form-control" rows="3" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-success btn-sm">Kirim Ulasan</button>
                        <small class="d-block text-muted mt-2">Ulasan Anda akan ditinjau admin terlebih dahulu.</small>
                    </form>
                <?php endif; ?>

                <?php while ($review = mysqli_fetch_assoc($reviews)): ?>
                    <div class="border rounded p-3 mb-2">
                        <div class="d-flex justify-content-between">
                            <strong><?= e($review['fullname'] ?: $review['username']) ?></strong>
                            <span class="text-warning">★ <?= (int)$review['rating'] ?></span>
                        </div>
                        <p class="mb-1 small"><?= e($review['comment'] ?? '') ?></p>
                        <small class="text-muted"><?= e($review['created_at']) ?></small>
                    </div>
                <?php endwhile; ?>
            </section>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h2 class="h6">Informasi Kontak</h2>
                    <p class="small mb-1"><strong>Alamat:</strong><br><?= nl2br(e($restaurant['address'])) ?></p>
                    <p class="small mb-1"><strong>Telepon:</strong> <?= e($restaurant['phone'] ?? '-') ?></p>
                    <p class="small mb-1"><strong>Email:</strong> <?= e($restaurant['email'] ?? '-') ?></p>
                    <p class="small mb-3"><strong>Jam Operasional:</strong> <?= e((string)$restaurant['open_time']) ?> - <?= e((string)$restaurant['close_time']) ?></p>

                    <h2 class="h6">Peta Lokasi</h2>
                    <?php if (!empty($mapsEmbed)): ?>
                        <div class="ratio ratio-16x9">
                            <?= $mapsEmbed ?>
                        </div>
                    <?php else: ?>
                        <iframe class="w-100 rounded" height="250" loading="lazy" referrerpolicy="no-referrer-when-downgrade" src="https://www.google.com/maps?q=<?= $mapsQuery ?>&output=embed"></iframe>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
