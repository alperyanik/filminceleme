<?php
// Start output buffering
ob_start();

// Include required files
require_once 'includes/session.php';
require_once 'config/database.php';
require_once 'classes/User.php';

// If user is already logged in, redirect to appropriate page
if (isLoggedIn()) {
    if (isAdmin()) {
        header("Location: admin/dashboard.php");
    } else {
        header("Location: index.php");
    }
    exit();
}

// Initialize database connection
$database = new Database();
$db = $database->getConnection();
$user = new User($db);

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user->username = $_POST['username'];
    $user->password = $_POST['password'];

    if ($user->login()) {
        $_SESSION['user_id'] = $user->id;
        $_SESSION['username'] = $user->username;
        $_SESSION['is_admin'] = $user->is_admin;

        if (isset($_POST['remember_me'])) {
            $user->setRememberMe();
        }

        // Redirect based on user type
        if ($user->is_admin) {
            header("Location: admin/dashboard.php");
        } else {
            header("Location: index.php");
        }
        exit();
    } else {
        $message = "Geçersiz kullanıcı adı veya şifre!";
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giriş Yap - Film İnceleme Sitesi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="text-center">Giriş Yap</h3>
                    </div>
                    <div class="card-body">
                        <?php if ($message): ?>
                            <div class="alert alert-danger"><?php echo $message; ?></div>
                        <?php endif; ?>
                        
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="username" class="form-label">Kullanıcı Adı</label>
                                <input type="text" class="form-control" id="username" name="username" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Şifre</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="remember_me" name="remember_me">
                                <label class="form-check-label" for="remember_me">Beni Hatırla</label>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">Giriş Yap</button>
                            </div>
                        </form>
                        <div class="mt-3 text-center">
                            <p>Hesabınız yok mu? <a href="register.php">Kayıt Ol</a></p>
                            <p><a href="index.php">Ana Sayfaya Dön</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 