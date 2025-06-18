<?php
require_once 'includes/session.php';
require_once 'config/database.php';
require_once 'classes/User.php';
require_once 'classes/Review.php';

requireLogin();

$database = new Database();
$db = $database->getConnection();

$user = new User($db);
$review = new Review($db);

// Get current user info
$user_id = getCurrentUserId();
$stmt = $db->prepare("SELECT username, email, created_at FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($username, $email, $created_at);
$stmt->fetch();
$stmt->close();

// Get user reviews
$reviews = $review->getUserReviews($user_id);

include 'includes/header.php';
?>
<div class="container mt-4">
    <h1 class="mb-4">Profilim</h1>
    <div class="card mb-4">
        <div class="card-body">
            <h4 class="card-title mb-3"><?php echo sanitizeOutput($username); ?></h4>
            <p><strong>Email:</strong> <?php echo sanitizeOutput($email); ?></p>
            <p><strong>Kayıt Tarihi:</strong> <?php echo date('d.m.Y H:i', strtotime($created_at)); ?></p>
        </div>
    </div>
    <h3 class="mb-3">Yorumlarım</h3>
    <?php if ($reviews->num_rows > 0): ?>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Film</th>
                        <th>Puan</th>
                        <th>Yorum</th>
                        <th>Tarih</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $reviews->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo sanitizeOutput($row['movie_title']); ?></td>
                            <td><?php echo (int)$row['rating']; ?> / 5</td>
                            <td><?php echo nl2br(sanitizeOutput($row['comment'])); ?></td>
                            <td><?php echo date('d.m.Y H:i', strtotime($row['created_at'])); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="alert alert-info">Henüz hiç yorum yapmadınız.</div>
    <?php endif; ?>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 