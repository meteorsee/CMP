<?php
include('./config/session_timeout.php');
include('./config/auth.php');

// Check if user is logged in
if (!isset($_SESSION['user']) || $_SESSION['role_id'] != 1) { // Ensure admin access
    header('Location: ' . SITEURL . 'login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $teacher_name = mysqli_real_escape_string($conn, $_POST['teacher_name']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $gender = mysqli_real_escape_string($conn, $_POST['gender']);
    $profile_desc = mysqli_real_escape_string($conn, $_POST['profile_desc']);
    // Set profile image to NULL
    $profile_image = NULL; // Default value
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $school_id = mysqli_real_escape_string($conn, $_POST['school_id']); // Get selected school

    // Hash the password before storing
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert the new teacher into the users table
    $query = "INSERT INTO users (username, password, role_id, school_id, full_name, gender, profile_desc, profile_image, email) 
              VALUES ('$username', '$hashed_password', 2, '$school_id', '$teacher_name', '$gender', '$profile_desc', NULL, '$email')";
    if (mysqli_query($conn, $query)) {
        $_SESSION['success'] = "Teacher added successfully!";
    } else {
        $_SESSION['error'] = "Error adding teacher: " . mysqli_error($conn);
    }
}

// Fetch schools for dropdown
$schools_query = "SELECT * FROM schools";
$schools_result = mysqli_query($conn, $schools_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Teacher</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="./assets/css/style.css">
    <script src="./assets/js/activity-timeout.php"></script>
    <script>
        window.onload = function () {
            <?php if (isset($_SESSION['success'])): ?>
                alert("<?php echo $_SESSION['success']; ?>");
                <?php unset($_SESSION['success']); ?>
                setTimeout(function () {
                    window.location.href = 'view_teachers.php'; // Redirect after alert
                }, 2000); // Redirect after 2 seconds
            <?php elseif (isset($_SESSION['error'])): ?>
                alert("<?php echo $_SESSION['error']; ?>");
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>
        }
    </script>
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
                <h1 class="mt-4">Add Teacher</h1>
                <form action="" method="POST" class="mt-3">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username:</label>
                        <input type="text" name="username" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="teacher_name" class="form-label">Teacher Name:</label>
                        <input type="text" name="teacher_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email:</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="gender" class="form-label">Gender:</label>
                        <select name="gender" class="form-select" required>
                            <option value="">Select Gender</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="profile_desc" class="form-label">Profile Description:</label>
                        <textarea name="profile_desc" class="form-control"></textarea>
                    </div>
                    <!-- Hidden input for profile image -->
                    <input type="hidden" name="profile_image" value="NULL">
                    <div class="mb-3">
                        <label for="password" class="form-label">Password:</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="school_id" class="form-label">Select School:</label>
                        <select name="school_id" class="form-select" required>
                            <?php while ($school = mysqli_fetch_assoc($schools_result)): ?>
                                <option value="<?php echo $school['id']; ?>"><?php echo $school['name']; ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Add Teacher</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
