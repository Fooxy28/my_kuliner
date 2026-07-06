<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/helpers.php';

requireRole('admin');
$pageTitle = 'Kelola User';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'create') {
        $fullname = trim((string)($_POST['fullname'] ?? ''));
        $username = trim((string)($_POST['username'] ?? ''));
        $email = trim((string)($_POST['email'] ?? ''));
        $role = (string)($_POST['role'] ?? 'user');
        $password = (string)($_POST['password'] ?? '123456');
        $status = (string)($_POST['status'] ?? 'active');

        if ($fullname !== '' && $username !== '' && filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = mysqli_prepare($conn, 'INSERT INTO users (fullname, username, email, password, role, status) VALUES (?, ?, ?, ?, ?, ?)');
            mysqli_stmt_bind_param($stmt, 'ssssss', $fullname, $username, $email, $hash, $role, $status);
            mysqli_stmt_execute($stmt);
        }
    }

    if ($action === 'update') {
        $id = (int)($_POST['id'] ?? 0);
        $fullname = trim((string)($_POST['fullname'] ?? ''));
        $phone = trim((string)($_POST['phone'] ?? ''));
        $role = (string)($_POST['role'] ?? 'user');
        $status = (string)($_POST['status'] ?? 'active');

        $stmt = mysqli_prepare($conn, 'UPDATE users SET fullname = ?, phone = ?, role = ?, status = ? WHERE id = ?');
        mysqli_stmt_bind_param($stmt, 'ssssi', $fullname, $phone, $role, $status, $id);
        mysqli_stmt_execute($stmt);
    }

    if ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        $stmt = mysqli_prepare($conn, 'DELETE FROM users WHERE id = ?');
        mysqli_stmt_bind_param($stmt, 'i', $id);
        mysqli_stmt_execute($stmt);
    }

    redirect('admin/users.php');
}

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
$users = mysqli_query($conn, 'SELECT * FROM users ORDER BY created_at DESC');
?>
<main class="container py-4">
    <div class="row g-4">
        <div class="col-lg-3"><?php require __DIR__ . '/../includes/sidebar.php'; ?></div>
        <div class="col-lg-9">
            <h1 class="h4 mb-3">Manajemen User</h1>

            <form method="post" class="card card-body mb-4 border-0 shadow-sm">
                <input type="hidden" name="action" value="create">
                <div class="row g-2">
                    <div class="col-md-3"><input class="form-control" name="fullname" placeholder="Nama Lengkap" required></div>
                    <div class="col-md-2"><input class="form-control" name="username" placeholder="Username" required></div>
                    <div class="col-md-3"><input class="form-control" type="email" name="email" placeholder="Email" required></div>
                    <div class="col-md-2">
                        <select class="form-select" name="role">
                            <option value="user">User</option>
                            <option value="restaurant_admin">Admin Restoran</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    <div class="col-md-2"><input class="form-control" type="password" name="password" value="123456" required></div>
                    <div class="col-md-12 d-grid"><button class="btn btn-success btn-sm">Tambah User</button></div>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-striped align-middle">
                    <thead><tr><th>ID</th><th>Nama</th><th>Email</th><th>Role</th><th>Status</th><th>Aksi</th></tr></thead>
                    <tbody>
                    <?php while ($user = mysqli_fetch_assoc($users)): ?>
                        <tr>
                            <td><?= (int)$user['id'] ?></td>
                            <td><?= e($user['fullname']) ?><br><small class="text-muted">@<?= e($user['username']) ?></small></td>
                            <td><?= e($user['email']) ?></td>
                            <td>
                                <form method="post" class="d-flex gap-2">
                                    <input type="hidden" name="action" value="update">
                                    <input type="hidden" name="id" value="<?= (int)$user['id'] ?>">
                                    <input type="hidden" name="fullname" value="<?= e($user['fullname']) ?>">
                                    <input type="hidden" name="phone" value="<?= e($user['phone'] ?? '') ?>">
                                    <select class="form-select form-select-sm" name="role">
                                        <option value="user" <?= $user['role'] === 'user' ? 'selected' : '' ?>>User</option>
                                        <option value="restaurant_admin" <?= $user['role'] === 'restaurant_admin' ? 'selected' : '' ?>>Admin Restoran</option>
                                        <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                                    </select>
                            </td>
                            <td>
                                    <select class="form-select form-select-sm" name="status">
                                        <option value="active" <?= $user['status'] === 'active' ? 'selected' : '' ?>>Aktif</option>
                                        <option value="inactive" <?= $user['status'] === 'inactive' ? 'selected' : '' ?>>Nonaktif</option>
                                    </select>
                            </td>
                            <td class="d-flex gap-2">
                                    <button class="btn btn-sm btn-primary">Simpan</button>
                                </form>
                                <form method="post" onsubmit="return confirm('Hapus user ini?')">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?= (int)$user['id'] ?>">
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
