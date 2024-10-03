<?php
session_start();

// Ensure quiz_id and answers are present in the POST request
if (isset($_POST['quiz_id']) && isset($_POST['answers'])) {
    $quiz_id = $_POST['quiz_id'];

    // Check if there are already saved answers for the quiz
    if (!isset($_SESSION['user_answers'][$quiz_id])) {
        $_SESSION['user_answers'][$quiz_id] = []; // Initialize an empty array if no answers exist
    }

    // Merge new answers with the previous ones
    foreach ($_POST['answers'] as $question_id => $answer) {
        $_SESSION['user_answers'][$quiz_id][$question_id] = $answer;
    }

    // Save remaining time in session
    if (isset($_POST['remaining_time'])) {
        $_SESSION['remaining_time'] = intval($_POST['remaining_time']);
    }
}
