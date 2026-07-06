<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/helpers.php';

requireRole('user');
$pageTitle = 'Profil User';

$user = currentUser();
$userId = (int)($user['id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'profile') {
        $fullname = trim((string)($_POST['fullname'] ?? ''));
        $phone = trim((string)($_POST['phone'] ?? ''));

        $stmt = mysqli_prepare($conn, 'UPDATE users SET fullname = ?, phone = ? WHERE id = ?');
        mysqli_stmt_bind_param($stmt, 'ssi', $fullname, $phone, $userId);
        mysqli_stmt_execute($stmt);

        $_SESSION['user']['fullname'] = $fullname;
    }

    if ($action === 'password') {
        $old = (string)($_POST['old_password'] ?? '');
        $new = (string)($_POST['new_password'] ?? '');

        $checkStmt = mysqli_prepare($conn, 'SELECT password FROM users WHERE id = ?');
        mysqli_stmt_bind_param($checkStmt, 'i', $userId);
        mysqli_stmt_execute($checkStmt);
        $data = mysqli_stmt_get_result($checkStmt)->fetch_assoc();

        if ($data && password_verify($old, $data['password']) && strlen($new) >= 6) {
            $hash = password_hash($new, PASSWORD_DEFAULT);
            $upStmt = mysqli_prepare($conn, 'UPDATE users SET password = ? WHERE id = ?');
            mysqli_stmt_bind_param($upStmt, 'si', $hash, $userId);
            mysqli_stmt_execute($upStmt);
        }
    }

    redirect('user/profile.php');
}

$stmt = mysqli_prepare($conn, 'SELECT * FROM users WHERE id = ?');
mysqli_stmt_bind_param($stmt, 'i', $userId);
mysqli_stmt_execute($stmt);
$data = mysqli_stmt_get_result($stmt)->fetch_assoc();

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>
<main class="container py-4" style="max-width: 900px;">
    <h1 class="h4 mb-3">Profil Saya</h1>
    <div class="row g-4">
        <div class="col-md-6">
            <form method="post" class="card card-body border-0 shadow-sm">
                <input type="hidden" name="action" value="profile">
                <h2 class="h6">Update Profil</h2>
                <div class="mb-2"><label class="form-label">Nama Lengkap</label><input class="form-control" name="fullname" value="<?= e($data['fullname']) ?>"></div>
                <div class="mb-2"><label class="form-label">Email</label><input class="form-control" value="<?= e($data['email']) ?>" disabled></div>
                <div class="mb-2"><label class="form-label">No HP</label><input class="form-control" name="phone" value="<?= e($data['phone'] ?? '') ?>"></div>
                <button class="btn btn-success btn-sm">Simpan Profil</button>
            </form>
        </div>
        <div class="col-md-6">
            <form method="post" class="card card-body border-0 shadow-sm">
                <input type="hidden" name="action" value="password">
                <h2 class="h6">Ganti Password</h2>
                <div class="mb-2"><label class="form-label">Password Lama</label><input type="password" class="form-control" name="old_password" required></div>
                <div class="mb-2"><label class="form-label">Password Baru</label><input type="password" class="form-control" name="new_password" required></div>
                <button class="btn btn-outline-success btn-sm">Ubah Password</button>
            </form>
        </div>
    </div>
    <a href="<?= e(baseUrl('user/reviews.php')) ?>" class="btn btn-link px-0 mt-3">Lihat Ulasan Saya</a>
</main>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
