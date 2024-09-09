<?php
// Start the session
session_start();
include('includes/db.php');

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Sanitize inputs
    $username = mysqli_real_escape_string($conn, $username);
    $password = mysqli_real_escape_string($conn, $password);

    // Fetch the user from the database
    $query = "SELECT * FROM users WHERE username = '$username'";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);

        // Verify the password (assuming passwords are hashed in the database)
        if (password_verify($password, $user['password'])) {
            // Set session variables
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_name'] = $user['username'];

            // Redirect to the dashboard (index.php)
            header("Location: index.php");
            exit;
        } else {
            // Invalid password
            echo "Invalid credentials. Please try again.";
        }
    } else {
        // User not found
        echo "Invalid credentials. Please try again.";
    }
}
?>
