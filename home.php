<?php
include('./config/session_timeout.php');
include('./config/auth.php');
include('./config/check_role.php');


// Fetch announcements
$announcements_result = $conn->query("SELECT a.*, u.username FROM announcements a JOIN users u ON a.user_id = u.id ORDER BY a.created_at DESC");

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>

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
                <h1>Welcome, <?php echo htmlspecialchars($_SESSION['user']); ?>!</h1>
                <p>This is your home page. Stay active to keep your session alive.</p>

                <h1>Announcements</h1>
                <div class="accordion" id="announcementsAccordion">
                    <?php
                    $index = 0;
                    while ($announcement = $announcements_result->fetch_assoc()):
                        $index++;
                        ?>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading<?php echo $index; ?>">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#collapse<?php echo $index; ?>" aria-expanded="false"
                                    aria-controls="collapse<?php echo $index; ?>">
                                    <?php echo htmlspecialchars($announcement['title']); ?>
                                </button>
                            </h2>
                            <div id="collapse<?php echo $index; ?>" class="accordion-collapse collapse"
                                aria-labelledby="heading<?php echo $index; ?>" data-bs-parent="#announcementsAccordion">
                                <div class="accordion-body">     
                                    <p><?php echo htmlspecialchars_decode($announcement['content']); ?></p>
                                    <small>Posted by <?php echo htmlspecialchars($announcement['username']); ?> on
                                        <?php echo $announcement['created_at']; ?>
                                    </small>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>


            </div>
        </div>
    </div>
    <!-- Include inactivity timeout script -->
    <?php
    include("./assets/js/inactivity-timeout.php");
    ?>
</body>

</html>