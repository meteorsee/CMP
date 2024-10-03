<?php
    session_start();
    
    // Define constants for connection parameters
    define('LOCALHOST', 'localhost');
    define('DB_USERNAME', 'root');
    define('DB_PASSWORD', '');
    define('DB_NAME', 'cmp');
    define('SITEURL', 'http://localhost/cmp_new/');

    // Create connection
    $conn = mysqli_connect(LOCALHOST, DB_USERNAME, DB_PASSWORD);

    // Check connection and handle error
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // Select database and handle error
    $db_select = mysqli_select_db($conn, DB_NAME);
    if (!$db_select) {
        die("Database selection failed: " . mysqli_error($conn));
    }
?>
