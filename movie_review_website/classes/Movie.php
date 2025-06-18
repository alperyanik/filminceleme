<?php
class Movie {
    private $conn;
    private $table_name = "movies";

    public $id;
    public $title;
    public $description;
    public $release_year;
    public $poster;
    public $created_at;
    public $average_rating;

    public function __construct($db) {
        if (!$db) {
            throw new Exception("Database connection is required");
        }
        $this->conn = $db;
    }

    public function read() {
        if (!$this->conn) {
            throw new Exception("Database connection is not available");
        }

        $query = "SELECT m.*, 
                    (SELECT AVG(rating) FROM reviews WHERE movie_id = m.id) as average_rating 
                 FROM " . $this->table_name . " m 
                 ORDER BY m.created_at DESC";

        try {
            $result = $this->conn->query($query);
            if (!$result) {
                throw new Exception("Query error: " . $this->conn->error);
            }
            return $result;
        } catch(Exception $e) {
            echo "Query error: " . $e->getMessage();
            return false;
        }
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . "
                (title, description, release_year, poster)
                VALUES
                (?, ?, ?, ?)";

        try {
            $stmt = $this->conn->prepare($query);
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $this->conn->error);
            }

            $stmt->bind_param("ssis", $this->title, $this->description, $this->release_year, $this->poster);

            if($stmt->execute()) {
                return true;
            }
            return false;
        } catch(Exception $e) {
            echo "Film ekleme hatası: " . $e->getMessage();
            return false;
        }
    }

    public function readOne() {
        $query = "SELECT m.*, 
                    (SELECT AVG(rating) FROM reviews WHERE movie_id = m.id) as average_rating 
                 FROM " . $this->table_name . " m
                 WHERE m.id = ?";

        try {
            $stmt = $this->conn->prepare($query);
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $this->conn->error);
            }

            $stmt->bind_param("i", $this->id);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();

            if($row) {
                $this->title = $row['title'];
                $this->description = $row['description'];
                $this->release_year = $row['release_year'];
                $this->poster = $row['poster'];
                $this->created_at = $row['created_at'];
                $this->average_rating = $row['average_rating'];
                return true;
            }
            return false;
        } catch(Exception $e) {
            echo "Film okuma hatası: " . $e->getMessage();
            return false;
        }
    }

    public function update() {
        $query = "UPDATE " . $this->table_name . "
                SET
                    title = ?,
                    description = ?,
                    release_year = ?,
                    poster = ?
                WHERE
                    id = ?";

        try {
            $stmt = $this->conn->prepare($query);
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $this->conn->error);
            }

            $stmt->bind_param("ssisi", $this->title, $this->description, $this->release_year, $this->poster, $this->id);

            if($stmt->execute()) {
                return true;
            }
            return false;
        } catch(Exception $e) {
            echo "Film güncelleme hatası: " . $e->getMessage();
            return false;
        }
    }

    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";

        try {
            $stmt = $this->conn->prepare($query);
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $this->conn->error);
            }

            $stmt->bind_param("i", $this->id);

            if($stmt->execute()) {
                return true;
            }
            return false;
        } catch(Exception $e) {
            echo "Film silme hatası: " . $e->getMessage();
            return false;
        }
    }

    public function getTotalCount() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name;
        $result = $this->conn->query($query);
        $row = $result->fetch_assoc();
        return $row['total'];
    }

    public function getTotalReviews() {
        $query = "SELECT COUNT(*) as total FROM reviews";
        $result = $this->conn->query($query);
        $row = $result->fetch_assoc();
        return $row['total'];
    }

    public function getRecent($limit = 5) {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY created_at DESC LIMIT ?";
        
        try {
            $stmt = $this->conn->prepare($query);
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $this->conn->error);
            }

            $stmt->bind_param("i", $limit);
            $stmt->execute();
            return $stmt->get_result();
        } catch(Exception $e) {
            echo "Query error: " . $e->getMessage();
            return false;
        }
    }
} 