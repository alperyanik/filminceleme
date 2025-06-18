-- Create database
CREATE DATABASE movie_review_db;
GO

USE movie_review_db;
GO

-- Users table
CREATE TABLE users (
    user_id INT IDENTITY(1,1) PRIMARY KEY,
    username NVARCHAR(50) NOT NULL UNIQUE,
    email NVARCHAR(100) NOT NULL UNIQUE,
    password NVARCHAR(255) NOT NULL,
    is_admin BIT DEFAULT 0,
    created_at DATETIME DEFAULT GETDATE()
);

-- Movies table
CREATE TABLE movies (
    movie_id INT IDENTITY(1,1) PRIMARY KEY,
    title NVARCHAR(200) NOT NULL,
    description NVARCHAR(MAX),
    poster_url NVARCHAR(255),
    release_year INT,
    created_at DATETIME DEFAULT GETDATE(),
    updated_at DATETIME DEFAULT GETDATE()
);

-- Reviews table
CREATE TABLE reviews (
    review_id INT IDENTITY(1,1) PRIMARY KEY,
    movie_id INT FOREIGN KEY REFERENCES movies(movie_id),
    user_id INT FOREIGN KEY REFERENCES users(user_id),
    review_text NVARCHAR(MAX),
    rating INT CHECK (rating >= 1 AND rating <= 5),
    created_at DATETIME DEFAULT GETDATE(),
    UNIQUE(movie_id, user_id)
);

-- Insert default admin user (password: Admin123!)
INSERT INTO users (username, email, password, is_admin)
VALUES ('admin', 'admin@movie.com', '$2y$10$8K1p/a0dR1Ux5Y5Y5Y5Y5O5Y5Y5Y5Y5Y5Y5Y5Y5Y5Y5Y5Y5Y5Y', 1); 