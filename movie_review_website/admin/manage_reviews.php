<?php
require_once '../includes/session.php';
require_once '../config/database.php';
require_once '../classes/Review.php';

if (!isLoggedIn() || !isAdmin()) {
    header('Location: ../login.php');
    exit;
}

$database = new Database();
$db = $database->getConnection();
$review = new Review($db);

// Handle delete
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $review->id = (int)$_GET['delete'];
    // Find user_id for this review
    $stmt = $db->prepare("SELECT user_id FROM reviews WHERE id = ?");
    $stmt->bind_param("i", $review->id);
    $stmt->execute();
    $stmt->bind_result($user_id);
    $stmt->fetch();
    $stmt->close();
    if ($user_id) {
        $review->user_id = $user_id;
        if ($review->delete()) {
            header('Location: manage_reviews.php?msg=deleted');
            exit;
        } else {
            $msg = 'Silme işlemi başarısız.';
        }
    }
}
// Get all reviews
$sql = "SELECT r.id, r.comment, r.rating, r.created_at, m.title as movie_title, u.username FROM reviews r LEFT JOIN movies m ON r.movie_id = m.id LEFT JOIN users u ON r.user_id = u.id ORDER BY r.created_at DESC";
$result = $db->query($sql);
include '../includes/header.php';
?>
<div class="container mt-4">
    <h1 class="mb-4">İncelemeleri Yönet</h1>
    <?php if (isset($_GET['msg']) && $_GET['msg'] === 'deleted'): ?>
        <div class="alert alert-success">İnceleme silindi.</div>
    <?php elseif (isset($msg)): ?>
        <div class="alert alert-danger"><?php echo $msg; ?></div>
    <?php endif; ?>
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Film</th>
                    <th>Kullanıcı</th>
                    <th>Puan</th>
                    <th>Yorum</th>
                    <th>Tarih</th>
                    <th>İşlemler</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo sanitizeOutput($row['movie_title']); ?></td>
                        <td><?php echo sanitizeOutput($row['username']); ?></td>
                        <td><?php echo (int)$row['rating']; ?> / 5</td>
                        <td><?php echo nl2br(sanitizeOutput($row['comment'])); ?></td>
                        <td><?php echo date('d.m.Y H:i', strtotime($row['created_at'])); ?></td>
                        <td>
                            <a href="manage_reviews.php?delete=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Bu incelemeyi silmek istediğinizden emin misiniz?');">Sil</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 