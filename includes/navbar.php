<?php
$user = currentUser();
?>
<nav class="navbar navbar-expand-lg bg-white shadow-sm sticky-top">
    <div class="container">
        <a class="navbar-brand fw-bold text-success" href="<?= e(baseUrl()) ?>">Kuliner Lombok</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNavbar">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item"><a class="nav-link" href="<?= e(baseUrl()) ?>">Beranda</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= e(baseUrl('restaurants.php')) ?>">Katalog</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= e(baseUrl('about.php')) ?>">Tentang</a></li>
            </ul>
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                <?php if ($user): ?>
                    <?php if (($user['role'] ?? '') === 'admin'): ?>
                        <li class="nav-item"><a class="nav-link" href="<?= e(baseUrl('admin/index.php')) ?>">Admin Panel</a></li>
                    <?php elseif (($user['role'] ?? '') === 'restaurant_admin'): ?>
                        <li class="nav-item"><a class="nav-link" href="<?= e(baseUrl('restaurant_admin/index.php')) ?>">Panel Restoran</a></li>
                    <?php else: ?>
                        <li class="nav-item"><a class="nav-link" href="<?= e(baseUrl('user/profile.php')) ?>">Profil</a></li>
                    <?php endif; ?>
                    <li class="nav-item"><a class="nav-link" href="<?= e(baseUrl('auth/logout.php')) ?>">Logout</a></li>
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link" href="<?= e(baseUrl('auth/login.php')) ?>">Login</a></li>
                    <li class="nav-item"><a class="btn btn-success btn-sm mt-1" href="<?= e(baseUrl('auth/register.php')) ?>">Daftar</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
