<?php
class User {
    private $conn;
    private $table_name = "users";

    public $id;
    public $username;
    public $email;
    public $password;
    public $is_admin;
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function register() {
        $query = "INSERT INTO " . $this->table_name . " (username, email, password) VALUES (?, ?, ?)";
        
        try {
            $stmt = $this->conn->prepare($query);
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $this->conn->error);
            }

            $hashed_password = password_hash($this->password, PASSWORD_DEFAULT);
            
            $stmt->bind_param("sss", $this->username, $this->email, $hashed_password);
            
            if($stmt->execute()) {
                return true;
            }
            return false;
        } catch(Exception $e) {
            echo "Registration error: " . $e->getMessage();
            return false;
        }
    }

    public function login() {
        $query = "SELECT id, username, password, is_admin FROM " . $this->table_name . " WHERE username = ?";
        
        try {
            $stmt = $this->conn->prepare($query);
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $this->conn->error);
            }

            $stmt->bind_param("s", $this->username);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                if (password_verify($this->password, $row['password'])) {
                    $this->id = $row['id'];
                    $this->username = $row['username'];
                    $this->is_admin = $row['is_admin'];
                    return true;
                }
            }
            return false;
        } catch(Exception $e) {
            echo "Login error: " . $e->getMessage();
            return false;
        }
    }

    public function checkRememberMe() {
        if(isset($_COOKIE['remember_user'])) {
            $token = $_COOKIE['remember_user'];
            $query = "SELECT user_id, username, is_admin 
                    FROM " . $this->table_name . " 
                    WHERE remember_token = :token";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":token", $token);
            $stmt->execute();

            if($stmt->rowCount() > 0) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                $_SESSION['user_id'] = $row['user_id'];
                $_SESSION['username'] = $row['username'];
                $_SESSION['is_admin'] = $row['is_admin'];
                return true;
            }
        }
        return false;
    }

    public function setRememberMe() {
        $token = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', strtotime('+30 days'));
        
        $query = "UPDATE " . $this->table_name . " SET remember_token = ?, token_expires = ? WHERE id = ?";
        
        try {
            $stmt = $this->conn->prepare($query);
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $this->conn->error);
            }

            $stmt->bind_param("ssi", $token, $expires, $this->id);
            
            if($stmt->execute()) {
                setcookie('remember_token', $token, strtotime('+30 days'), '/', '', true, true);
                return true;
            }
            return false;
        } catch(Exception $e) {
            echo "Remember me error: " . $e->getMessage();
            return false;
        }
    }

    public function getTotalCount() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name;
        $result = $this->conn->query($query);
        $row = $result->fetch_assoc();
        return $row['total'];
    }
}
?> 