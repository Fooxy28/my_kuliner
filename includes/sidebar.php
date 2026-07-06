<?php
$user = currentUser();
$role = $user['role'] ?? '';
?>
<div class="list-group mb-3">
    <?php if ($role === 'admin'): ?>
        <a href="<?= e(baseUrl('admin/index.php')) ?>" class="list-group-item list-group-item-action">Dashboard</a>
        <a href="<?= e(baseUrl('admin/users.php')) ?>" class="list-group-item list-group-item-action">Kelola User</a>
        <a href="<?= e(baseUrl('admin/restaurants.php')) ?>" class="list-group-item list-group-item-action">Kelola Restoran</a>
        <a href="<?= e(baseUrl('admin/menus.php')) ?>" class="list-group-item list-group-item-action">Kelola Menu</a>
        <a href="<?= e(baseUrl('admin/gallery.php')) ?>" class="list-group-item list-group-item-action">Kelola Galeri</a>
        <a href="<?= e(baseUrl('admin/reviews.php')) ?>" class="list-group-item list-group-item-action">Kelola Ulasan</a>
    <?php elseif ($role === 'restaurant_admin'): ?>
        <a href="<?= e(baseUrl('restaurant_admin/index.php')) ?>" class="list-group-item list-group-item-action">Dashboard</a>
        <a href="<?= e(baseUrl('restaurant_admin/profile.php')) ?>" class="list-group-item list-group-item-action">Profil Restoran</a>
        <a href="<?= e(baseUrl('restaurant_admin/menus.php')) ?>" class="list-group-item list-group-item-action">Menu</a>
        <a href="<?= e(baseUrl('restaurant_admin/gallery.php')) ?>" class="list-group-item list-group-item-action">Galeri</a>
        <a href="<?= e(baseUrl('restaurant_admin/reviews.php')) ?>" class="list-group-item list-group-item-action">Ulasan</a>
    <?php endif; ?>
</div>
