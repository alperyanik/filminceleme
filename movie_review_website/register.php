<?php
// Start output buffering
ob_start();

// Include required files
require_once 'includes/session.php';
require_once 'config/database.php';
require_once 'classes/User.php';

// Initialize database connection
$database = new Database();
$db = $database->getConnection();
$user = new User($db);

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user->username = $_POST['username'];
    $user->email = $_POST['email'];
    $user->password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($user->password !== $confirm_password) {
        $message = "Şifreler eşleşmiyor!";
    } else {
        if ($user->register()) {
            header("Location: login.php?msg=registered");
            exit();
        } else {
            $message = "Kayıt işlemi başarısız oldu. Kullanıcı adı veya e-posta adresi zaten kullanımda olabilir.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kayıt Ol - Film İnceleme Sitesi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="text-center">Kayıt Ol</h3>
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
                                <label for="email" class="form-label">E-posta Adresi</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Şifre</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <div class="mb-3">
                                <label for="confirm_password" class="form-label">Şifre Tekrar</label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">Kayıt Ol</button>
                            </div>
                        </form>
                        <div class="mt-3 text-center">
                            <p>Zaten hesabınız var mı? <a href="login.php">Giriş Yap</a></p>
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