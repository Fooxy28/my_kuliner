<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/helpers.php';

requireRole('admin');
$pageTitle = 'Kelola Menu';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'create') {
        $restaurantId = (int)($_POST['restaurant_id'] ?? 0);
        $name = trim((string)($_POST['name'] ?? ''));
        $description = trim((string)($_POST['description'] ?? ''));
        $price = (float)($_POST['price'] ?? 0);
        $status = (string)($_POST['status'] ?? 'available');

        $stmt = mysqli_prepare($conn, 'INSERT INTO menus (restaurant_id, name, description, price, status) VALUES (?, ?, ?, ?, ?)');
        mysqli_stmt_bind_param($stmt, 'issds', $restaurantId, $name, $description, $price, $status);
        mysqli_stmt_execute($stmt);
    }

    if ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        $stmt = mysqli_prepare($conn, 'DELETE FROM menus WHERE id = ?');
        mysqli_stmt_bind_param($stmt, 'i', $id);
        mysqli_stmt_execute($stmt);
    }

    redirect('admin/menus.php');
}

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';

$restaurants = mysqli_query($conn, "SELECT id, name FROM restaurants ORDER BY name ASC");
$menus = mysqli_query($conn, "SELECT m.*, r.name AS restaurant_name FROM menus m INNER JOIN restaurants r ON r.id = m.restaurant_id ORDER BY m.created_at DESC");
?>
<main class="container py-4">
    <div class="row g-4">
        <div class="col-lg-3"><?php require __DIR__ . '/../includes/sidebar.php'; ?></div>
        <div class="col-lg-9">
            <h1 class="h4 mb-3">Manajemen Menu</h1>
            <form method="post" class="card card-body mb-4 border-0 shadow-sm">
                <input type="hidden" name="action" value="create">
                <div class="row g-2">
                    <div class="col-md-3">
                        <select name="restaurant_id" class="form-select" required>
                            <option value="">Pilih Restoran</option>
                            <?php while ($r = mysqli_fetch_assoc($restaurants)): ?>
                                <option value="<?= (int)$r['id'] ?>"><?= e($r['name']) ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="col-md-3"><input name="name" class="form-control" placeholder="Nama Menu" required></div>
                    <div class="col-md-2"><input name="price" type="number" step="0.01" class="form-control" placeholder="Harga" required></div>
                    <div class="col-md-2"><select name="status" class="form-select"><option>available</option><option>unavailable</option></select></div>
                    <div class="col-md-12"><textarea name="description" class="form-control" placeholder="Deskripsi"></textarea></div>
                    <div class="col-md-12 d-grid"><button class="btn btn-success btn-sm">Tambah Menu</button></div>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-striped">
                    <thead><tr><th>Menu</th><th>Restoran</th><th>Harga</th><th>Status</th><th>Aksi</th></tr></thead>
                    <tbody>
                    <?php while ($m = mysqli_fetch_assoc($menus)): ?>
                        <tr>
                            <td><?= e($m['name']) ?></td>
                            <td><?= e($m['restaurant_name']) ?></td>
                            <td>Rp <?= number_format((float)$m['price'], 0, ',', '.') ?></td>
                            <td><?= e($m['status']) ?></td>
                            <td>
                                <form method="post" onsubmit="return confirm('Hapus menu ini?')">
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
