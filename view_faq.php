<?php
// Include session timeout and authentication configurations
include('./config/session_timeout.php');
include('./config/auth.php');

// Fetch all FAQs from the database
$result = $conn->query("SELECT * FROM faqs ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>View FAQs</title>
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
                <h1 class="mb-4">Frequently Asked Questions</h1>
                <div class="accordion" id="faqAccordion">
                    <?php 
                    $i = 0; // Initialize counter
                    while ($row = $result->fetch_assoc()): ?>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading<?php echo $i; ?>">
                                <button class="accordion-button collapsed" type="button" 
                                        data-bs-toggle="collapse" 
                                        data-bs-target="#collapse<?php echo $i; ?>" 
                                        aria-expanded="false" 
                                        aria-controls="collapse<?php echo $i; ?>">
                                    <?php echo $row['question']; ?>
                                </button>
                            </h2>
                            <div id="collapse<?php echo $i; ?>" class="accordion-collapse collapse" 
                                 aria-labelledby="heading<?php echo $i; ?>" 
                                 data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    <?php echo htmlspecialchars_decode($row['answer']); ?>
                                </div>
                            </div>
                        </div>
                    <?php 
                    $i++; // Increment counter
                    endwhile; ?>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
