<?php
include('./config/session_timeout.php');
include('./config/auth.php');

$username = $_SESSION['user']; // Get the username from the session

// Fetch the user's ID based on the username
$user_result = $conn->query("SELECT id FROM users WHERE username = '$username'");
$user = $user_result->fetch_assoc();
$user_id = $user['id']; // Get the user's ID

// Fetch all quizzes
$result = $conn->query("SELECT * FROM quizzes ORDER BY created_at DESC");

// When accessing the quiz list, clear session variables related to the quiz timer
unset($_SESSION['start_time']);
unset($_SESSION['quiz_duration']);
unset($_SESSION['quiz_reset']);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>View Quizzes</title>
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
                <h1 class="my-4">Available Quizzes</h1>
                <ul class="list-group">
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <?php
                        // Check how many times the user has attempted this quiz
                        $quiz_id = $row['id'];
                        $attempt_result = $conn->query("SELECT attempt_count FROM quiz_attempts WHERE user_id = $user_id AND quiz_id = $quiz_id");
                        $attempt = $attempt_result->fetch_assoc();
                        $attempt_count = $attempt['attempt_count'] ?? 0; // Default to 0 if no attempt found
                    
                        // Calculate remaining attempts
                        $remaining_attempts = $row['max_attempts'] - $attempt_count;

                        // Fetch the highest mark for this quiz
                        $highest_score_result = $conn->query("SELECT MAX(score) AS highest_score FROM results WHERE user_id = $user_id AND quiz_id = $quiz_id");
                        $highest_score = $highest_score_result->fetch_assoc();
                        $highest_score_output = $highest_score['highest_score'] ?? 0; // Default to 0 if no score found
                    

                        ?>
                        <li class="list-group-item">
                            <strong><?php echo htmlspecialchars($row['title']); ?></strong><br>
                            <p>Description: <?php echo htmlspecialchars($row['description']); ?><br></p>
                            <small>Remaining Attempts: <strong><?php echo $remaining_attempts; ?></strong></small><br>
                            <small>Highest Mark: <strong><?php echo $highest_score_output; ?></strong></small>
                            <?php if ($attempt_count < $row['max_attempts']): ?>
                                <a href="take_quiz.php?quiz_id=<?php echo $row['id']; ?>"
                                    class="btn btn-primary btn-sm float-end" onclick="return confirmTakeQuiz();">Take Quiz</a>

                                <script>
                                    function confirmTakeQuiz() {
                                        return confirm("Are you sure you want to take the quiz?");
                                    }
                                </script>

                            <?php else: ?>
                                <button class="btn btn-secondary btn-sm float-end" disabled>Quiz Fully Attempted</button>
                            <?php endif; ?>
                        </li>
                    <?php endwhile; ?>
                </ul>
            </div>
        </div>
    </div>
</body>

</html>