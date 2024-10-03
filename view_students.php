<?php
include('./config/session_timeout.php');
include('./config/auth.php');
include('./config/check_role.php');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Students</title>

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
            <h1 class="mt-4">View Students</h1>

            <!-- Search Input -->
            <div class="mb-3">
                <input type="text" id="search" class="form-control" placeholder="Search student by name...">
            </div>

            <!-- Student Table -->
            <table class="table table-bordered mt-3">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>School</th>
                    </tr>
                </thead>
                <tbody id="studentTableBody">
                    <!-- Results will be displayed here -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    // Function to load all students initially
    function loadStudents() {
        var xhr = new XMLHttpRequest();
        xhr.open('GET', 'search_students.php?query=', true); // Fetch all students
        xhr.onload = function() {
            if (this.status === 200) {
                document.getElementById('studentTableBody').innerHTML = this.responseText;
            } else {
                console.error('Error fetching data from server.');
            }
        };
        xhr.onerror = function() {
            console.error('Network error.');
        };
        xhr.send();
    }

    // Load students on page load
    loadStudents();

    // Event listener for search input
    document.getElementById('search').addEventListener('input', function() {
        var searchQuery = this.value.trim();

        if (searchQuery.length > 0) {
            // AJAX request to fetch filtered student data
            var xhr = new XMLHttpRequest();
            xhr.open('GET', 'search_students.php?query=' + encodeURIComponent(searchQuery), true);
            xhr.onload = function() {
                if (this.status === 200) {
                    console.log(this.responseText); // Debugging: log the response to the console
                    document.getElementById('studentTableBody').innerHTML = this.responseText;
                } else {
                    console.error('Error fetching data from server.');
                }
            };
            xhr.onerror = function() {
                console.error('Network error.');
            };
            xhr.send();
        } else {
            loadStudents(); // Load all students if input is empty
        }
    });
</script>

</body>
</html>
