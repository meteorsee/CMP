<?php
include('./config/database.php');
include('./config/auth.php');

// Check user role
if (!isset($_SESSION['user']) || ($_SESSION['role_id'] != 1 && $_SESSION['role_id'] != 2)) {
    header('Location: ' . SITEURL . 'login.php');
    exit();
}

// Handle adding a new FAQ
if (isset($_POST['add_faq'])) {
    $question = mysqli_real_escape_string($conn, $_POST['question']);
    $answer = mysqli_real_escape_string($conn, $_POST['answer']);

    $stmt = $conn->prepare("INSERT INTO faqs (question, answer) VALUES (?, ?)");
    $stmt->bind_param("ss", $question, $answer);
    $stmt->execute();

    $_SESSION['msg'] = "FAQ added successfully!";
    header('Location: faq.php');
    exit();
}

// Handle editing an existing FAQ
if (isset($_POST['edit_faq'])) {
    $faq_id = $_POST['faq_id'];
    $question = mysqli_real_escape_string($conn, $_POST['question']);
    $answer = mysqli_real_escape_string($conn, $_POST['answer']);

    $stmt = $conn->prepare("UPDATE faqs SET question = ?, answer = ? WHERE id = ?");
    $stmt->bind_param("ssi", $question, $answer, $faq_id);
    $stmt->execute();

    $_SESSION['msg'] = "FAQ updated successfully!";
    header('Location: faq.php');
    exit();
}

// Handle deleting an FAQ
if (isset($_GET['delete'])) {
    $faq_id = $_GET['delete'];

    $stmt = $conn->prepare("DELETE FROM faqs WHERE id = ?");
    $stmt->bind_param("i", $faq_id);
    $stmt->execute();

    $_SESSION['msg'] = "FAQ deleted successfully!";
    header('Location: faq.php');
    exit();
}

// Fetch all FAQs
$result = $conn->query("SELECT * FROM faqs ORDER BY created_at DESC");

// Fetch FAQ data for editing
$faq_to_edit = null;
if (isset($_GET['edit'])) {
    $faq_id = $_GET['edit'];
    $faq_stmt = $conn->prepare("SELECT * FROM faqs WHERE id = ?");
    $faq_stmt->bind_param("i", $faq_id);
    $faq_stmt->execute();
    $faq_to_edit = $faq_stmt->get_result()->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>FAQ Management</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="./assets/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/tinymce@5/tinymce.min.js"></script>
    <script>
        tinymce.init({
            selector: '#answer',  // The ID of the textarea to replace with TinyMCE
            plugins: 'advlist autolink lists link image charmap print preview hr anchor pagebreak',
            toolbar_mode: 'floating',
            menubar: false,  // Optional: Removes the menubar for a simpler UI
        });
    </script>
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
                <h1 class="my-4">FAQ Management</h1>

                <!-- Display success or error messages -->
                <?php if (isset($_SESSION['msg'])): ?>
                    <div class="alert alert-success">
                        <?php
                        echo $_SESSION['msg'];
                        unset($_SESSION['msg']);
                        ?>
                    </div>
                <?php endif; ?>

                <!-- Form to add or edit a FAQ -->
                <form action="" method="POST" class="mb-4">
                    <input type="hidden" name="faq_id"
                        value="<?php echo isset($faq_to_edit['id']) ? $faq_to_edit['id'] : ''; ?>">
                    <div class="mb-3">
                        <label for="question" class="form-label">Question</label>
                        <input type="text" name="question" id="question" class="form-control"
                            placeholder="Enter the question" required
                            value="<?php echo isset($faq_to_edit['question']) ? htmlspecialchars($faq_to_edit['question']) : ''; ?>">
                    </div>
                    <div class="mb-3">
                        <label for="answer" class="form-label">Answer</label>
                        <textarea name="answer" id="answer" class="form-control" rows="4" placeholder="Enter the answer"
                            required><?php echo isset($faq_to_edit['answer']) ? htmlspecialchars($faq_to_edit['answer']) : ''; ?></textarea>
                    </div>

                    <button type="submit" name="<?php echo isset($faq_to_edit) ? 'edit_faq' : 'add_faq'; ?>"
                        class="btn btn-primary">
                        <?php echo isset($faq_to_edit) ? 'Update FAQ' : 'Add FAQ'; ?>
                    </button>
                </form>

                <h2>Existing FAQs</h2>
                <div class="accordion" id="faqAccordion">
                    <?php $i = 0;
                    while ($row = $result->fetch_assoc()): ?>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading<?php echo $i; ?>">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#collapse<?php echo $i; ?>" aria-expanded="false"
                                    aria-controls="collapse<?php echo $i; ?>">
                                    <?php echo $row['question']; ?>
                                </button>
                            </h2>
                            <div id="collapse<?php echo $i; ?>" class="accordion-collapse collapse"
                                aria-labelledby="heading<?php echo $i; ?>" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    <?php echo $row['answer']; ?>
                                    <div class="mt-2">
                                        <a href="?edit=<?php echo $row['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                                        <a href="?delete=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm">Delete</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php $i++; endwhile; ?>
                </div>

            </div>
        </div>
    </div>
</body>

</html>