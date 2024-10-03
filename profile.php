<?php
include('./config/database.php');
include('./config/auth.php');

// Fetch current user data
$username = $_SESSION['user']; // Assuming this is the username stored in session
$role_id = $_SESSION['role_id']; // admin = 1, teacher = 2, student = 3

// Fetch the user data from the database
$query = "SELECT * FROM users WHERE username = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('s', $username);  // 's' for string since username is a string
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

$user_id = $user['id'];  // Get the user ID for updates

// Update Profile logic
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_username = $_POST['username']; // New username input
    $password = $_POST['password'];
    $target_file = $user['profile_image']; // Keep the old profile picture by default
    $profile_image_updated = false;

    // File upload for profile picture (teacher and student only)
    if ($role_id != 1 && isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == UPLOAD_ERR_OK) {
        $target_dir = "assets/uploads/";
        $target_file = $target_dir . basename($_FILES["profile_image"]["name"]);
        move_uploaded_file($_FILES["profile_image"]["tmp_name"], $target_file);
        $profile_image_updated = true; // Indicate that the profile image was updated
    }

    // Updating fields for teachers and students
    if ($role_id != 1) {
        $email = $_POST['email'];
        $description = $_POST['description'];

        if (!empty($password)) {
            // Update query for teachers and students with password
            $update_query = "UPDATE users SET username = ?, password = ?, profile_image = ?, email = ?, profile_desc = ? WHERE id = ?";
            $stmt = $conn->prepare($update_query);
            $stmt->bind_param('sssssi', $new_username, password_hash($password, PASSWORD_DEFAULT), $target_file, $email, $description, $user_id);
        } else {
            // Update query for teachers and students without password
            $update_query = "UPDATE users SET username = ?, profile_image = ?, email = ?, profile_desc = ? WHERE id = ?";
            $stmt = $conn->prepare($update_query);
            $stmt->bind_param('ssssi', $new_username, $target_file, $email, $description, $user_id);
        }
    } else {
        // Admin (password only)
        if (!empty($password)) {
            // Update query for admin with password
            $update_query = "UPDATE users SET username = ?, password = ? WHERE id = ?";
            $stmt = $conn->prepare($update_query);
            $stmt->bind_param('ssi', $new_username, password_hash($password, PASSWORD_DEFAULT), $user_id);
        } else {
            // Update username for admin without password
            $update_query = "UPDATE users SET username = ? WHERE id = ?";
            $stmt = $conn->prepare($update_query);
            $stmt->bind_param('si', $new_username, $user_id);
        }
    }

    // Execute and handle response
    if (isset($stmt) && $stmt->execute()) {
        // Check if profile image was updated
        if ($profile_image_updated) {
            // Update session for the profile image without logging out
            $_SESSION['profile_image'] = $target_file; // Update session with new profile image path
            echo "<script>alert('Profile image updated successfully');</script>";
        }
        
        // If password is updated, log out user
        if (!empty($password)) {
            // Clear session and redirect to login
            session_unset();
            session_destroy();
            header("Location: login.php"); // Redirect to the login page
            exit(); // Stop further execution
        }

        // Update session username
        $_SESSION['user'] = $new_username; // Update session username
        echo "<script>alert('Profile updated successfully');</script>";
        header("Location: profile.php");
        exit();  // Stop further execution after redirect
    } else {
        echo "<script>alert('Error updating profile');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <link rel="stylesheet" href="./assets/css/style.css">
    <script src="./assets/js/activity-timeout.php"></script>
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar (Col 2) -->
            <div class="col-2 p-0">
                <?php include('./config/sidebar.php'); ?>
            </div>

            <!-- Main Content (Col 10) -->
            <div class="col-10">
                <h1 class="my-4">Profile Page</h1>
                <form action="profile.php" method="post" enctype="multipart/form-data" class="row g-3">
                    <!-- Username -->
                    <div class="col-md-6">
                        <label for="username" class="form-label">Username:</label>
                        <input type="text" class="form-control" id="username" name="username" value="<?= htmlspecialchars($user['username']) ?>" required>
                    </div>

                    <!-- New Password -->
                    <div class="col-md-6">
                        <label for="password" class="form-label">New Password:</label>
                        <input type="password" class="form-control" id="password" name="password">
                    </div>

                    <?php if ($role_id != 1): ?>
                        <!-- Profile Picture (for Teachers and Students) -->
                        <div class="col-md-6">
                            <label for="profile_image" class="form-label">Profile Picture:</label>
                            <input type="file" class="form-control" id="profile_image" name="profile_image">
                        </div>

                        <!-- Email -->
                        <div class="col-md-6">
                            <label for="email" class="form-label">Email:</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
                        </div>

                        <!-- Description -->
                        <div class="col-md-12">
                            <label for="description" class="form-label">Description:</label>
                            <textarea class="form-control" id="description" name="description" rows="4" required><?= htmlspecialchars($user['profile_desc']) ?></textarea>
                        </div>
                    <?php endif; ?>

                    <!-- Submit Button -->
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">Update Profile</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>

</html>
