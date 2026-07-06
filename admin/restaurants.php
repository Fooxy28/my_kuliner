<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/helpers.php';

requireRole('admin');
$pageTitle = 'Kelola Restoran';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'create') {
        $userId = (int)($_POST['user_id'] ?? 0);
        $name = trim((string)($_POST['name'] ?? ''));
        $address = trim((string)($_POST['address'] ?? ''));
        $district = trim((string)($_POST['district'] ?? ''));
        $status = (string)($_POST['status'] ?? 'pending');

        if ($userId > 0 && $name !== '' && $address !== '') {
            $slug = slugify($name . '-' . uniqid());
            $stmt = mysqli_prepare($conn, 'INSERT INTO restaurants (user_id, name, slug, address, district, status) VALUES (?, ?, ?, ?, ?, ?)');
            mysqli_stmt_bind_param($stmt, 'isssss', $userId, $name, $slug, $address, $district, $status);
            mysqli_stmt_execute($stmt);
        }
    }

    if ($action === 'status') {
        $id = (int)($_POST['id'] ?? 0);
        $status = (string)($_POST['status'] ?? 'pending');
        $stmt = mysqli_prepare($conn, 'UPDATE restaurants SET status = ? WHERE id = ?');
        mysqli_stmt_bind_param($stmt, 'si', $status, $id);
        mysqli_stmt_execute($stmt);
    }

    if ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        $stmt = mysqli_prepare($conn, 'DELETE FROM restaurants WHERE id = ?');
        mysqli_stmt_bind_param($stmt, 'i', $id);
        mysqli_stmt_execute($stmt);
    }

    redirect('admin/restaurants.php');
}

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';

$admins = mysqli_query($conn, "SELECT id, fullname FROM users WHERE role = 'restaurant_admin' AND status = 'active' ORDER BY fullname");
$restaurants = mysqli_query($conn, "SELECT r.*, u.fullname AS owner_name FROM restaurants r INNER JOIN users u ON u.id = r.user_id ORDER BY r.created_at DESC");
?>
<main class="container py-4">
    <div class="row g-4">
        <div class="col-lg-3"><?php require __DIR__ . '/../includes/sidebar.php'; ?></div>
        <div class="col-lg-9">
            <h1 class="h4 mb-3">Manajemen Restoran</h1>

            <form method="post" class="card card-body mb-4 border-0 shadow-sm">
                <input type="hidden" name="action" value="create">
                <div class="row g-2">
                    <div class="col-md-3">
                        <select name="user_id" class="form-select" required>
                            <option value="">Pilih Admin Restoran</option>
                            <?php while ($admin = mysqli_fetch_assoc($admins)): ?>
                                <option value="<?= (int)$admin['id'] ?>"><?= e($admin['fullname']) ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="col-md-3"><input class="form-control" name="name" placeholder="Nama Restoran" required></div>
                    <div class="col-md-3"><input class="form-control" name="district" placeholder="Kecamatan"></div>
                    <div class="col-md-3"><select class="form-select" name="status"><option>pending</option><option>approved</option><option>rejected</option></select></div>
                    <div class="col-md-12"><textarea class="form-control" name="address" placeholder="Alamat" required></textarea></div>
                    <div class="col-md-12 d-grid"><button class="btn btn-success btn-sm">Tambah Restoran</button></div>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-striped align-middle">
                    <thead><tr><th>Nama</th><th>Pemilik</th><th>Kecamatan</th><th>Status</th><th>Aksi</th></tr></thead>
                    <tbody>
                    <?php while ($r = mysqli_fetch_assoc($restaurants)): ?>
                        <tr>
                            <td><?= e($r['name']) ?></td>
                            <td><?= e($r['owner_name']) ?></td>
                            <td><?= e($r['district'] ?? '-') ?></td>
                            <td>
                                <form method="post" class="d-flex gap-2">
                                    <input type="hidden" name="action" value="status">
                                    <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                                    <select name="status" class="form-select form-select-sm">
                                        <option value="pending" <?= $r['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                                        <option value="approved" <?= $r['status'] === 'approved' ? 'selected' : '' ?>>Approved</option>
                                        <option value="rejected" <?= $r['status'] === 'rejected' ? 'selected' : '' ?>>Rejected</option>
                                    </select>
                                    <button class="btn btn-sm btn-primary">Simpan</button>
                                </form>
                            </td>
                            <td>
                                <form method="post" onsubmit="return confirm('Hapus restoran ini?')">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
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
