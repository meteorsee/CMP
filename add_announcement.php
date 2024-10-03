<?php
include('./config/session_timeout.php');
include('./config/auth.php');

// Check user role
if (!isset($_SESSION['user']) || ($_SESSION['role_id'] !== 1 && $_SESSION['role_id'] !== 2)) {
    header('Location: home.php'); // Redirect if not admin or teacher
    exit();
}
$username = $_SESSION['user']; // Assuming this is the username stored in session

// Fetch the user data from the database
$query = "SELECT * FROM users WHERE username = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('s', $username);  // 's' for string since username is a string
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

$user_id = $user['id'];  // Get the user ID for updates

// Handle form submission for new announcement
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_announcement'])) {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $content = mysqli_real_escape_string($conn, $_POST['content']);

    // Prepare and execute the insert statement
    $stmt = $conn->prepare("INSERT INTO announcements (user_id, title, content) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $user_id, $title, $content);

    if ($stmt->execute()) {
        header('Location: add_announcement.php');
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
}

// Handle editing an existing announcement
if (isset($_GET['edit'])) {
    $announcement_id = $_GET['edit'];

    // Fetch the announcement to be edited, limited to the current user
    $stmt = $conn->prepare("SELECT * FROM announcements WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $announcement_id, $user_id);
    $stmt->execute();
    $announcement_result = $stmt->get_result();
    $announcement = $announcement_result->fetch_assoc();
}

// Update the announcement if the form is submitted
if (isset($_POST['edit_announcement'])) {
    $announcement_id = $_POST['announcement_id'];
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $content = mysqli_real_escape_string($conn, $_POST['content']);

    $stmt = $conn->prepare("UPDATE announcements SET title = ?, content = ? WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ssii", $title, $content, $announcement_id, $user_id);
    $stmt->execute();

    header('Location: add_announcement.php');
    exit();
}

// Handle deleting an announcement
if (isset($_GET['delete'])) {
    $announcement_id = $_GET['delete'];

    $stmt = $conn->prepare("DELETE FROM announcements WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $announcement_id, $user_id);
    $stmt->execute();

    header('Location: add_announcement.php');
    exit();
}

// Fetch only the announcements created by the current user
$stmt = $conn->prepare("SELECT * FROM announcements WHERE user_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Announcement Management</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/tinymce@5/tinymce.min.js"></script>
<script>
    tinymce.init({
        selector: '#content',  // The ID of the textarea to replace with TinyMCE
        plugins: 'advlist autolink lists link image charmap print preview hr anchor pagebreak',
        toolbar_mode: 'floating',
        menubar: false,  // Optional: Removes the menubar for a simpler UI
    });
</script>


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
                <h1>Announcement Management</h1>
                
                <!-- Display success or error messages -->
                <?php if (isset($_GET['message'])): ?>
                    <div class="alert alert-success">
                        <?php
                        echo $_GET['message'];
                        ?>
                    </div>
                <?php endif; ?>

                <!-- Form to add a new announcement -->
                <form method="POST" action="" class="mb-4">
    <div class="mb-3">
        <label for="title" class="form-label">Title</label>
        <input type="text" class="form-control" id="title" name="title" value="<?php echo isset($announcement) ? $announcement['title'] : ''; ?>" required>
    </div>
    <div class="mb-3">
        <label for="content" class="form-label">Content</label>
        <textarea class="form-control" id="content" name="content" rows="5" required><?php echo isset($announcement) ? $announcement['content'] : ''; ?></textarea>
    </div>
    <button type="submit" name="<?php echo isset($announcement) ? 'edit_announcement' : 'add_announcement'; ?>" class="btn btn-primary">
        <?php echo isset($announcement) ? 'Update Announcement' : 'Submit'; ?>
    </button>
    <?php if (isset($announcement)): ?>
        <input type="hidden" name="announcement_id" value="<?php echo $announcement['id']; ?>">
    <?php endif; ?>
</form>


                <h2>Your Announcements</h2>
                <div class="accordion" id="announcementAccordion">
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading<?php echo $row['id']; ?>">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#collapse<?php echo $row['id']; ?>" aria-expanded="false"
                                    aria-controls="collapse<?php echo $row['id']; ?>">
                                    <?php echo $row['title']; ?>
                                </button>
                            </h2>
                            <div id="collapse<?php echo $row['id']; ?>" class="accordion-collapse collapse"
                                aria-labelledby="heading<?php echo $row['id']; ?>" data-bs-parent="#announcementAccordion">
                                <div class="accordion-body">
                                    <?php echo $row['content']; ?>
                                    <div class="mt-2">
                                        <a href="?edit=<?php echo $row['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                                        <a href="?delete=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm">Delete</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
