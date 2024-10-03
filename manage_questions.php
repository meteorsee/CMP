<?php
include('./config/session_timeout.php');
include('./config/auth.php');

// Ensure the user is a teacher
if (!isset($_SESSION['user']) || ($_SESSION['role_id'] != 1 && $_SESSION['role_id'] != 2)) {
    header('Location: ' . SITEURL . 'login.php');
    exit();
}

$quiz_id = isset($_GET['quiz_id']) ? (int) $_GET['quiz_id'] : 0;

// Fetch quiz title
$quiz_title = "";
if ($quiz_id > 0) {
    $stmt_quiz = $conn->prepare("SELECT title FROM quizzes WHERE id = ?");
    $stmt_quiz->bind_param("i", $quiz_id);
    $stmt_quiz->execute();
    $result_quiz = $stmt_quiz->get_result();
    if ($result_quiz->num_rows > 0) {
        $quiz_row = $result_quiz->fetch_assoc();
        $quiz_title = $quiz_row['title'];
    }
    $stmt_quiz->close();
}

// Fetch questions for the quiz
$questions = [];
if ($quiz_id > 0) {
    $stmt = $conn->prepare("SELECT * FROM questions WHERE quiz_id = ?");
    $stmt->bind_param("i", $quiz_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $stmt_options = $conn->prepare("SELECT * FROM options WHERE question_id = ?");
        $stmt_options->bind_param("i", $row['id']);
        $stmt_options->execute();
        $options_result = $stmt_options->get_result();
        $options = [];
        while ($opt_row = $options_result->fetch_assoc()) {
            $options[] = $opt_row;
        }
        $questions[] = ['question' => $row, 'options' => $options];
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Manage Questions for <?php echo $quiz_title; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <link rel="stylesheet" href="./assets/css/style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
                <h1>Manage Questions for Quiz: <?php echo $quiz_title; ?></h1>

                <?php if (isset($_SESSION['msg'])): ?>
                    <div class="alert alert-success">
                        <?php
                        echo $_SESSION['msg'];
                        unset($_SESSION['msg']);
                        ?>
                    </div>
                <?php endif; ?>

                <form id="questions-form" method="POST" action="save_questions.php?quiz_id=<?php echo $quiz_id; ?>">
                    <div id="questions-container">
                        <?php if (count($questions) > 0): ?>
                            <?php foreach ($questions as $index => $question_data): ?>
                                <div class="question-set" data-question-id="<?php echo $question_data['question']['id']; ?>">
                                    <label for="question_<?php echo $index; ?>">Question <?php echo $index + 1; ?></label>
                                    <input type="text" name="questions[<?php echo $question_data['question']['id']; ?>]"
                                        value="<?php echo $question_data['question']['question_text']; ?>" class="form-control"
                                        required>

                                    <label>Options</label>
                                    <div class="options-container">
                                        <?php foreach ($question_data['options'] as $option_index => $option): ?>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio"
                                                    name="correct_answers[<?php echo $question_data['question']['id']; ?>]"
                                                    value="<?php echo $option['id']; ?>" <?php echo $option['is_correct'] ? 'checked' : ''; ?>>
                                                <input type="text"
                                                    name="options[<?php echo $question_data['question']['id']; ?>][<?php echo $option['id']; ?>]"
                                                    value="<?php echo $option['option_text']; ?>" class="form-control d-inline">
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                    <button type="button" class="btn btn-danger delete-question-btn"
                                        data-question-id="<?php echo $question_data['question']['id']; ?>">Delete</button>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p>No questions added yet. Click "Add Question" to start.</p>
                        <?php endif; ?>
                    </div>
                    <button type="button" id="add-question" class="btn btn-primary mb-3">Add Question</button>
                    <button type="submit" name="save_questions" class="btn btn-success mb-3">Save Questions</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            const questionsContainer = $('#questions-container');

            // Add new question dynamically
            $('#add-question').click(function () {
                const questionCount = $('.question-set').length;
                const newQuestionHTML = `
        <div class="question-set">
            <label>Question ${questionCount + 1}</label>
            <input type="text" name="new_questions[${questionCount}]" class="form-control" required>
            <label>Options</label>
            <div class="options-container">
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="new_correct_answers[${questionCount}]" value="0" required>
                    <input type="text" name="new_options[${questionCount}][]" class="form-control d-inline" placeholder="Option 1" required>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="new_correct_answers[${questionCount}]" value="1">
                    <input type="text" name="new_options[${questionCount}][]" class="form-control d-inline" placeholder="Option 2" required>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="new_correct_answers[${questionCount}]" value="2">
                    <input type="text" name="new_options[${questionCount}][]" class="form-control d-inline" placeholder="Option 3" required>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="new_correct_answers[${questionCount}]" value="3">
                    <input type="text" name="new_options[${questionCount}][]" class="form-control d-inline" placeholder="Option 4" required>
                </div>
            </div>
            <button type="button" class="btn btn-danger delete-question-btn">Delete</button>
        </div>`;
                questionsContainer.append(newQuestionHTML);
            });


            // Handle question deletion
            $(document).on('click', '.delete-question-btn', function () {
                const questionElement = $(this).closest('.question-set');
                const questionId = questionElement.data('question-id');

                // Add the question ID to a hidden input for tracking deleted questions
                if (questionId) {
                    $('#questions-form').append('<input type="hidden" name="deleted_questions[]" value="' + questionId + '">');
                }

                questionElement.remove();
            });

        });
    </script>
</body>

</html>