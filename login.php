<?php
include('./config/database.php');

// If user is already logged in, redirect them to home page
if (isset($_SESSION['user'])) {
    header('Location: ' . SITEURL . 'home.php');
    exit();
}

// Handle login form submission
if (isset($_POST['submit'])) {
    // Get user input and escape strings
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // SQL statement using prepared statements to prevent SQL Injection
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username); // "s" specifies the type (string)
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if the user exists
    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();

        // Verify password using password_verify (compare plain text password with the hashed password)
        if (password_verify($password, $row['password'])) {
            // Password is correct, login successful

            // Regenerate session ID to prevent session fixation
            session_regenerate_id(true);

            // Store necessary user details in session variables
            $_SESSION['login'] = "<div class='alert alert-success'>Login Successful.</div>";
            $_SESSION['user'] = $row['username']; // Store username in session
            $_SESSION['role_id'] = $row['role_id']; // Store role id in session
            $_SESSION['profile_image'] = $row['profile_image']; // Store profile image in session

            // Handle "Remember Me" functionality (optional)
            if (isset($_POST['remember_me'])) {
                setcookie('username', $username, time() + (86400 * 30), "/");
                setcookie('password', $password, time() + (86400 * 30), "/");
            } else {
                setcookie('username', '', time() - 3600, "/");
                setcookie('password', '', time() - 3600, "/");
            }

            // Redirect to home page
            header('Location: ' . SITEURL . 'home.php');
            exit();
        } else {
            // Incorrect password
            $_SESSION['login'] = "<div class='alert alert-danger text-center'>Invalid credentials. Try again.</div>";
            header('Location: ' . SITEURL . 'login.php');
            exit();
        }
    } else {
        // User not found
        $_SESSION['login'] = "<div class='alert alert-danger text-center'>Username or Password did not match.</div>";
        header('Location: ' . SITEURL . 'login.php');
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CMP - Login</title>

    <!-- Bootstrap CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Optional custom CSS -->
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card mt-5">
                    <div class="card-header text-center">
                        <h1>Login</h1>
                    </div>
                    <div class="card-body">
                        <!-- Display login status messages -->
                        <?php
                        if (isset($_SESSION['login'])) {
                            echo $_SESSION['login'];
                            unset($_SESSION['login']);
                        }
                        ?>
                        <!-- Login Form -->
                        <form action="" method="POST">
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" name="username" class="form-control" placeholder="Enter username"
                                    required>
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" name="password" class="form-control" placeholder="Enter password"
                                    required>
                            </div>

                            <div class="form-check mb-3">
                                <input type="checkbox" name="remember_me" class="form-check-input">
                                <label class="form-check-label" for="remember_me">Remember Me</label>
                            </div>

                            <button type="submit" name="submit" class="btn btn-primary w-100">Login</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>

</html>
