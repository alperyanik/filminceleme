<?php
// Prevent any output before session start
if (ob_get_level()) ob_end_clean();
ob_start();

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Session timeout settings
$session_timeout = 600; // 10 minutes in seconds

// Check if session is expired
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $session_timeout)) {
    // Last request was more than 10 minutes ago
    session_unset();     // Unset $_SESSION variable for this page
    session_destroy();   // Destroy session data
    header("Location: login.php?msg=timeout");
    exit();
}

// Update last activity time stamp
$_SESSION['last_activity'] = time();

// Function to check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Function to check if user is admin
function isAdmin() {
    return isset($_SESSION['is_admin']) && ($_SESSION['is_admin'] == 1 || $_SESSION['is_admin'] === true);
}

// Function to require login
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: /movie_review_website/login.php');
        exit;
    }
}

// Function to require admin
function requireAdmin() {
    if (!isAdmin()) {
        header('Location: /movie_review_website/login.php');
        exit;
    }
}

// Function to get current user ID
function getCurrentUserId() {
    return $_SESSION['user_id'] ?? null;
}

// Function to get current username
function getCurrentUsername() {
    return $_SESSION['username'] ?? null;
}

// Function to sanitize output
function sanitizeOutput($text) {
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}
?> 