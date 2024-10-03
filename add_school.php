<?php
include('./config/session_timeout.php');
include('./config/auth.php');

// Check if user is logged in
if (!isset($_SESSION['user']) || $_SESSION['role_id'] != 1) {
    header('Location: ' . SITEURL . 'login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $school_name = mysqli_real_escape_string($conn, $_POST['school_name']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    
    // Insert the new school into the schools table
    $query = "INSERT INTO schools (name, address) VALUES ('$school_name', '$address')";
    if (mysqli_query($conn, $query)) {
        $_SESSION['success'] = "School added successfully!";
    } else {
        $_SESSION['error'] = "Error adding school: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add School</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="./assets/css/style.css">
    <script src="./assets/js/activity-timeout.php"></script>
    <script>
        window.onload = function() {
            <?php if (isset($_SESSION['success'])): ?>
                alert("<?php echo $_SESSION['success']; ?>");
                <?php unset($_SESSION['success']); ?>
                setTimeout(function() {
                    window.location.href = 'view_schools.php'; // Redirect after alert
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
            <div class="col-2 p-0">
                <?php include('./config/sidebar.php'); ?>
            </div>
            <div class="col-10">
                <h1 class="mt-4">Add School</h1>
                <form action="" method="POST" class="mt-3">
                    <div class="mb-3">
                        <label for="school_name" class="form-label">School Name:</label>
                        <input type="text" name="school_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="address" class="form-label">Address:</label>
                        <input type="text" name="address" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Add School</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
