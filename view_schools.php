<?php
include('./config/session_timeout.php');
include('./config/auth.php');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Schools</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="./assets/css/style.css">
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
                <h1 class="mt-4">View Schools</h1>
                <?php
                // Increment ID
                $id = 1;

                // Fetch schools from the database
                $query = "SELECT * FROM schools";
                $result = mysqli_query($conn, $query);

                if (mysqli_num_rows($result) > 0): ?>
                    <table class="table table-bordered mt-3">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Address</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                <tr>
                                    <td><?php echo $id++ ?></td>
                                    <td><?php echo $row['name']; ?></td>
                                    <td><?php echo $row['address']; ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="alert alert-info">No schools found.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>

</html>