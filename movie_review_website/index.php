<?php
// Start output buffering
ob_start();

// Include required files
require_once 'includes/session.php';
require_once 'config/database.php';
require_once 'classes/Movie.php';

// Initialize database connection
$database = new Database();
$db = $database->getConnection();
$movie = new Movie($db);

// Get movie list
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
if ($search !== '') {
    $stmt = $db->prepare("SELECT m.*, (SELECT AVG(rating) FROM reviews WHERE movie_id = m.id) as average_rating FROM movies m WHERE m.title LIKE ? ORDER BY m.created_at DESC");
    $like = "%$search%";
    $stmt->bind_param("s", $like);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $movie->read();
}

// Include header
include 'includes/header.php';
?>

<div class="container mt-4">
    <h1 class="mb-4">Film Listesi</h1>
    <form class="mb-4" method="get" action="" onsubmit="return false;">
        <div class="input-group">
            <input type="text" class="form-control" id="searchInput" name="search" placeholder="Film ara..." value="<?php echo htmlspecialchars($search); ?>" autocomplete="off">
            <button class="btn btn-primary" type="submit">Ara</button>
        </div>
    </form>
    
    <div class="row row-cols-1 row-cols-md-3 g-4">
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="col">
                <div class="card h-100">
                    <?php if ($row['poster']): ?>
                        <img src="<?php echo sanitizeOutput($row['poster']); ?>" class="card-img-top" alt="<?php echo sanitizeOutput($row['title']); ?>">
                    <?php else: ?>
                        <img src="assets/images/no-poster.jpg" class="card-img-top" alt="Poster Yok">
                    <?php endif; ?>
                    <div class="card-body">
                        <h5 class="card-title"><?php echo sanitizeOutput($row['title']); ?></h5>
                        <p class="card-text">
                            <?php 
                            $description = $row['description'];
                            echo strlen($description) > 100 ? substr($description, 0, 100) . '...' : $description;
                            ?>
                        </p>
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="rating">
                                <?php
                                $rating = round($row['average_rating'] ?? 0, 1);
                                for ($i = 1; $i <= 5; $i++) {
                                    if ($i <= $rating) {
                                        echo '<i class="fas fa-star text-warning"></i>';
                                    } else {
                                        echo '<i class="far fa-star text-warning"></i>';
                                    }
                                }
                                echo " ($rating)";
                                ?>
                            </div>
                            <a href="movie.php?id=<?php echo $row['id']; ?>" class="btn btn-primary">Detaylar</a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const cards = document.querySelectorAll('.card.h-100');
    searchInput.addEventListener('input', function() {
        const value = this.value.toLowerCase();
        cards.forEach(card => {
            const title = card.querySelector('.card-title').textContent.toLowerCase();
            if (title.includes(value)) {
                card.parentElement.style.display = '';
            } else {
                card.parentElement.style.display = 'none';
            }
        });
    });
});
</script>
</body>
</html> 