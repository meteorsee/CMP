<?php
include('./config/session_timeout.php');
include('./config/auth.php');

// Prevent caching of this page
header("Cache-Control: no-cache, must-revalidate"); // HTTP 1.1
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
header("Pragma: no-cache");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: view_quiz.php');
    exit();
}

if (!isset($_POST['quiz_id'])) {
    echo "Invalid submission.";
    exit();
}

$quiz_id = intval($_POST['quiz_id']);
$username = $_SESSION['user'];

// Fetch the user's ID based on the username
$user_result = $conn->query("SELECT id FROM users WHERE username = '$username'");
$user = $user_result->fetch_assoc();

if (!$user) {
    echo "User not found.";
    exit();
}

$user_id = $user['id'];

// Retrieve the max attempts allowed for the quiz
$quiz_result = $conn->query("SELECT max_attempts FROM quizzes WHERE id = $quiz_id");
$quiz_data = $quiz_result->fetch_assoc();

if (!$quiz_data) {
    echo "Quiz not found.";
    exit();
}

$max_attempt = $quiz_data['max_attempts'];

// Check if the user has already attempted the quiz
$attempt_check = $conn->prepare("SELECT attempt_count FROM quiz_attempts WHERE user_id = ? AND quiz_id = ?");
$attempt_check->bind_param("ii", $user_id, $quiz_id);
$attempt_check->execute();
$attempt_result = $attempt_check->get_result();

$attempt_data = $attempt_result->fetch_assoc();
$current_attempts = $attempt_data ? $attempt_data['attempt_count'] : 0;

$score = 0;

// Combine the answers from the form and the session
if (isset($_SESSION['user_answers'][$quiz_id])) {
    $combined_answers = $_SESSION['user_answers'][$quiz_id];

    // If there are new answers submitted in the form, merge them with session answers
    if (isset($_POST['answers'])) {
        foreach ($_POST['answers'] as $question_id => $answer_id) {
            $combined_answers[$question_id] = $answer_id;
        }
    }
} else {
    // If no session answers, just take the answers from the form
    $combined_answers = $_POST['answers'] ?? [];
}

// Check if the user exceeded the maximum allowed attempts
if ($current_attempts >= $max_attempt) {
    // Allow them to view the quiz but prevent submission
    $_SESSION['msg'] = "You have exceeded the maximum number of attempts for this quiz, but you can still review your answers.";
    header('Location: view_quiz.php');
    exit();
}

// Check if the user has not exceeded the maximum allowed attempts
if ($current_attempts < $max_attempt) {
    // Increment the attempt count
    $new_attempt_count = $current_attempts + 1;

    // If it's the first attempt, insert a new record
    if ($current_attempts === 0) {
        $insert_attempt = $conn->prepare("INSERT INTO quiz_attempts (user_id, quiz_id, attempt_count) VALUES (?, ?, ?)");
        $insert_attempt->bind_param("iii", $user_id, $quiz_id, $new_attempt_count);
        $insert_attempt->execute();
    } else {
        // If not the first attempt, update the existing record
        $update_attempt = $conn->prepare("UPDATE quiz_attempts SET attempt_count = ? WHERE user_id = ? AND quiz_id = ?");
        $update_attempt->bind_param("iii", $new_attempt_count, $user_id, $quiz_id);
        $update_attempt->execute();
    }

    // Process combined answers
    foreach ($combined_answers as $question_id => $answer_id) {
        // Ensure the answer is an integer
        $student_answer = intval($answer_id);

        // Check if the student's selected option is the correct one
        $option_result = $conn->query("SELECT is_correct FROM options WHERE id = $student_answer AND question_id = $question_id LIMIT 1");

        if ($option_result->num_rows > 0) {
            $option = $option_result->fetch_assoc();
            if ($option['is_correct'] == 1) {
                // Increment the score if the answer is correct
                $score++;
            }
        }
    }

    // Insert the score into the results table
    $stmt = $conn->prepare("INSERT INTO results (quiz_id, user_id, score) VALUES (?, ?, ?)");
    $stmt->bind_param("iii", $quiz_id, $user_id, $score);
    $stmt->execute();

    // Unset quiz session data (reset for future attempts)
    unset($_SESSION['start_time']);
    unset($_SESSION['quiz_duration']);
    unset($_SESSION['user_answers'][$quiz_id]);

    // Set a session flag indicating the quiz has been submitted
    $_SESSION['quiz_submitted'][$quiz_id] = true;

    
    header("Location: view_quiz.php"); // Redirect to results page
    exit();
} else {
    // Exceeded max attempts
    echo "You have exceeded the maximum number of attempts for this quiz.";
}
?>
