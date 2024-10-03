<?php
// Check if role is already set in session
if (!isset($_SESSION['role_id'])) {
    // Fetch role from the database
    $username = $_SESSION['user']; // Assuming this is the username stored in session

    // Prepare the query
    $query = "SELECT role_id FROM users WHERE username = ?";

    // Create a prepared statement
    if ($stmt = mysqli_prepare($conn, $query)) {
        // Bind parameters
        mysqli_stmt_bind_param($stmt, "s", $username);

        // Execute the statement
        mysqli_stmt_execute($stmt);

        // Store the result
        mysqli_stmt_store_result($stmt);

        // Check if a user was found
        if (mysqli_stmt_num_rows($stmt) == 1) {
            // Bind the result to a variable
            mysqli_stmt_bind_result($stmt, $_SESSION['role_id']);
            mysqli_stmt_fetch($stmt); // Fetch the result
        } else {
            // Handle case where user does not exist
            session_unset();
            session_destroy();
            header('Location: ' . SITEURL . 'login.php');
            exit();
        }

        // Close the statement
        mysqli_stmt_close($stmt);
    } else {
        // Handle error in preparing statement
        die("Database query error: " . mysqli_error($conn));
    }
}
?>
