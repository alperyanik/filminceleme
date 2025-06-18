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
            $this->conn = new mysqli($this->host, $this->username, $this->password, $this->db_name);
            
            if ($this->conn->connect_error) {
                throw new Exception("Connection failed: " . $this->conn->connect_error);
            }
            
            $this->conn->set_charset("utf8");
        } catch(Exception $e) {
            echo "Database connection error: " . $e->getMessage();
        }

        return $this->conn;
    }
} 