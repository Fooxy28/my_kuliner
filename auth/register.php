<?php

declare(strict_types=1);

$pageTitle = 'Register';

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim((string)($_POST['username'] ?? ''));
    $email = trim((string)($_POST['email'] ?? ''));
    $fullname = trim((string)($_POST['fullname'] ?? ''));
    $password = (string)($_POST['password'] ?? '');
    $confirmPassword = (string)($_POST['confirm_password'] ?? '');

    if ($username === '' || $email === '' || $fullname === '' || $password === '') {
        $errors[] = 'Semua field wajib diisi.';
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Format email tidak valid.';
    }

    if ($password !== $confirmPassword) {
        $errors[] = 'Konfirmasi password tidak cocok.';
    }

    if (strlen($password) < 6) {
        $errors[] = 'Password minimal 6 karakter.';
    }

    if (empty($errors)) {
        $checkStmt = mysqli_prepare($conn, 'SELECT id FROM users WHERE username = ? OR email = ? LIMIT 1');
        mysqli_stmt_bind_param($checkStmt, 'ss', $username, $email);
        mysqli_stmt_execute($checkStmt);
        $exists = mysqli_stmt_get_result($checkStmt)->fetch_assoc();

        if ($exists) {
            $errors[] = 'Username atau email sudah terdaftar.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $role = 'user';
            $status = 'active';

            $stmt = mysqli_prepare($conn, 'INSERT INTO users (username, email, password, fullname, role, status) VALUES (?, ?, ?, ?, ?, ?)');
            mysqli_stmt_bind_param($stmt, 'ssssss', $username, $email, $hash, $fullname, $role, $status);
            mysqli_stmt_execute($stmt);

            flash('success', 'Registrasi berhasil. Silakan login.');
            redirect('auth/login.php');
        }
    }
}
?>

<main class="container py-5" style="max-width: 640px;">
    <h1 class="h3 mb-4">Daftar Akun Pengunjung</h1>

    <?php foreach ($errors as $error): ?>
        <div class="alert alert-danger"><?= e($error) ?></div>
    <?php endforeach; ?>

    <form method="post" class="card card-body shadow-sm border-0">
        <div class="mb-3">
            <label class="form-label">Nama</label>
            <input type="text" name="fullname" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Username</label>
            <input type="text" name="username" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Konfirmasi Password</label>
            <input type="password" name="confirm_password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-success">Register</button>
    </form>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
