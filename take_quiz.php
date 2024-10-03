<?php
include('./config/session_timeout.php');
include('./config/auth.php');

// Prevent caching of this page
header("Cache-Control: no-cache, must-revalidate"); // HTTP 1.1
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
header("Pragma: no-cache");

if (!isset($_GET['quiz_id'])) {
    header('Location: view_quiz.php');
    exit();
}

$quiz_id = intval($_GET['quiz_id']);
$limit = 5;
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $limit;

// Fetch quiz details
$quiz_result = $conn->query("SELECT * FROM quizzes WHERE id = $quiz_id");
$quiz = $quiz_result->fetch_assoc();

if (!$quiz) {
    die("Quiz not found.");
}

// Fetch total questions
$total_questions_result = $conn->query("SELECT COUNT(*) AS total FROM questions WHERE quiz_id = $quiz_id");
$total_questions = $total_questions_result->fetch_assoc()['total'];
$total_pages = ceil($total_questions / $limit);

// Fetch questions for the current page
$questions_result = $conn->query("SELECT * FROM questions WHERE quiz_id = $quiz_id LIMIT $limit OFFSET $offset");

if (isset($_GET['reset']) || !isset($_SESSION['start_time']) || $_SESSION['quiz_reset']) {
    // Reset the timer for a new quiz attempt
    $_SESSION['start_time'] = time();
    $_SESSION['quiz_duration'] = $quiz['duration']; // Duration from the quiz table
    $remaining_time = $_SESSION['quiz_duration'] * 60;
    $_SESSION['quiz_reset'] = false; // Clear the reset flag
} else {
    // Continue the timer from the session
    $elapsed_time = time() - $_SESSION['start_time'];
    $remaining_time = max(0, $_SESSION['quiz_duration'] * 60 - $elapsed_time);
}

// Handle logic when the user wants to reset the quiz (e.g., via the "Take Quiz" button)
if (isset($_GET['reset'])) {
    $_SESSION['quiz_reset'] = true;
}

// Track answers: if user has answered some questions, pre-populate them
$user_answers = isset($_SESSION['user_answers'][$quiz_id]) ? $_SESSION['user_answers'][$quiz_id] : [];

// Retrieve the max attempts allowed for the quiz
$quiz_result = $conn->query("SELECT max_attempts FROM quizzes WHERE id = $quiz_id");
$quiz_data = $quiz_result->fetch_assoc();

if (!$quiz_data) {
    echo "Quiz not found.";
    exit();
}

// Check if the user has already attempted the quiz
$attempt_check = $conn->prepare("SELECT attempt_count FROM quiz_attempts WHERE user_id = ? AND quiz_id = ?");
$attempt_check->bind_param("ii", $user_id, $quiz_id);
$attempt_check->execute();
$attempt_result = $attempt_check->get_result();

$attempt_data = $attempt_result->fetch_assoc();
$current_attempts = $attempt_data ? $attempt_data['attempt_count'] : 0;

$max_attempts = $quiz_data['max_attempts'];

// Redirect if the student has reached the maximum attempts
if ($current_attempts >= $max_attempts) {
    $_SESSION['msg'] = "You have reached the maximum number of attempts for this quiz.";
    header('Location: view_quiz.php');
    exit();
}

