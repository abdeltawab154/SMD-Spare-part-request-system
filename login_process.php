<?php
session_start();

// Database connection details
$host = "localhost";
$dbname = "spare_part";
$user = "root";
$password = "root";

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize user input
    $enteredUsername = filter_var($_POST['username'], FILTER_SANITIZE_STRING);
    $enteredPassword = filter_var($_POST['password'], FILTER_SANITIZE_STRING);

    try {
        // Create a PDO connection
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Prepare and execute a query to fetch user details from the database
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username LIMIT 1");
        $stmt->bindParam(':username', $enteredUsername);
        $stmt->execute();
        $userDetails = $stmt->fetch(PDO::FETCH_ASSOC);

        // Check if a user with the entered username exists and verify the password
        if ($userDetails && $enteredPassword === $userDetails['password']) {
            // Authentication successful
            $_SESSION['username'] = $enteredUsername;
            $_SESSION['name'] = $userDetails['name']; // Retrieve and store user's name

            // Regenerate session ID for security
            session_regenerate_id(true);

            header("Location: display.php"); // Redirect to the main application page
            exit();
        } else {
            // Authentication failed
            $error_message = "Invalid username or password. Please try again.";
        }
    } catch (PDOException $e) {
        // Log errors instead of exposing them to users
        error_log("Database Error: " . $e->getMessage());
        $error_message = "An unexpected error occurred. Please try again later.";
    }
}

// If the form is not submitted or authentication fails, redirect to the login page with an error message
header("Location: login.php?error=" . urlencode($error_message));
exit();
?>
