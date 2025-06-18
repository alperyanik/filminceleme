<?php
class Database {
    private $host = "localhost";
    private $db_name = "movie_review_db";
    private $username = "root";
    private $password = ""; // Default XAMPP password is empty
    public $conn;

    public function getConnection() {
        $this->conn = null;

        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name,
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->exec("set names utf8");
        } catch(PDOException $e) {
            echo "Database connection error: " . $e->getMessage();
        }

        return $this->conn;
    }
}
?> 