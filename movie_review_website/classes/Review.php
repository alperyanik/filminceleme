<?php
class Review {
    private $conn;
    private $table_name = "reviews";

    public $id;
    public $movie_id;
    public $user_id;
    public $comment;
    public $rating;
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        // Check if user has already reviewed this movie
        if($this->hasUserReviewed()) {
            return false;
        }

        $query = "INSERT INTO " . $this->table_name . " 
                (movie_id, user_id, comment, rating) 
                VALUES (?, ?, ?, ?)";

        $stmt = $this->conn->prepare($query);

        $this->comment = htmlspecialchars(strip_tags($this->comment));
        $rating = (int)$this->rating;

        $stmt->bind_param("iisi", $this->movie_id, $this->user_id, $this->comment, $rating);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function hasUserReviewed() {
        $query = "SELECT id FROM " . $this->table_name . " 
                WHERE movie_id = ? AND user_id = ?";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ii", $this->movie_id, $this->user_id);
        $stmt->execute();

        $stmt->store_result();
        return $stmt->num_rows > 0;
    }

    public function getMovieReviews() {
        $query = "SELECT r.*, u.username, r.updated_at 
                FROM " . $this->table_name . " r 
                LEFT JOIN users u ON r.user_id = u.id 
                WHERE r.movie_id = ? 
                ORDER BY r.created_at DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $this->movie_id);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function getAverageRating() {
        $query = "SELECT AVG(rating) as average_rating 
                FROM " . $this->table_name . " 
                WHERE movie_id = ?";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $this->movie_id);
        $stmt->execute();

        $row = $stmt->fetch_assoc();
        return $row['average_rating'] ? round($row['average_rating'], 1) : 0;
    }

    public function update() {
        $query = "UPDATE " . $this->table_name . " 
                SET comment = ?, 
                    rating = ?, 
                    updated_at = CURRENT_TIMESTAMP 
                WHERE id = ? 
                AND user_id = ?";

        $stmt = $this->conn->prepare($query);

        $this->comment = htmlspecialchars(strip_tags($this->comment));
        $rating = (int)$this->rating;

        $stmt->bind_param("siii", $this->comment, $rating, $this->id, $this->user_id);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " 
                WHERE id = ? 
                AND user_id = ?";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ii", $this->id, $this->user_id);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function getUserReviews($user_id) {
        $query = "SELECT r.*, m.title as movie_title, r.updated_at FROM " . $this->table_name . " r LEFT JOIN movies m ON r.movie_id = m.id WHERE r.user_id = ? ORDER BY r.created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        return $stmt->get_result();
    }
}
?> 