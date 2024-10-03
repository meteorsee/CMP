<!-- Hamburger Icon -->
<div class="hamburger" id="header-toggle">
    <i class="fas fa-bars"></i>
</div>

<!-- Sidebar Section -->
<div class="sidebar l-navbar bg-dark text-white vh-100" id="nav-bar">
    <div class="bg_shadow"></div>
    <div class="sidebar_inner">
        <!-- Close Icon -->
        <div class="close text-white" id="header-close">
            <i class="fas fa-times"></i>
        </div>

        <div class="profile_info">
            <div class="profile_data">
                <!-- Display profile image from session -->
                <?php if (!empty($_SESSION['profile_image'])): ?>
                    <img src="<?= $_SESSION['profile_image'] ?>" alt="Profile Picture" class="img-thumbnail mt-2"
                        width="100">
                <?php endif; ?>
                <p class="name"><?php echo htmlspecialchars($_SESSION['user']); ?></p>
            </div>
        </div>

        <!-- Sidebar Menu Section -->
        <ul class="sidebar_menu list-unstyled">
            <li class="nav-item">
                <a href="home.php" class="nav-link text-white">
                    <i class="fas fa-home"></i> Home
                </a>
            </li>

            <!-- Announcement Section -->
            <?php if ($_SESSION['role_id'] == 1): // admin ?>
                <li class="nav-item">
                    <a class="nav-link text-white" data-bs-toggle="collapse" href="#announcementMenu" role="button"
                        aria-expanded="false" aria-controls="announcementMenu">
                        <i class="fas fa-bullhorn"></i> Announcement
                    </a>
                    <ul class="collapse list-unstyled ps-3" id="announcementMenu">
                        <li><a href="add_announcement.php" class="nav-link text-white"><i class="fas fa-plus"></i> Add Announcement</a>
                        </li>
                    </ul>
                </li>
            <?php endif; ?>

            <!-- School Section -->
            <?php if ($_SESSION['role_id'] == 1): // admin ?>
                <li class="nav-item">
                    <a class="nav-link text-white" data-bs-toggle="collapse" href="#schoolMenu" role="button"
                        aria-expanded="false" aria-controls="schoolMenu">
                        <i class="fas fa-school"></i> School
                    </a>
                    <ul class="collapse list-unstyled ps-3" id="schoolMenu">
                        <li><a href="add_school.php" class="nav-link text-white"><i class="fas fa-plus"></i> Add School</a>
                        </li>
                        <li><a href="view_schools.php" class="nav-link text-white"><i class="fas fa-list"></i> View
                                Schools</a></li>
                    </ul>
                </li>
            <?php endif; ?>

            <!-- Teachers Section -->
            <?php if ($_SESSION['role_id'] == 1): // admin or lecturer ?>
                <li class="nav-item">
                    <a class="nav-link text-white" data-bs-toggle="collapse" href="#teacherMenu" role="button"
                        aria-expanded="false" aria-controls="teacherMenu">
                        <i class="fas fa-chalkboard-teacher"></i> Teachers
                    </a>
                    <ul class="collapse list-unstyled ps-3" id="teacherMenu">
                        <?php if ($_SESSION['role_id'] == 1): // admin ?>
                            <li><a href="add_teacher.php" class="nav-link text-white"><i class="fas fa-user-plus"></i> Add
                                    Teacher</a></li>
                        <?php endif; ?>
                        <li><a href="view_teachers.php" class="nav-link text-white"><i class="fas fa-list"></i> View
                                Teachers</a></li>
                    </ul>
                </li>
            <?php endif; ?>

            <!-- Students Section -->
            <?php if ($_SESSION['role_id'] == 1 || $_SESSION['role_id'] == 2): // admin or lecturer ?>
                <li class="nav-item">
                    <a class="nav-link text-white" data-bs-toggle="collapse" href="#studentMenu" role="button"
                        aria-expanded="false" aria-controls="studentMenu">
                        <i class="fas fa-user-graduate"></i> Students
                    </a>
                    <ul class="collapse list-unstyled ps-3" id="studentMenu">
                        <?php if ($_SESSION['role_id'] == 1): // admin ?>
                            <li><a href="add_student.php" class="nav-link text-white"><i class="fas fa-user-plus"></i> Add
                                    Student</a></li>
                        <?php endif; ?>
                        <li><a href="view_students.php" class="nav-link text-white"><i class="fas fa-list"></i> View
                                Students</a></li>
                    </ul>
                </li>
            <?php endif; ?>

            <!-- Quiz Section -->
            <?php if ($_SESSION['role_id'] == 1 || $_SESSION['role_id'] == 2): // admin or teacher ?>
                <li class="nav-item">
                    <a class="nav-link text-white" data-bs-toggle="collapse" href="#quizMenu" role="button"
                        aria-expanded="false" aria-controls="quizMenu">
                        <i class="fas fa-book"></i> Quiz
                    </a>
                    <ul class="collapse list-unstyled ps-3" id="quizMenu">
                    <li>
                    <a href="quiz.php" class="nav-link text-white">
                            <i class="fas fa-clipboard-list"></i> Quiz Management
                        </a>
                    </li>
                    <li><a href="view_results.php" class="nav-link text-white">
                            <i class="fas fa-list"></i> View Results
                        </a>
                    </li>    
                    </ul>

                </li>
            <?php endif; ?>

            <?php if ($_SESSION['role_id'] == 3): // student ?>
                <li class="nav-item">
                    <a href="view_quiz.php" class="nav-link text-white">
                        <i class="fas fa-clipboard-list"></i> View Quiz
                    </a>
                </li>
            <?php endif; ?>

            <!-- FAQ Section -->
            <?php if ($_SESSION['role_id'] == 1 || $_SESSION['role_id'] == 2): // admin or lecturer ?>
                <li class="nav-item">
                    <a href="faq.php" class="nav-link text-white">
                        <i class="fas fa-info-circle"></i> Manage FAQ
                    </a>
                </li>
            <?php elseif ($_SESSION['role_id'] == 3): // student ?>
                <li class="nav-item">
                    <a href="view_faq.php" class="nav-link text-white">
                        <i class="fas fa-info-circle"></i> View FAQ
                    </a>
                </li>
            <?php endif; ?>



            <!-- Profile Section -->
            <li class="nav-item">
                <a href="profile.php" class="nav-link text-white">
                    <i class="fas fa-calendar-alt"></i> Profile
                </a>
            </li>

            <!-- Logout Button -->
            <li class="nav-item">
                <a href="./logout.php" class="nav-link text-white">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </li>
        </ul>
    </div>
</div>

<!-- Sidebar Script -->
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const toggle = document.getElementById('header-toggle'); // Hamburger icon
        const closeIcon = document.getElementById('header-close'); // Close icon
        const nav = document.getElementById('nav-bar'); // Sidebar

        // Initially, hide the close icon (for mobile view)
        closeIcon.style.display = 'none';

        // Toggle sidebar visibility and icons on hamburger click
        toggle.addEventListener('click', function () {
            nav.classList.toggle('show'); // Toggle sidebar visibility
            toggle.style.display = 'none'; // Hide hamburger icon
            closeIcon.style.display = 'block'; // Show close icon
        });

        // Close sidebar and switch icons when the 'X' is clicked
        closeIcon.addEventListener('click', function () {
            nav.classList.remove('show'); // Hide sidebar
            closeIcon.style.display = 'none'; // Hide close icon
            toggle.style.display = 'block'; // Show hamburger icon
        });
    });
</script>