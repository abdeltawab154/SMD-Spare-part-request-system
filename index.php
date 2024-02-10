<?php
session_start();

// Check if the requested page is the root URL
if ($_SERVER['REQUEST_URI'] == '/spare_part/') {
    // Redirect to the login page
    header("Location: login.html");
    exit();
}

// List of pages that users can access without logging in
$allowedPages = array('login.html', 'public_page.php', 'another_public_page.php');

// Check if the requested page is in the allowed list
$requestedPage = basename($_SERVER['REQUEST_URI']);
if (!in_array($requestedPage, $allowedPages) && !isset($_SESSION['username'])) {
    // Redirect to the login page if not logged in and trying to access a restricted page
    header("Location: login.html");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <title>SparePart Request System</title>
    <!-- Your HTML head content goes here -->
    <style>
        /* Your custom styles go here */
    </style>
</head>
<body>
    <!-- Your HTML body content goes here -->

    <!-- jQuery and Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
