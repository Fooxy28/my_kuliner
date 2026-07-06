<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/helpers.php';

requireRole('restaurant_admin');
$pageTitle = 'Galeri Restoran';

$user = currentUser();
$userId = (int)($user['id'] ?? 0);
$restaurant = mysqli_query($conn, "SELECT id FROM restaurants WHERE user_id = $userId LIMIT 1")->fetch_assoc();
if (!$restaurant) {
    die('Restoran tidak ditemukan.');
}
$restaurantId = (int)$restaurant['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'create') {
        $caption = trim((string)($_POST['caption'] ?? ''));
        $image = uploadImage('image', __DIR__ . '/../uploads/gallery');

        if ($image !== null) {
            $stmt = mysqli_prepare($conn, 'INSERT INTO gallery (restaurant_id, image, caption) VALUES (?, ?, ?)');
            mysqli_stmt_bind_param($stmt, 'iss', $restaurantId, $image, $caption);
            mysqli_stmt_execute($stmt);
        }
    }

    if ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        $stmt = mysqli_prepare($conn, 'DELETE FROM gallery WHERE id = ? AND restaurant_id = ?');
        mysqli_stmt_bind_param($stmt, 'ii', $id, $restaurantId);
        mysqli_stmt_execute($stmt);
    }

    redirect('restaurant_admin/gallery.php');
}

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
$galleries = mysqli_query($conn, "SELECT * FROM gallery WHERE restaurant_id = $restaurantId ORDER BY created_at DESC");
?>
<main class="container py-4">
    <div class="row g-4">
        <div class="col-lg-3"><?php require __DIR__ . '/../includes/sidebar.php'; ?></div>
        <div class="col-lg-9">
            <h1 class="h4 mb-3">Kelola Galeri</h1>
            <form method="post" enctype="multipart/form-data" class="card card-body mb-4 border-0 shadow-sm">
                <input type="hidden" name="action" value="create">
                <div class="row g-2">
                    <div class="col-md-6"><input class="form-control" name="caption" placeholder="Caption"></div>
                    <div class="col-md-6"><input class="form-control" type="file" name="image" accept="image/*" required></div>
                    <div class="col-md-12 d-grid"><button class="btn btn-success btn-sm">Upload</button></div>
                </div>
            </form>

            <div class="row g-3">
                <?php while ($g = mysqli_fetch_assoc($galleries)): ?>
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm">
                            <img src="<?= e(baseUrl('uploads/gallery/' . $g['image'])) ?>" class="card-img-top" style="height:180px;object-fit:cover" alt="gallery">
                            <div class="card-body">
                                <p class="small mb-2"><?= e($g['caption'] ?? '-') ?></p>
                                <form method="post" onsubmit="return confirm('Hapus gambar?')">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?= (int)$g['id'] ?>">
                                    <button class="btn btn-sm btn-danger w-100">Hapus</button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>
</main>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
