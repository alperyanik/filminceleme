<?php
require_once '../config/database.php';
require_once '../classes/Movie.php';
require_once '../includes/session.php';

requireAdmin();

$database = new Database();
$db = $database->getConnection();
$movie = new Movie($db);

$message = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $release_year = intval($_POST['release_year'] ?? 0);
    $hasImage = isset($_FILES['poster']) && $_FILES['poster']['error'] == 0;
    $poster_url = '';

    // Klasörü her durumda oluştur
    $target_dir = "../uploads/posters/";
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    // Validasyon
    if ($title === '' || $description === '' || $release_year < 1900 || $release_year > intval(date('Y'))) {
        $message = 'Lütfen tüm alanları doğru şekilde doldurun.';
    } else if ($hasImage) {
        $allowed_types = ['image/jpeg', 'image/png'];
        if (!in_array($_FILES['poster']['type'], $allowed_types)) {
            $message = 'Sadece JPG ve PNG dosyaları yükleyebilirsiniz.';
        } else {
            $ext = strtolower(pathinfo($_FILES["poster"]["name"], PATHINFO_EXTENSION));
            $new_filename = uniqid('poster_', true) . '.' . $ext;
            $target_file = $target_dir . $new_filename;
            if (move_uploaded_file($_FILES["poster"]["tmp_name"], $target_file)) {
                $poster_url = "uploads/posters/" . $new_filename;
            } else {
                $message = 'Afiş yüklenirken bir hata oluştu.';
            }
        }
    }

    // Eğer afiş yüklenmediyse placeholder üret
    if ($message === '' && !$hasImage) {
        $initials = mb_strtoupper(mb_substr($title, 0, 1));
        $words = explode(' ', $title);
        if (count($words) > 1) {
            $initials .= mb_strtoupper(mb_substr($words[1], 0, 1));
        }
        // Basit bir SVG placeholder üret
        $svg = '<svg width="200" height="300" xmlns="http://www.w3.org/2000/svg"><rect width="100%" height="100%" fill="#888"/><text x="50%" y="55%" font-size="64" fill="#fff" text-anchor="middle" alignment-baseline="middle" font-family="Arial, sans-serif">' . $initials . '</text></svg>';
        $svg_file = $target_dir . uniqid('placeholder_', true) . '.svg';
        file_put_contents($svg_file, $svg);
        $poster_url = "uploads/posters/" . basename($svg_file);
    }

    if ($message === '') {
        $movie->title = $title;
        $movie->description = $description;
        $movie->release_year = $release_year;
        $movie->poster = $poster_url;
        if ($movie->create()) {
            $success = true;
            $message = 'Film başarıyla eklendi!';
        } else {
            $message = 'Film eklenirken bir hata oluştu.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yeni Film Ekle - Film İnceleme Sitesi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
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
                    <?php if (isLoggedIn() && isAdmin()): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="/movie_review_website/admin/dashboard.php">Yönetim Paneli</a>
                    </li>
                    <?php endif; ?>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="/movie_review_website/logout.php">Çıkış Yap</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="mb-0">Yeni Film Ekle</h3>
                    </div>
                    <div class="card-body">
                        <?php if ($message): ?>
                            <div class="alert alert-<?php echo $success ? 'success' : 'danger'; ?>"><?php echo $message; ?></div>
                        <?php endif; ?>
                        <form method="POST" action="" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="title" class="form-label">Film Başlığı</label>
                                <input type="text" class="form-control" id="title" name="title" required>
                            </div>
                            <div class="mb-3">
                                <label for="description" class="form-label">Film Açıklaması</label>
                                <textarea class="form-control" id="description" name="description" rows="4" required></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="release_year" class="form-label">Yayın Yılı</label>
                                <input type="number" class="form-control" id="release_year" name="release_year" min="1900" max="<?php echo date('Y'); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="poster" class="form-label">Film Afişi (isteğe bağlı)</label>
                                <input type="file" class="form-control" id="poster" name="poster" accept="image/jpeg,image/png">
                                <small class="text-muted">Sadece JPG veya PNG yükleyebilirsiniz. Yüklemezseniz otomatik placeholder oluşturulur.</small>
                            </div>
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">Film Ekle</button>
                                <a href="dashboard.php" class="btn btn-secondary">İptal</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 