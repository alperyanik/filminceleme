<?php
require_once '../config/database.php';
require_once '../classes/Movie.php';
require_once '../includes/session.php';

requireAdmin();

$database = new Database();
$db = $database->getConnection();
$movie = new Movie($db);

$movie->id = isset($_GET['id']) ? $_GET['id'] : die('Film ID bulunamadı.');

// Get movie details before deleting
$movie->readOne();

if ($movie->delete()) {
    // Delete poster file if exists
    if ($movie->poster && file_exists("../" . $movie->poster)) {
        unlink("../" . $movie->poster);
    }
    header("Location: dashboard.php?msg=deleted");
} else {
    header("Location: dashboard.php?msg=error");
}
exit(); 