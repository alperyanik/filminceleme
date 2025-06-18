-- Users tablosunu güncelle
ALTER TABLE users
ADD COLUMN remember_token VARCHAR(64) NULL,
ADD COLUMN token_expires DATETIME NULL,
ADD INDEX idx_remember_token (remember_token); 