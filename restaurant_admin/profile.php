<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/helpers.php';

requireRole('restaurant_admin');
$pageTitle = 'Profil Restoran';

$user = currentUser();
$userId = (int)($user['id'] ?? 0);

$restaurantStmt = mysqli_prepare($conn, 'SELECT * FROM restaurants WHERE user_id = ? ORDER BY id ASC LIMIT 1');
mysqli_stmt_bind_param($restaurantStmt, 'i', $userId);
mysqli_stmt_execute($restaurantStmt);
$restaurant = mysqli_stmt_get_result($restaurantStmt)->fetch_assoc();

if (!$restaurant) {
    die('Data restoran tidak ditemukan.');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim((string)($_POST['name'] ?? ''));
    $description = trim((string)($_POST['description'] ?? ''));
    $history = trim((string)($_POST['history'] ?? ''));
    $address = trim((string)($_POST['address'] ?? ''));
    $district = trim((string)($_POST['district'] ?? ''));
    $village = trim((string)($_POST['village'] ?? ''));
    $phone = trim((string)($_POST['phone'] ?? ''));
    $email = trim((string)($_POST['email'] ?? ''));
    $website = trim((string)($_POST['website'] ?? ''));
    $openTime = $_POST['open_time'] ?? null;
    $closeTime = $_POST['close_time'] ?? null;
    $category = trim((string)($_POST['category'] ?? ''));
    $facilities = implode(', ', $_POST['facilities'] ?? []);
    
    $mapsEmbed = trim((string)($_POST['maps_embed'] ?? ''));
    $mainImage = uploadImage('main_image', __DIR__ . '/../uploads/restaurants');

    if ($mainImage === null) {
        $mainImage = $restaurant['main_image'];
    }

    $stmt = mysqli_prepare($conn, 'UPDATE restaurants SET name=?, category=?, description=?, history=?, address=?, district=?, village=?, phone=?, email=?, website=?, open_time=?, close_time=?, maps_embed=?, facilities=?, main_image=? WHERE id=?');
    mysqli_stmt_bind_param($stmt, 'sssssssssssssssi', $name, $category, $description, $history, $address, $district, $village, $phone, $email, $website, $openTime, $closeTime, $mapsEmbed, $facilities, $mainImage, $restaurant['id']);
    mysqli_stmt_execute($stmt);

    redirect('restaurant_admin/profile.php');
}

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>
<main class="container py-4">
    <div class="row g-4">
        <div class="col-lg-3"><?php require __DIR__ . '/../includes/sidebar.php'; ?></div>
        <div class="col-lg-9">
            <h1 class="h4 mb-3">Profil Restoran</h1>
            <form method="post" enctype="multipart/form-data" class="card card-body border-0 shadow-sm">
                <div class="row g-3">
                    <div class="col-md-6"><label class="form-label">Nama</label><input name="name" value="<?= e($restaurant['name']) ?>" class="form-control" required></div>
                    <div class="col-md-6">
                        <label class="form-label">Kategori Sejarah</label>
                        <select name="category" class="form-select">
                            <option value="">Pilih Kategori...</option>
                            <option value="Resep Turun Temurun" <?= ($restaurant['category'] ?? '') === 'Resep Turun Temurun' ? 'selected' : '' ?>>Resep Turun Temurun</option>
                            <option value="Bangunan Bersejarah" <?= ($restaurant['category'] ?? '') === 'Bangunan Bersejarah' ? 'selected' : '' ?>>Bangunan Bersejarah</option>
                            <option value="Legenda Kuliner" <?= ($restaurant['category'] ?? '') === 'Legenda Kuliner' ? 'selected' : '' ?>>Legenda Kuliner</option>
                            <option value="Kuliner Tradisional" <?= ($restaurant['category'] ?? '') === 'Kuliner Tradisional' ? 'selected' : '' ?>>Kuliner Tradisional</option>
                        </select>
                    </div>
                    <div class="col-md-3"><label class="form-label">Kecamatan</label><input name="district" value="<?= e($restaurant['district'] ?? '') ?>" class="form-control"></div>
                    <div class="col-md-3"><label class="form-label">Desa</label><input name="village" value="<?= e($restaurant['village'] ?? '') ?>" class="form-control"></div>
                    <div class="col-md-12"><label class="form-label">Alamat</label><textarea name="address" class="form-control" required><?= e($restaurant['address']) ?></textarea></div>
                    <div class="col-md-12"><label class="form-label">Sejarah</label><textarea name="history" rows="4" class="form-control"><?= e($restaurant['history'] ?? '') ?></textarea></div>
                    <div class="col-md-12"><label class="form-label">Deskripsi</label><textarea name="description" rows="4" class="form-control"><?= e($restaurant['description'] ?? '') ?></textarea></div>
                    <div class="col-md-4"><label class="form-label">Telepon</label><input name="phone" value="<?= e($restaurant['phone'] ?? '') ?>" class="form-control"></div>
                    <div class="col-md-4"><label class="form-label">Email</label><input type="email" name="email" value="<?= e($restaurant['email'] ?? '') ?>" class="form-control"></div>
                    <div class="col-md-4"><label class="form-label">Website</label><input name="website" value="<?= e($restaurant['website'] ?? '') ?>" class="form-control"></div>
                    <div class="col-md-3"><label class="form-label">Buka</label><input type="time" name="open_time" value="<?= e((string)$restaurant['open_time']) ?>" class="form-control"></div>
                    <div class="col-md-3"><label class="form-label">Tutup</label><input type="time" name="close_time" value="<?= e((string)$restaurant['close_time']) ?>" class="form-control"></div>
                    <div class="col-md-12">
                        <label class="form-label mb-2">Fasilitas</label>
                        <div>
                            <?php 
                            $savedFacilities = array_map('trim', explode(',', $restaurant['facilities'] ?? ''));
                            $availableFacilities = ['Parkir Luas', 'WiFi', 'Musala', 'Toilet Bersih', 'Area Merokok', 'Ramah Anak', 'Akses Kursi Roda', 'Pembayaran Non-Tunai'];
                            foreach($availableFacilities as $fac): 
                            ?>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="facilities[]" value="<?= e($fac) ?>" id="fac_<?= md5($fac) ?>" <?= in_array($fac, $savedFacilities) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="fac_<?= md5($fac) ?>"><?= e($fac) ?></label>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Kode Embed Google Maps</label>
                        <textarea name="maps_embed" rows="3" class="form-control" placeholder='<iframe src="https://www.google.com/maps/embed?pb=..." ...></iframe>'><?= e($restaurant['maps_embed'] ?? '') ?></textarea>
                        <small class="text-muted">Cari restoran Anda di Google Maps, klik Bagikan > Sematkan Peta, lalu salin kodenya ke sini.</small>
                    </div>
                    <div class="col-md-6"><label class="form-label">Foto Utama</label><input type="file" name="main_image" class="form-control" accept="image/*"></div>
                    <div class="col-md-12 d-grid"><button class="btn btn-success">Simpan Perubahan</button></div>
                </div>
            </form>
        </div>
    </div>
</main>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
