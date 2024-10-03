<?php
include('./config/session_timeout.php');
include('./config/auth.php');

if (!isset($_GET['quiz_id']) || !isset($_GET['page'])) {
    echo json_encode(['error' => 'Missing parameters']);
    exit();
}

$quiz_id = intval($_GET['quiz_id']);
$page = intval($_GET['page']);
$limit = 5; // Number of questions per page
$offset = ($page - 1) * $limit;

// Fetch questions for the quiz with limit and offset
$questions_result = $conn->query("SELECT * FROM questions WHERE quiz_id = $quiz_id LIMIT $limit OFFSET $offset");

if ($questions_result->num_rows > 0) {
    $questions = [];
    while ($question = $questions_result->fetch_assoc()) {
        // Fetch options for this question
        $options_result = $conn->query("SELECT * FROM options WHERE question_id = " . $question['id']);
        $options = [];
        while ($option = $options_result->fetch_assoc()) {
            $options[] = [
                'id' => $option['id'],
                'text' => htmlspecialchars($option['option_text'])
            ];
        }
        $questions[] = [
            'id' => $question['id'],
            'text' => htmlspecialchars($question['question_text']),
            'options' => $options
        ];
    }

    echo json_encode([
        'questions' => $questions
    ]);
} else {
    echo json_encode(['error' => 'No questions found']);
}
?>
