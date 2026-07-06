<?php

declare(strict_types=1);

$pageTitle = 'Katalog Restoran';

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';

$q = trim((string)($_GET['q'] ?? ''));
$district = trim((string)($_GET['district'] ?? ''));
$category = trim((string)($_GET['category'] ?? ''));
$minPrice = trim((string)($_GET['min_price'] ?? ''));
$maxPrice = trim((string)($_GET['max_price'] ?? ''));
$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 9;
$offset = ($page - 1) * $perPage;

$districtsResult = mysqli_query($conn, "SELECT DISTINCT district FROM restaurants WHERE district IS NOT NULL AND district != '' ORDER BY district ASC");

$where = " WHERE r.status = 'approved' ";
$params = [];
$types = '';

if ($q !== '') {
    $where .= ' AND (r.name LIKE ? OR EXISTS (SELECT 1 FROM menus m WHERE m.restaurant_id = r.id AND m.name LIKE ?))';
    $search = '%' . $q . '%';
    $params[] = $search;
    $params[] = $search;
    $types .= 'ss';
}

if ($district !== '') {
    $where .= ' AND r.district = ?';
    $params[] = $district;
    $types .= 's';
}

if ($category !== '') {
    $where .= ' AND r.category = ?';
    $params[] = $category;
    $types .= 's';
}

if ($minPrice !== '' && is_numeric($minPrice)) {
    $where .= ' AND EXISTS (SELECT 1 FROM menus m WHERE m.restaurant_id = r.id AND m.price >= ?)';
    $params[] = (float)$minPrice;
    $types .= 'd';
}

if ($maxPrice !== '' && is_numeric($maxPrice)) {
    $where .= ' AND EXISTS (SELECT 1 FROM menus m WHERE m.restaurant_id = r.id AND m.price <= ?)';
    $params[] = (float)$maxPrice;
    $types .= 'd';
}

$countSql = "SELECT COUNT(*) AS total FROM restaurants r $where";
$countStmt = mysqli_prepare($conn, $countSql);
if (!empty($params)) {
    mysqli_stmt_bind_param($countStmt, $types, ...$params);
}
mysqli_stmt_execute($countStmt);
$totalData = mysqli_stmt_get_result($countStmt)->fetch_assoc();
$totalRows = (int)($totalData['total'] ?? 0);
$totalPages = max(1, (int)ceil($totalRows / $perPage));

$sql = "
    SELECT r.*, COALESCE(AVG(rv.rating), 0) AS avg_rating, COUNT(rv.id) AS total_reviews
    FROM restaurants r
    LEFT JOIN reviews rv ON rv.restaurant_id = r.id AND rv.status = 'approved'
    $where
    GROUP BY r.id
    ORDER BY r.created_at DESC
    LIMIT ? OFFSET ?
";
$stmt = mysqli_prepare($conn, $sql);
$finalTypes = $types . 'ii';
$finalParams = $params;
$finalParams[] = $perPage;
$finalParams[] = $offset;
mysqli_stmt_bind_param($stmt, $finalTypes, ...$finalParams);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>

<main class="container py-5">
    <div class="text-center mb-5">
        <h1 class="display-5 fw-bold text-dark">Katalog Kuliner Wisata</h1>
        <p class="text-muted">Temukan destinasi kuliner bersejarah terbaik di Pulau Lombok</p>
    </div>

    <form method="get" class="glass-panel p-4 mb-5 mx-auto bg-white" style="max-width: 1000px;">
        <div class="row g-3">
            <div class="col-md-12">
                <label class="form-label small fw-bold text-muted">Kata Kunci</label>
                <div class="input-group input-group-lg">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" name="q" class="form-control border-start-0 ps-0 shadow-none" value="<?= e($q) ?>" placeholder="Cari nama restoran atau menu...">
                </div>
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-bold text-muted">Kecamatan</label>
                <select name="district" class="form-select border-0 bg-light">
                    <option value="">Semua Wilayah</option>
                    <?php while ($d = mysqli_fetch_assoc($districtsResult)): ?>
                        <option value="<?= e($d['district']) ?>" <?= $district === ($d['district'] ?? '') ? 'selected' : '' ?>><?= e($d['district']) ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-bold text-muted">Kategori</label>
                <select name="category" class="form-select border-0 bg-light">
                    <option value="">Semua Kategori</option>
                    <option value="Resep Turun Temurun" <?= $category === 'Resep Turun Temurun' ? 'selected' : '' ?>>Resep Turun Temurun</option>
                    <option value="Bangunan Bersejarah" <?= $category === 'Bangunan Bersejarah' ? 'selected' : '' ?>>Bangunan Bersejarah</option>
                    <option value="Legenda Kuliner" <?= $category === 'Legenda Kuliner' ? 'selected' : '' ?>>Legenda Kuliner</option>
                    <option value="Kuliner Tradisional" <?= $category === 'Kuliner Tradisional' ? 'selected' : '' ?>>Kuliner Tradisional</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-bold text-muted">Harga Min (Rp)</label>
                <input type="number" name="min_price" class="form-control border-0 bg-light" placeholder="0" value="<?= e($minPrice) ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-bold text-muted">Harga Max (Rp)</label>
                <input type="number" name="max_price" class="form-control border-0 bg-light" placeholder="~" value="<?= e($maxPrice) ?>">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button class="btn btn-success w-100 py-2" type="submit">Terapkan Filter</button>
            </div>
        </div>
    </form>

    <div class="row g-4">
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
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

    <nav class="mt-4">
        <ul class="pagination">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                    <a class="page-link" href="?q=<?= urlencode($q) ?>&district=<?= urlencode($district) ?>&category=<?= urlencode($category) ?>&min_price=<?= urlencode($minPrice) ?>&max_price=<?= urlencode($maxPrice) ?>&page=<?= $i ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>
        </ul>
    </nav>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
