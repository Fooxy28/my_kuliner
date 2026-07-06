<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/helpers.php';

requireRole('admin');
$pageTitle = 'Kelola Galeri';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'create') {
        $restaurantId = (int)($_POST['restaurant_id'] ?? 0);
        $caption = trim((string)($_POST['caption'] ?? ''));
        $image = uploadImage('image', __DIR__ . '/../uploads/gallery');

        if ($restaurantId > 0 && $image !== null) {
            $stmt = mysqli_prepare($conn, 'INSERT INTO gallery (restaurant_id, image, caption) VALUES (?, ?, ?)');
            mysqli_stmt_bind_param($stmt, 'iss', $restaurantId, $image, $caption);
            mysqli_stmt_execute($stmt);
        }
    }

    if ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        $stmt = mysqli_prepare($conn, 'DELETE FROM gallery WHERE id = ?');
        mysqli_stmt_bind_param($stmt, 'i', $id);
        mysqli_stmt_execute($stmt);
    }

    redirect('admin/gallery.php');
}

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';

$restaurants = mysqli_query($conn, 'SELECT id, name FROM restaurants ORDER BY name');
$galleries = mysqli_query($conn, 'SELECT g.*, r.name AS restaurant_name FROM gallery g INNER JOIN restaurants r ON r.id = g.restaurant_id ORDER BY g.created_at DESC');
?>
<main class="container py-4">
    <div class="row g-4">
        <div class="col-lg-3"><?php require __DIR__ . '/../includes/sidebar.php'; ?></div>
        <div class="col-lg-9">
            <h1 class="h4 mb-3">Manajemen Galeri</h1>
            <form method="post" enctype="multipart/form-data" class="card card-body mb-4 border-0 shadow-sm">
                <input type="hidden" name="action" value="create">
                <div class="row g-2">
                    <div class="col-md-4">
                        <select name="restaurant_id" class="form-select" required>
                            <option value="">Pilih Restoran</option>
                            <?php while ($r = mysqli_fetch_assoc($restaurants)): ?>
                                <option value="<?= (int)$r['id'] ?>"><?= e($r['name']) ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="col-md-4"><input name="caption" class="form-control" placeholder="Caption"></div>
                    <div class="col-md-4"><input type="file" name="image" class="form-control" accept="image/*" required></div>
                    <div class="col-md-12 d-grid"><button class="btn btn-success btn-sm">Upload</button></div>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-striped align-middle">
                    <thead><tr><th>Preview</th><th>Restoran</th><th>Caption</th><th>Aksi</th></tr></thead>
                    <tbody>
                    <?php while ($g = mysqli_fetch_assoc($galleries)): ?>
                        <tr>
                            <td><img src="<?= e(baseUrl('uploads/gallery/' . $g['image'])) ?>" style="width:80px;height:60px;object-fit:cover" class="rounded" alt="gallery"></td>
                            <td><?= e($g['restaurant_name']) ?></td>
                            <td><?= e($g['caption'] ?? '-') ?></td>
                            <td>
                                <form method="post" onsubmit="return confirm('Hapus gambar ini?')">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?= (int)$g['id'] ?>">
                                    <button class="btn btn-sm btn-danger">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
