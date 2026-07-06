<?php

declare(strict_types=1);

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/helpers.php';

requireRole('user');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('restaurants.php');
}

$restaurantId = (int)($_POST['restaurant_id'] ?? 0);
$rating = (int)($_POST['rating'] ?? 0);
$comment = trim((string)($_POST['comment'] ?? ''));
$status = 'approved';
$user = currentUser();
$userId = (int)($user['id'] ?? 0);

if ($restaurantId <= 0 || $rating < 1 || $rating > 5 || $comment === '') {
    flash('error', 'Data ulasan tidak valid.');
    redirect('restaurant_detail.php?id=' . $restaurantId);
}

$stmt = mysqli_prepare($conn, 'INSERT INTO reviews (user_id, restaurant_id, rating, comment, status) VALUES (?, ?, ?, ?, ?)');
mysqli_stmt_bind_param($stmt, 'iiiss', $userId, $restaurantId, $rating, $comment, $status);
mysqli_stmt_execute($stmt);

flash('success', 'Ulasan berhasil dikirim.');
redirect('restaurant_detail.php?id=' . $restaurantId);
