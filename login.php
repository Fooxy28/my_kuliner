<?php

declare(strict_types=1);

$pageTitle = 'Login';

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
$error = null;
$success = flash('success');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identity = trim((string)($_POST['identity'] ?? ''));
    $password = (string)($_POST['password'] ?? '');

    $stmt = mysqli_prepare($conn, 'SELECT * FROM users WHERE (email = ? OR username = ?) LIMIT 1');
    mysqli_stmt_bind_param($stmt, 'ss', $identity, $identity);
    mysqli_stmt_execute($stmt);
    $user = mysqli_stmt_get_result($stmt)->fetch_assoc();

    if (!$user || !password_verify($password, $user['password']) || ($user['status'] ?? 'inactive') !== 'active') {
        $error = 'Login gagal. Cek kembali akun Anda.';
    } else {
        $_SESSION['user'] = [
            'id' => (int)$user['id'],
            'username' => $user['username'],
            'fullname' => $user['fullname'],
            'email' => $user['email'],
            'role' => $user['role']
        ];

        if ($user['role'] === 'admin') {
            redirect('admin/index.php');
        }

        if ($user['role'] === 'restaurant_admin') {
            redirect('restaurant_admin/index.php');
        }

        redirect('index.php');
    }
}
?>

<main class="container py-5" style="max-width: 560px;">
    <h1 class="h3 mb-4">Login</h1>

    <?php if ($success): ?>
        <div class="alert alert-success"><?= e($success) ?></div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= e($error) ?></div>
    <?php endif; ?>

    <form method="post" class="card card-body shadow-sm border-0">
        <div class="mb-3">
            <label class="form-label">Email atau Username</label>
            <input type="text" name="identity" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-success">Masuk</button>
    </form>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
