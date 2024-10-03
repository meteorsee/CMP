<?php
include('./config/session_timeout.php');
include('./config/auth.php');

// Check user role
if (!isset($_SESSION['user']) || ($_SESSION['role_id'] != 1 && $_SESSION['role_id'] != 2)) {
    header('Location: ' . SITEURL . 'login.php');
    exit();
}

// Handle adding a new quiz
if (isset($_POST['add_quiz'])) {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $duration = intval($_POST['duration']);
    $max_attempts = intval($_POST['max_attempts']);

    // Prepare and execute statement to add quiz
    $stmt = $conn->prepare("INSERT INTO quizzes (title, description, duration, max_attempts) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssii", $title, $description, $duration, $max_attempts);
    $stmt->execute();

    $_SESSION['msg'] = "Quiz added successfully!";
    header('Location: quiz.php');
    exit();
}

// Handle quiz deletion
if (isset($_GET['delete'])) {
    $quiz_id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM quizzes WHERE id = ?");
    $stmt->bind_param("i", $quiz_id);
    $stmt->execute();

    $_SESSION['msg'] = "Quiz deleted successfully!";
    header('Location: quiz.php');
    exit();
}

// Fetch all quizzes
$result = $conn->query("SELECT * FROM quizzes ORDER BY created_at DESC");
$total_quizzes = $result->num_rows;

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Quiz Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="./assets/css/style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-2 p-0">
                <?php include('./config/sidebar.php'); ?>
            </div>
            <div class="col-10">
                <h1 class="my-4">Quiz Management</h1>

                <?php if (isset($_SESSION['msg'])): ?>
                    <div class="alert alert-success">
                        <?php
                        echo $_SESSION['msg'];
                        unset($_SESSION['msg']);
                        ?>
                    </div>
                <?php endif; ?>

                <form action="" method="POST" class="mb-4">
                    <div class="mb-3">
                        <label for="title" class="form-label">Quiz Title</label>
                        <input type="text" name="title" id="title" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea name="description" id="description" class="form-control" rows="4"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="duration" class="form-label">Duration (in minutes)</label>
                        <input type="number" name="duration" id="duration" class="form-control" min="1" value="1"
                            required>
                    </div>
                    <div class="mb-3">
                        <label for="max_attempts" class="form-label">Max Attempts</label>
                        <input type="number" name="max_attempts" id="max_attempts" class="form-control" min="1"
                            value="2" required>
                    </div>
                    <button type="submit" name="add_quiz" class="btn btn-primary">Add Quiz</button>
                </form>

                <h2>Existing Quizzes (Total: <?php echo $total_quizzes; ?>)</h2>
                <ul class="list-group">
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <strong><?php echo $row['title']; ?></strong><br>
                                <small>Description: <?php echo $row['description']; ?></small><br>
                                <small>Duration: <?php echo $row['duration']; ?> minutes</small>
                            </div>
                            <div>
                                <a href="manage_questions.php?quiz_id=<?php echo $row['id']; ?>"
                                    class="btn btn-success btn-sm">Manage Questions</a>
                                <a href="?delete=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm">Delete</a>
                            </div>
                        </li>
                    <?php endwhile; ?>
                </ul>
            </div>
        </div>
    </div>
</body>

</html>