<?php
include('./config/session_timeout.php');
include('./config/auth.php');
include('./config/check_role.php');

// Check if query parameter is set
if (isset($_GET['query'])) {
    $query = $_GET['query'];
    $query = mysqli_real_escape_string($conn, $query); // Sanitize input

    // Step 2: Prepare the SQL query
    $sql = "SELECT users.full_name, users.email FROM users 
            WHERE users.role_id = 2 AND users.full_name LIKE '%$query%';"; // Corrected LIKE syntax
    
    // Step 3: Execute the query and check for errors
    $result = mysqli_query($conn, $sql);
    if (!$result) {
        die('SQL Error: ' . mysqli_error($conn)); // Print SQL error if the query fails
    }

    // Step 5: Fetch and display each row of the result set
    if (mysqli_num_rows($result) > 0) {
        $id = 1;
        while ($row = mysqli_fetch_assoc($result)) { // Use mysqli_fetch_assoc
            // Output table rows
            echo '
                <tr>
                    <td>' . $id++ . '</td>
                    <td>' . htmlspecialchars($row['full_name']) . '</td>
                    <td>' . htmlspecialchars($row['email']) . '</td>
                </tr>';
        }
    } else {
        // No students found
        echo '<tr><td colspan="4">No teachers found.</td></tr>'; // Updated message
    }
}
?>
