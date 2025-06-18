<?php
require_once '../config/database.php';
require_once '../classes/Movie.php';
require_once '../includes/session.php';

requireAdmin();

$database = new Database();
$db = $database->getConnection();
$movie = new Movie($db);

$movie->id = isset($_GET['id']) ? $_GET['id'] : die('Film ID bulunamadı.');
$movie->readOne();

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $movie->title = $_POST['title'];
    $movie->description = $_POST['description'];
    $movie->release_year = $_POST['release_year'];
    
    // Handle file upload
    if (isset($_FILES['poster']) && $_FILES['poster']['error'] == 0) {
        $target_dir = "../uploads/posters/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        $file_extension = strtolower(pathinfo($_FILES["poster"]["name"], PATHINFO_EXTENSION));
        $new_filename = uniqid() . '.' . $file_extension;
        $target_file = $target_dir . $new_filename;
        
        // Check if image file is a actual image
        $check = getimagesize($_FILES["poster"]["tmp_name"]);
        if ($check !== false) {
            if (move_uploaded_file($_FILES["poster"]["tmp_name"], $target_file)) {
                // Delete old poster if exists
                if ($movie->poster && file_exists("../" . $movie->poster)) {
                    unlink("../" . $movie->poster);
                }
                $movie->poster = "uploads/posters/" . $new_filename;
            } else {
                $message = "Dosya yüklenirken bir hata oluştu.";
            }
        } else {
            $message = "Dosya bir resim değil.";
        }
    }

    if (empty($message)) {
        if ($movie->update()) {
            header("Location: dashboard.php?msg=updated");
            exit();
        } else {
            $message = "Film güncellenirken bir hata oluştu.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Film Düzenle - Film İnceleme Sitesi</title>
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
                        <h3 class="mb-0">Film Düzenle</h3>
                    </div>
                    <div class="card-body">
                        <?php if ($message): ?>
                            <div class="alert alert-danger"><?php echo $message; ?></div>
                        <?php endif; ?>

                        <form method="POST" action="" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="title" class="form-label">Film Başlığı</label>
                                <input type="text" class="form-control" id="title" name="title" value="<?php echo sanitizeOutput($movie->title); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="description" class="form-label">Film Açıklaması</label>
                                <textarea class="form-control" id="description" name="description" rows="4" required><?php echo sanitizeOutput($movie->description); ?></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="release_year" class="form-label">Yayın Yılı</label>
                                <input type="number" class="form-control" id="release_year" name="release_year" value="<?php echo sanitizeOutput($movie->release_year); ?>" min="1900" max="<?php echo date('Y'); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="poster" class="form-label">Film Posteri</label>
                                <?php if ($movie->poster): ?>
                                    <div class="mb-2">
                                        <img src="../<?php echo sanitizeOutput($movie->poster); ?>" alt="Mevcut Poster" style="max-height: 200px;">
                                    </div>
                                <?php endif; ?>
                                <input type="file" class="form-control" id="poster" name="poster" accept="image/*">
                                <small class="text-muted">Yeni bir poster yüklemezseniz mevcut poster korunacaktır.</small>
                            </div>
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">Değişiklikleri Kaydet</button>
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