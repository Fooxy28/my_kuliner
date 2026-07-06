<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/helpers.php';

requireRole('admin');
$pageTitle = 'Kelola Ulasan';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $id = (int)($_POST['id'] ?? 0);

    if ($action === 'approve') {
        $stmt = mysqli_prepare($conn, "UPDATE reviews SET status = 'approved' WHERE id = ?");
        mysqli_stmt_bind_param($stmt, 'i', $id);
        mysqli_stmt_execute($stmt);
    }

    if ($action === 'delete') {
        $stmt = mysqli_prepare($conn, 'DELETE FROM reviews WHERE id = ?');
        mysqli_stmt_bind_param($stmt, 'i', $id);
        mysqli_stmt_execute($stmt);
    }

    redirect('admin/reviews.php');
}

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';

$reviews = mysqli_query($conn, "
    SELECT rv.*, u.fullname, r.name AS restaurant_name
    FROM reviews rv
    INNER JOIN users u ON u.id = rv.user_id
    INNER JOIN restaurants r ON r.id = rv.restaurant_id
    WHERE rv.status = 'reported'
    ORDER BY rv.created_at DESC
");
?>
<main class="container py-4">
    <div class="row g-4">
        <div class="col-lg-3"><?php require __DIR__ . '/../includes/sidebar.php'; ?></div>
        <div class="col-lg-9">
            <h1 class="h4 mb-3">Manajemen Ulasan</h1>
            <div class="table-responsive">
                <table class="table table-striped align-middle">
                    <thead><tr><th>Pengguna</th><th>Restoran</th><th>Rating</th><th>Komentar</th><th>Status</th><th>Aksi</th></tr></thead>
                    <tbody>
                    <?php while ($rv = mysqli_fetch_assoc($reviews)): ?>
                        <tr class="<?= $rv['status'] === 'reported' ? 'table-warning' : '' ?>">
                            <td><?= e($rv['fullname']) ?></td>
                            <td><?= e($rv['restaurant_name']) ?></td>
                            <td><?= (int)$rv['rating'] ?></td>
                            <td><?= e($rv['comment'] ?? '-') ?></td>
                            <td>
                                <span class="badge bg-danger">Dilaporkan</span>
                            </td>
                            <td>
                                <div class="d-flex gap-2">
                                    <form method="post" onsubmit="return confirm('Setujui dan kembalikan ulasan ini?')">
                                        <input type="hidden" name="action" value="approve">
                                        <input type="hidden" name="id" value="<?= (int)$rv['id'] ?>">
                                        <button class="btn btn-sm btn-success">Approve</button>
                                    </form>
                                    <form method="post" onsubmit="return confirm('Hapus ulasan ini?')">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?= (int)$rv['id'] ?>">
                                        <button class="btn btn-sm btn-danger">Hapus</button>
                                    </form>
                                </div>
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
