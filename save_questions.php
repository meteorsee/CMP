<?php
include('./config/session_timeout.php');
include('./config/auth.php');

$quiz_id = isset($_GET['quiz_id']) ? (int) $_GET['quiz_id'] : 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Existing questions
    if (isset($_POST['questions'])) {
        foreach ($_POST['questions'] as $question_id => $question_text) {
            $question_text = mysqli_real_escape_string($conn, $question_text);

            // Update question text
            $stmt = $conn->prepare("UPDATE questions SET question_text = ? WHERE id = ? AND quiz_id = ?");
            $stmt->bind_param("sii", $question_text, $question_id, $quiz_id);
            $stmt->execute();

            // Update options
            if (isset($_POST['options'][$question_id])) {
                foreach ($_POST['options'][$question_id] as $option_id => $option_text) {
                    $option_text = mysqli_real_escape_string($conn, $option_text);
                    $is_correct = isset($_POST['correct_answers'][$question_id]) && $_POST['correct_answers'][$question_id] == $option_id ? 1 : 0;

                    $stmt_option = $conn->prepare("UPDATE options SET option_text = ?, is_correct = ? WHERE id = ? AND question_id = ?");
                    $stmt_option->bind_param("siii", $option_text, $is_correct, $option_id, $question_id);
                    $stmt_option->execute();
                }
            }
        }
    }

    // New questions
    if (isset($_POST['new_questions'])) {
        foreach ($_POST['new_questions'] as $index => $new_question_text) {
            $new_question_text = mysqli_real_escape_string($conn, $new_question_text);

            // Insert new question
            $stmt_new_question = $conn->prepare("INSERT INTO questions (quiz_id, question_text) VALUES (?, ?)");
            $stmt_new_question->bind_param("is", $quiz_id, $new_question_text);
            $stmt_new_question->execute();
            $new_question_id = $stmt_new_question->insert_id;

            // Insert new options
            if (isset($_POST['new_options'][$index])) {
                foreach ($_POST['new_options'][$index] as $opt_index => $new_option_text) {
                    $new_option_text = mysqli_real_escape_string($conn, $new_option_text);
                    $is_correct = isset($_POST['new_correct_answers'][$index]) && $_POST['new_correct_answers'][$index] == $opt_index ? 1 : 0;

                    $stmt_new_option = $conn->prepare("INSERT INTO options (question_id, option_text, is_correct) VALUES (?, ?, ?)");
                    $stmt_new_option->bind_param("isi", $new_question_id, $new_option_text, $is_correct);
                    $stmt_new_option->execute();
                }
            }
        }
    }

    // Handle deleted questions
    if (isset($_POST['deleted_questions'])) {
        foreach ($_POST['deleted_questions'] as $deleted_question_id) {
            $deleted_question_id = (int) $deleted_question_id;

            // Delete options related to the question
            $stmt_delete_options = $conn->prepare("DELETE FROM options WHERE question_id = ?");
            $stmt_delete_options->bind_param("i", $deleted_question_id);
            $stmt_delete_options->execute();

            // Delete the question itself
            $stmt_delete_question = $conn->prepare("DELETE FROM questions WHERE id = ? AND quiz_id = ?");
            $stmt_delete_question->bind_param("ii", $deleted_question_id, $quiz_id);
            $stmt_delete_question->execute();
        }
    }


    // Redirect back to manage questions
    $_SESSION['msg'] = 'Questions saved successfully.';
    header('Location: manage_questions.php?quiz_id=' . $quiz_id);
    exit();
}
?>