// Redirect if the student has already submitted the quiz and it's their first attempt
if (isset($_SESSION['quiz_submitted'][$quiz_id]) && $current_attempts == 1) {
    $_SESSION['msg'] = "You have already submitted this quiz and cannot access it again.";
    header('Location: view_quiz.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($quiz['title']); ?></title>
    <!-- Include Bootstrap and other required CSS/JS -->
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
            <h1 class="my-4"><?php echo htmlspecialchars($quiz['title']); ?></h1>
            <p><?php echo htmlspecialchars($quiz['description']); ?></p>

            <div id="quiz-timer" class="alert alert-warning d-none"></div> <!-- Timer display -->

            <!-- Quiz Form -->
            <form id="quizForm" action="submit_quiz.php" method="POST">
                <input type="hidden" name="quiz_id" value="<?php echo $quiz_id; ?>">
                <input type="hidden" name="remaining_time" id="remaining_time" value="<?php echo $remaining_time; ?>">

                <?php while ($question = $questions_result->fetch_assoc()): ?>
                    <div class="mb-4">
                        <h5><?php echo htmlspecialchars($question['question_text']); ?></h5>
                        <?php
                        // Fetch options for this question
                        $options_result = $conn->query("SELECT * FROM options WHERE question_id = " . $question['id']);
                        ?>
                        <?php while ($option = $options_result->fetch_assoc()): ?>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="answers[<?php echo $question['id']; ?>]" value="<?php echo htmlspecialchars($option['id']); ?>"
                                       <?php if (isset($user_answers[$question['id']]) && $user_answers[$question['id']] == $option['id']) echo 'checked'; ?>>
                                <label class="form-check-label">
                                    <?php echo htmlspecialchars($option['option_text']); ?>
                                </label>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php endwhile; ?>

                <button type="submit" class="btn btn-primary">Submit Quiz</button>
            </form>

            <!-- Pagination -->
            <nav aria-label="Page navigation">
                <ul class="pagination">
                    <li class="page-item <?php if ($page <= 1) echo 'disabled'; ?>">
                        <a class="page-link" href="?quiz_id=<?php echo $quiz_id; ?>&page=<?php echo max(1, $page - 1); ?>" onclick="saveAnswers()">Previous</a>
                    </li>
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <li class="page-item <?php if ($i == $page) echo 'active'; ?>">
                            <a class="page-link" href="?quiz_id=<?php echo $quiz_id; ?>&page=<?php echo $i; ?>" onclick="saveAnswers()"><?php echo $i; ?></a>
                        </li>
                    <?php endfor; ?>
                    <li class="page-item <?php if ($page >= $total_pages) echo 'disabled'; ?>">
                        <a class="page-link" href="?quiz_id=<?php echo $quiz_id; ?>&page=<?php echo min($total_pages, $page + 1); ?>" onclick="saveAnswers()">Next</a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</div>

<script>
    // Save answers before navigating to the next page
    function saveAnswers() {
        const form = document.getElementById('quizForm');
        const data = new FormData(form);

        // Store answers in session via AJAX
        fetch('save_answers.php', {
            method: 'POST',
            body: data
        }).then(response => {
            if (!response.ok) {
                alert('Error saving answers!');
            }
        });
    }

    // Timer functionality
    window.onload = function () {
        if (!sessionStorage.getItem('quizStarted')) {
            let isConfirmed = confirm("Are you sure you want to start the quiz?");
            if (!isConfirmed) {
                window.location.href = "view_quiz.php";
            } else {
                sessionStorage.setItem('quizStarted', 'true');
                startTimer(<?php echo $remaining_time; ?>);
            }
        } else {
            startTimer(<?php echo $remaining_time; ?>);
        }
    };

    let timer = <?php echo $remaining_time; ?>; // Timer duration in seconds

    // Timer function
    function startTimer(duration) {
        let minutes, seconds;
        const display = document.getElementById('quiz-timer');
        display.classList.remove('d-none');

        // Update the display every second
        const interval = setInterval(function () {
            minutes = parseInt(duration / 60, 10);
            seconds = parseInt(duration % 60, 10);

            minutes = minutes < 10 ? "0" + minutes : minutes;
            seconds = seconds < 10 ? "0" + seconds : seconds;

            display.textContent = `Time remaining: ${minutes}:${seconds}`;

            // Stop the timer if it reaches zero
            if (--duration < 0) {
                clearInterval(interval);
                document.getElementById('quizForm').submit();
            }
        }, 1000);
    }
</script>
</body>
</html>
