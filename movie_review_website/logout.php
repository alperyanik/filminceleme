<?php
require_once 'includes/session.php';

// Unset all session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Delete the remember me cookie if it exists
if (isset($_COOKIE['remember_user'])) {
    setcookie('remember_user', '', time() - 3600, '/');
}

// Redirect to login page
header("Location: login.php");
exit(); 