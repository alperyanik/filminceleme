-- Admin kullanıcısı için şifre: Admin123!
-- Şifre PHP'de password_hash() ile hashlenmiş hali
INSERT INTO users (username, email, password, is_admin) 
VALUES (
    'admin',
    'admin@movie-review.com',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    1
) ON DUPLICATE KEY UPDATE 
    is_admin = 1; 