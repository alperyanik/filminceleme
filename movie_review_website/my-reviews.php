<?php
require_once 'includes/session.php';
require_once 'config/database.php';
require_once 'classes/Review.php';

requireLogin();

$database = new Database();
$db = $database->getConnection();
$review = new Review($db);
$user_id = getCurrentUserId();

// Handle delete
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $review->id = (int)$_GET['delete'];
    $review->user_id = $user_id;
    if ($review->delete()) {
        header('Location: my-reviews.php?msg=deleted');
        exit;
    } else {
        $msg = 'Silme işlemi başarısız.';
    }
}

// Handle edit
if (isset($_POST['edit_id'])) {
    $review->id = (int)$_POST['edit_id'];
    $review->user_id = $user_id;
    $review->comment = $_POST['edit_comment'];
    $review->rating = (int)$_POST['edit_rating'];
    if ($review->update()) {
        header('Location: my-reviews.php?msg=updated');
        exit;
    } else {
        $msg = 'Güncelleme başarısız.';
    }
}

// Get user reviews
$reviews = $review->getUserReviews($user_id);
include 'includes/header.php';
?>
<div class="container mt-4">
    <h1 class="mb-4">İncelemelerim</h1>
    <?php if (isset($_GET['msg']) && $_GET['msg'] === 'deleted'): ?>
        <div class="alert alert-success">İnceleme silindi.</div>
    <?php elseif (isset($_GET['msg']) && $_GET['msg'] === 'updated'): ?>
        <div class="alert alert-success">İnceleme güncellendi.</div>
    <?php elseif (isset($msg)): ?>
        <div class="alert alert-danger"><?php echo $msg; ?></div>
    <?php endif; ?>
    <?php if ($reviews->num_rows > 0): ?>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Film</th>
                        <th>Puan</th>
                        <th>Yorum</th>
                        <th>Tarih</th>
                        <th>İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $reviews->fetch_assoc()): ?>
                        <?php if (isset($_GET['edit']) && $_GET['edit'] == $row['id']): ?>
                        <tr>
                            <form method="post" action="">
                                <td><?php echo sanitizeOutput($row['movie_title']); ?></td>
                                <td>
                                    <select name="edit_rating" class="form-select form-select-sm" required>
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <option value="<?php echo $i; ?>" <?php if ($row['rating'] == $i) echo 'selected'; ?>><?php echo $i; ?></option>
                                        <?php endfor; ?>
                                    </select>
                                </td>
                                <td>
                                    <textarea name="edit_comment" class="form-control form-control-sm" rows="2" required><?php echo htmlspecialchars($row['comment']); ?></textarea>
                                </td>
                                <td>
                                    <?php
                                    $created = strtotime($row['created_at']);
                                    $updated = isset($row['updated_at']) ? strtotime($row['updated_at']) : $created;
                                    if ($updated > $created) {
                                        echo date('d.m.Y H:i', $updated) . ' <span class="badge bg-info">Güncellendi</span>';
                                    } else {
                                        echo date('d.m.Y H:i', $created);
                                    }
                                    ?>
                                </td>
                                <td>
                                    <input type="hidden" name="edit_id" value="<?php echo $row['id']; ?>">
                                    <button type="submit" class="btn btn-sm btn-success">Kaydet</button>
                                    <a href="my-reviews.php" class="btn btn-sm btn-secondary">İptal</a>
                                </td>
                            </form>
                        </tr>
                        <?php else: ?>
                        <tr>
                            <td><?php echo sanitizeOutput($row['movie_title']); ?></td>
                            <td><?php echo (int)$row['rating']; ?> / 5</td>
                            <td><?php echo nl2br(sanitizeOutput($row['comment'])); ?></td>
                            <td>
                                <?php
                                $created = strtotime($row['created_at']);
                                $updated = isset($row['updated_at']) ? strtotime($row['updated_at']) : $created;
                                if ($updated > $created) {
                                    echo date('d.m.Y H:i', $updated) . ' <span class="badge bg-info">Güncellendi</span>';
                                } else {
                                    echo date('d.m.Y H:i', $created);
                                }
                                ?>
                            </td>
                            <td>
                                <a href="my-reviews.php?edit=<?php echo $row['id']; ?>" class="btn btn-sm btn-primary">Düzenle</a>
                                <a href="my-reviews.php?delete=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Bu incelemeyi silmek istediğinizden emin misiniz?');">Sil</a>
                            </td>
                        </tr>
                        <?php endif; ?>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="alert alert-info">Henüz hiç inceleme yapmadınız.</div>
    <?php endif; ?>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 