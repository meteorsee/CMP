<script>
    // Time in milliseconds for inactivity before triggering the alert
    const inactivityTime = 300000; // 5 minutes
    let timeout;
    // Function to show alert message when session expires
    function showAlert() {
        alert("Your session has expired due to inactivity. Please log in again.");

        window.location.href = "<?php echo SITEURL; ?>login.php"; // Redirect to login page
    }

    // Reset the timeout on user activity
    function resetTimer() {
        clearTimeout(timeout);
        timeout = setTimeout(showAlert, inactivityTime);
    }

    // Events to reset the timer
    window.onload = resetTimer; // Reset timer on page load
    window.onmousemove = resetTimer; // Reset timer on mouse movement
    window.onkeypress = resetTimer; // Reset timer on key press
</script>