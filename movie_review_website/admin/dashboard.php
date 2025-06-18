<?php
// Start output buffering
ob_start();

// Include required files
require_once '../includes/session.php';
require_once '../config/database.php';
require_once '../classes/Movie.php';
require_once '../classes/User.php';

// Check if user is logged in and is admin
if (!isLoggedIn()) {
    header("Location: ../login.php");
    exit();
}

if (!isAdmin()) {
    header("Location: ../index.php");
    exit();
}

// Initialize database connection
$database = new Database();
$db = $database->getConnection();
$movie = new Movie($db);
$user = new User($db);

// Get statistics
$total_movies = $movie->getTotalCount();
$total_users = $user->getTotalCount();
$total_reviews = $movie->getTotalReviews();

// Get all movies
$all_movies = $movie->read();

// Set page title
$page_title = "Yönetim Paneli";
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - Film İnceleme Sitesi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="../index.php">Film İnceleme Sitesi</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="../index.php">Ana Sayfa</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="dashboard.php">Yönetim Paneli</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user"></i> <?php echo sanitizeOutput(getCurrentUsername()); ?>
                            <span class="badge bg-danger">Admin</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item" href="dashboard.php">
                                    <i class="fas fa-cog"></i> Yönetim Paneli
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item" href="../profile.php">
                                    <i class="fas fa-user-circle"></i> Profilim
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="../my-reviews.php">
                                    <i class="fas fa-star"></i> İncelemelerim
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item text-danger" href="../logout.php">
                                    <i class="fas fa-sign-out-alt"></i> Çıkış Yap
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h1 class="mb-4"><?php echo $page_title; ?></h1>
        
        <!-- İstatistikler -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <h5 class="card-title">Toplam Film</h5>
                        <p class="card-text display-4"><?php echo $total_movies; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <h5 class="card-title">Toplam Kullanıcı</h5>
                        <p class="card-text display-4"><?php echo $total_users; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <h5 class="card-title">Toplam İnceleme</h5>
                        <p class="card-text display-4"><?php echo $total_reviews; ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Hızlı İşlemler -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Hızlı İşlemler</h5>
                    </div>
                    <div class="card-body">
                        <a href="add_movie.php" class="btn btn-primary me-2">
                            <i class="fas fa-plus"></i> Yeni Film Ekle
                        </a>
                        <a href="manage_users.php" class="btn btn-secondary me-2">
                            <i class="fas fa-users"></i> Kullanıcıları Yönet
                        </a>
                        <a href="manage_reviews.php" class="btn btn-info">
                            <i class="fas fa-comments"></i> İncelemeleri Yönet
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Son Eklenen Filmler -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Son Eklenen Filmler</h5>
                        <form class="mb-0" onsubmit="return false;">
                            <input type="text" class="form-control form-control-sm" id="adminMovieSearch" placeholder="Film ara..." autocomplete="off" style="width: 200px;">
                        </form>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped" id="adminMoviesTable">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Film Adı</th>
                                        <th>Yıl</th>
                                        <th>Eklenme Tarihi</th>
                                        <th>İşlemler</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($row = $all_movies->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo $row['id']; ?></td>
                                        <td class="movie-title-cell"><?php echo sanitizeOutput($row['title']); ?></td>
                                        <td><?php echo $row['release_year']; ?></td>
                                        <td><?php echo date('d.m.Y H:i', strtotime($row['created_at'])); ?></td>
                                        <td>
                                            <a href="edit_movie.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-primary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="delete_movie.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Bu filmi silmek istediğinizden emin misiniz?')">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('adminMovieSearch');
        const table = document.getElementById('adminMoviesTable');
        if (searchInput && table) {
            searchInput.addEventListener('input', function() {
                const value = this.value.toLowerCase();
                const rows = table.querySelectorAll('tbody tr');
                rows.forEach(row => {
                    const titleCell = row.querySelector('.movie-title-cell');
                    if (titleCell && titleCell.textContent.toLowerCase().includes(value)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });
        }
    });
    </script>
</body>
</html> 