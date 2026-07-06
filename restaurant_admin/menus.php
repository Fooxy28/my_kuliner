<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/helpers.php';

requireRole('restaurant_admin');
$pageTitle = 'Menu Restoran';

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
        $name = trim((string)($_POST['name'] ?? ''));
        $description = trim((string)($_POST['description'] ?? ''));
        $price = (float)($_POST['price'] ?? 0);
        $status = (string)($_POST['status'] ?? 'available');
        $image = uploadImage('image', __DIR__ . '/../uploads/menus');

        $stmt = mysqli_prepare($conn, 'INSERT INTO menus (restaurant_id, name, description, price, image, status) VALUES (?, ?, ?, ?, ?, ?)');
        mysqli_stmt_bind_param($stmt, 'issdss', $restaurantId, $name, $description, $price, $image, $status);
        mysqli_stmt_execute($stmt);
    }

    if ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        $stmt = mysqli_prepare($conn, 'DELETE FROM menus WHERE id = ? AND restaurant_id = ?');
        mysqli_stmt_bind_param($stmt, 'ii', $id, $restaurantId);
        mysqli_stmt_execute($stmt);
    }

    redirect('restaurant_admin/menus.php');
}

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
$menus = mysqli_query($conn, "SELECT * FROM menus WHERE restaurant_id = $restaurantId ORDER BY created_at DESC");
?>
<main class="container py-4">
    <div class="row g-4">
        <div class="col-lg-3"><?php require __DIR__ . '/../includes/sidebar.php'; ?></div>
        <div class="col-lg-9">
            <h1 class="h4 mb-3">Kelola Menu</h1>
            <form method="post" enctype="multipart/form-data" class="card card-body mb-4 border-0 shadow-sm">
                <input type="hidden" name="action" value="create">
                <div class="row g-2">
                    <div class="col-md-4"><input name="name" class="form-control" placeholder="Nama Menu" required></div>
                    <div class="col-md-2"><input name="price" type="number" step="0.01" class="form-control" placeholder="Harga" required></div>
                    <div class="col-md-2"><select name="status" class="form-select"><option>available</option><option>unavailable</option></select></div>
                    <div class="col-md-4"><input type="file" name="image" class="form-control" accept="image/*"></div>
                    <div class="col-md-12"><textarea name="description" class="form-control" placeholder="Deskripsi"></textarea></div>
                    <div class="col-md-12 d-grid"><button class="btn btn-success btn-sm">Tambah Menu</button></div>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-striped align-middle">
                    <thead><tr><th>Menu</th><th>Harga</th><th>Status</th><th>Aksi</th></tr></thead>
                    <tbody>
                    <?php while ($m = mysqli_fetch_assoc($menus)): ?>
                        <tr>
                            <td><?= e($m['name']) ?></td>
                            <td>Rp <?= number_format((float)$m['price'], 0, ',', '.') ?></td>
                            <td><?= e($m['status']) ?></td>
                            <td>
                                <form method="post" onsubmit="return confirm('Hapus menu?')">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?= (int)$m['id'] ?>">
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
