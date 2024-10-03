<?php
include('./config/database.php');

// Define the timeout duration (in seconds)
define('TIMEOUT_DURATION', 300); // 5 minutes

// Check if the user is logged in
if (!isset($_SESSION['user'])) {
    header('Location: ' . SITEURL . 'login.php');
    exit();
}

// Check the last activity time
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > TIMEOUT_DURATION)) {
    // Last request was more than 5 minutes ago
    session_unset(); // Unset session variables
    session_destroy(); // Destroy session
    header('Location: ' . SITEURL . 'login.php'); // Redirect to login page
    exit();
}

// Update last activity time
$_SESSION['LAST_ACTIVITY'] = time(); // Update last activity time
?>
