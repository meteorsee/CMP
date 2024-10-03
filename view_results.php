<?php
include('./config/session_timeout.php');
include('./config/auth.php');

// Check user role
if (!isset($_SESSION['user']) || ($_SESSION['role_id'] != 1 && $_SESSION['role_id'] != 2)) {
    header('Location: ' . SITEURL . 'login.php');
    exit();
}

// Fetch quizzes
$quizzes_result = $conn->query("SELECT * FROM quizzes ORDER BY created_at DESC");

if (isset($_GET['quiz_id'])) {
    $quiz_id = intval($_GET['quiz_id']);
    // Fetch student names, latest and highest score per quiz per student
    $answers_result = $conn->query("
        SELECT u.full_name, MAX(r.score) AS highest_score, MAX(r.created_at) AS latest_submission
        FROM results r
        JOIN users u ON r.user_id = u.id
        WHERE r.quiz_id = $quiz_id
        GROUP BY u.id
    ");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Student Answers</title>
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
            <div class="col-2 p-0">
                <?php include('./config/sidebar.php'); ?>
            </div>
            <div class="col-10">
                <h1 class="my-4">View Student Answers</h1>

                <h2>Select a Quiz</h2>
                <ul class="list-group">
                    <?php while ($row = $quizzes_result->fetch_assoc()): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <strong><?php echo htmlspecialchars($row['title']); ?></strong>
                            <a href="?quiz_id=<?php echo $row['id']; ?>" class="btn btn-primary btn-sm">View Answers</a>
                        </li>
                    <?php endwhile; ?>
                </ul>

                <?php if (isset($answers_result) && $answers_result->num_rows > 0): ?>
                    <h2 class="mt-4">Student Scores for Quiz: <?php echo htmlspecialchars($quiz_id); ?></h2>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Highest Score</th>
                                <th>Latest Submission</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($answer_row = $answers_result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($answer_row['full_name']); ?></td>
                                    <td><?php echo htmlspecialchars($answer_row['highest_score']); ?></td>
                                    <td><?php echo htmlspecialchars($answer_row['latest_submission']); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php elseif (isset($answers_result)): ?>
                    <p>No answers found for this quiz.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>

</html>
