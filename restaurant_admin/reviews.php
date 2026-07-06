<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/helpers.php';

requireRole('restaurant_admin');
$pageTitle = 'Ulasan Restoran';

$user = currentUser();
$userId = (int)($user['id'] ?? 0);
$restaurant = mysqli_query($conn, "SELECT id, name FROM restaurants WHERE user_id = $userId LIMIT 1")->fetch_assoc();
if (!$restaurant) {
    die('Restoran tidak ditemukan.');
}
$restaurantId = (int)$restaurant['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'report') {
        $id = (int)($_POST['id'] ?? 0);
        $stmt = mysqli_prepare($conn, "UPDATE reviews SET status = 'reported' WHERE id = ? AND restaurant_id = ?");
        mysqli_stmt_bind_param($stmt, 'ii', $id, $restaurantId);
        mysqli_stmt_execute($stmt);
        redirect('restaurant_admin/reviews.php');
    }
}

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
$reviews = mysqli_query($conn, "
    SELECT rv.*, u.fullname
    FROM reviews rv
    INNER JOIN users u ON u.id = rv.user_id
    WHERE rv.restaurant_id = $restaurantId
    ORDER BY rv.created_at DESC
");
?>
<main class="container py-4">
    <div class="row g-4">
        <div class="col-lg-3"><?php require __DIR__ . '/../includes/sidebar.php'; ?></div>
        <div class="col-lg-9">
            <h1 class="h4 mb-3">Ulasan untuk <?= e($restaurant['name']) ?></h1>
            <div class="table-responsive">
                <table class="table table-striped align-middle">
                    <thead><tr><th>Pengunjung</th><th>Rating</th><th>Komentar</th><th>Status</th><th>Tanggal</th><th>Aksi</th></tr></thead>
                    <tbody>
                    <?php while ($r = mysqli_fetch_assoc($reviews)): ?>
                        <tr>
                            <td><?= e($r['fullname']) ?></td>
                            <td><?= (int)$r['rating'] ?></td>
                            <td><?= e($r['comment'] ?? '-') ?></td>
                            <td>
                                <?php if ($r['status'] === 'reported'): ?>
                                    <span class="badge bg-danger">Dilaporkan</span>
                                <?php elseif ($r['status'] === 'approved'): ?>
                                    <span class="badge bg-success">Publik</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary"><?= e(ucfirst($r['status'])) ?></span>
                                <?php endif; ?>
                            </td>
                            <td><?= e($r['created_at']) ?></td>
                            <td>
                                <?php if ($r['status'] === 'approved'): ?>
                                    <form method="post" onsubmit="return confirm('Laporkan ulasan ini ke admin?')">
                                        <input type="hidden" name="action" value="report">
                                        <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                                        <button class="btn btn-sm btn-outline-danger">Laporkan</button>
                                    </form>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
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
