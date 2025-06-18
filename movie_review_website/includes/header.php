<?php
// Start output buffering
ob_start();

// Include required files
require_once __DIR__ . '/session.php';
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Film İnceleme Sitesi</title>
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
                    <?php if (isLoggedIn() && isAdmin()): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="/movie_review_website/admin/dashboard.php">Yönetim Paneli</a>
                        </li>
                    <?php endif; ?>
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
                            <a class="nav-link" href="login.php">Giriş Yap</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="register.php">Kayıt Ol</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
</body>
</html> 