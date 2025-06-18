<?php
require_once '../includes/session.php';
require_once '../config/database.php';
require_once '../classes/User.php';
require_once '../classes/Review.php';

if (!isLoggedIn() || !isAdmin()) {
    header('Location: ../login.php');
    exit;
}

$database = new Database();
$db = $database->getConnection();
$user = new User($db);
$review = new Review($db);

// Handle promote/demote
if (isset($_GET['toggle_admin']) && is_numeric($_GET['toggle_admin'])) {
    $uid = (int)$_GET['toggle_admin'];
    if ($uid !== getCurrentUserId()) {
        $stmt = $db->prepare("UPDATE users SET is_admin = IF(is_admin=1,0,1) WHERE id = ?");
        $stmt->bind_param("i", $uid);
        $stmt->execute();
        $stmt->close();
        header('Location: manage_users.php?msg=admin');
        exit;
    }
}
// Handle delete
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $uid = (int)$_GET['delete'];
    if ($uid !== getCurrentUserId()) {
        // Delete user's reviews first
        $stmt = $db->prepare("DELETE FROM reviews WHERE user_id = ?");
        $stmt->bind_param("i", $uid);
        $stmt->execute();
        $stmt->close();
        // Delete user
        $stmt = $db->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $uid);
        $stmt->execute();
        $stmt->close();
        header('Location: manage_users.php?msg=deleted');
        exit;
    }
}
// Get all users
$result = $db->query("SELECT id, username, email, is_admin, created_at FROM users ORDER BY created_at DESC");
include '../includes/header.php';
?>
<div class="container mt-4">
    <h1 class="mb-4">Kullanıcıları Yönet</h1>
    <?php if (isset($_GET['msg']) && $_GET['msg'] === 'admin'): ?>
        <div class="alert alert-success">Kullanıcı admin yetkisi değiştirildi.</div>
    <?php elseif (isset($_GET['msg']) && $_GET['msg'] === 'deleted'): ?>
        <div class="alert alert-success">Kullanıcı silindi.</div>
    <?php endif; ?>
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Kullanıcı Adı</th>
                    <th>Email</th>
                    <th>Kayıt Tarihi</th>
                    <th>Yetki</th>
                    <th>İncelemeler</th>
                    <th>İşlemler</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo sanitizeOutput($row['username']); ?></td>
                        <td><?php echo sanitizeOutput($row['email']); ?></td>
                        <td><?php echo date('d.m.Y H:i', strtotime($row['created_at'])); ?></td>
                        <td>
                            <?php if ($row['is_admin']): ?>
                                <span class="badge bg-danger">Admin</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Kullanıcı</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-info" type="button" data-bs-toggle="collapse" data-bs-target="#reviews<?php echo $row['id']; ?>">Görüntüle</button>
                        </td>
                        <td>
                            <?php if ($row['id'] !== getCurrentUserId()): ?>
                                <a href="manage_users.php?delete=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Bu kullanıcıyı ve tüm incelemelerini silmek istediğinize emin misiniz?');">Sil</a>
                            <?php else: ?>
                                <span class="text-muted">(Kendiniz)</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr class="collapse" id="reviews<?php echo $row['id']; ?>">
                        <td colspan="6">
                            <?php
                            $user_reviews = $review->getUserReviews($row['id']);
                            if ($user_reviews->num_rows > 0): ?>
                                <ul class="list-group">
                                    <?php while ($rev = $user_reviews->fetch_assoc()): ?>
                                        <li class="list-group-item">
                                            <strong><?php echo sanitizeOutput($rev['movie_title']); ?></strong> -
                                            <?php echo (int)$rev['rating']; ?>/5<br>
                                            <span><?php echo nl2br(sanitizeOutput($rev['comment'])); ?></span>
                                            <small class="text-muted float-end"><?php echo date('d.m.Y H:i', strtotime($rev['created_at'])); ?></small>
                                        </li>
                                    <?php endwhile; ?>
                                </ul>
                            <?php else: ?>
                                <span class="text-muted">İnceleme yok.</span>
                            <?php endif; ?>
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