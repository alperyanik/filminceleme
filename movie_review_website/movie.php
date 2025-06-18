<?php
require_once 'includes/session.php';
require_once 'config/database.php';
require_once 'classes/Movie.php';
require_once 'classes/Review.php';

$database = new Database();
$db = $database->getConnection();

$movie = new Movie($db);
$review = new Review($db);

$movie->id = isset($_GET['id']) ? $_GET['id'] : die('Film ID bulunamadı.');
$movie->readOne();

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isLoggedIn()) {
    $review->movie_id = $movie->id;
    $review->user_id = getCurrentUserId();
    $review->comment = $_POST['review_text'];
    $review->rating = $_POST['rating'];

    if ($review->create()) {
        $message = "İncelemeniz başarıyla eklendi.";
    } else {
        $message = "Bu film için zaten bir inceleme yapmışsınız.";
    }
}

$review->movie_id = $movie->id;
$reviews = $review->getMovieReviews();
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo sanitizeOutput($movie->title); ?> - Film İnceleme Sitesi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="/movie_review_website/index.php">Film İnceleme Sitesi</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/movie_review_website/index.php">Ana Sayfa</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <?php if (isLoggedIn()): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user"></i> <?php echo sanitizeOutput(getCurrentUsername()); ?>
                                <?php if (isAdmin()): ?>
                                    <span class="badge bg-danger">Admin</span>
                                <?php endif; ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <?php if (isAdmin()): ?>
                                    <li>
                                        <a class="dropdown-item" href="/movie_review_website/admin/dashboard.php">
                                            <i class="fas fa-cog"></i> Yönetim Paneli
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                <?php endif; ?>
                                <li>
                                    <a class="dropdown-item" href="/movie_review_website/profile.php">
                                        <i class="fas fa-user-circle"></i> Profilim
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="/movie_review_website/my-reviews.php">
                                        <i class="fas fa-star"></i> İncelemelerim
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item text-danger" href="/movie_review_website/logout.php">
                                        <i class="fas fa-sign-out-alt"></i> Çıkış Yap
                                    </a>
                                </li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="/movie_review_website/login.php">Giriş Yap</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/movie_review_website/register.php">Kayıt Ol</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row">
            <div class="col-md-4">
                <?php if ($movie->poster): ?>
                    <img src="<?php echo sanitizeOutput($movie->poster); ?>" class="img-fluid rounded" alt="<?php echo sanitizeOutput($movie->title); ?>">
                <?php else: ?>
                    <img src="assets/images/no-poster.jpg" class="img-fluid rounded" alt="Poster Yok">
                <?php endif; ?>
            </div>
            <div class="col-md-8">
                <h1><?php echo sanitizeOutput($movie->title); ?></h1>
                <div class="rating mb-3">
                    <?php
                    $rating = round($movie->average_rating ?? 0, 1);
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
                <p class="lead"><?php echo sanitizeOutput($movie->description); ?></p>
                <p><strong>Yayın Yılı:</strong> <?php echo sanitizeOutput($movie->release_year); ?></p>
            </div>
        </div>

        <hr class="my-4">

        <?php if (isLoggedIn()): ?>
            <div class="card mb-4">
                <div class="card-header">
                    <h3>İnceleme Yap</h3>
                </div>
                <div class="card-body">
                    <?php if ($message): ?>
                        <div class="alert alert-info"><?php echo $message; ?></div>
                    <?php endif; ?>
                    
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label for="rating" class="form-label">Puanınız</label>
                            <select class="form-select" id="rating" name="rating" required>
                                <option value="">Puan seçin</option>
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <option value="<?php echo $i; ?>"><?php echo $i; ?> Yıldız</option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="review_text" class="form-label">İncelemeniz</label>
                            <textarea class="form-control" id="review_text" name="review_text" rows="3" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">İnceleme Gönder</button>
                    </form>
                </div>
            </div>
        <?php else: ?>
            <div class="alert alert-info">
                İnceleme yapabilmek için lütfen <a href="login.php">giriş yapın</a> veya <a href="register.php">kayıt olun</a>.
            </div>
        <?php endif; ?>

        <h3 class="mb-4">İncelemeler</h3>
        <?php while ($row = $reviews->fetch_assoc()): ?>
            <div class="card mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h5 class="card-title mb-0"><?php echo sanitizeOutput($row['username']); ?></h5>
                        <div class="rating">
                            <?php
                            for ($i = 1; $i <= 5; $i++) {
                                if ($i <= $row['rating']) {
                                    echo '<i class="fas fa-star text-warning"></i>';
                                } else {
                                    echo '<i class="far fa-star text-warning"></i>';
                                }
                            }
                            ?>
                        </div>
                    </div>
                    <p class="card-text"><?php echo nl2br(sanitizeOutput($row['comment'])); ?></p>
                    <small class="text-muted">
                        <?php
                        $created = strtotime($row['created_at']);
                        $updated = isset($row['updated_at']) ? strtotime($row['updated_at']) : $created;
                        if ($updated > $created) {
                            echo date('d.m.Y H:i', $updated) . ' <span class="badge bg-info">Güncellendi</span>';
                        } else {
                            echo date('d.m.Y H:i', $created);
                        }
                        ?>
                    </small>
                </div>
            </div>
        <?php endwhile; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 