<?php
session_start(); // Start the session if not already started

// Connect to the database (replace these variables with your actual database credentials)
$host = "localhost";
$username = "root";
$password = "root";
$database = "spare_part";

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $itemName = $conn->real_escape_string($_POST["itemName"]);
    $codeType = $conn->real_escape_string($_POST["codeType"]);
    $itemCode = $conn->real_escape_string($_POST["itemCode"]);
    $description = $conn->real_escape_string($_POST["description"]);
    $qty = $conn->real_escape_string($_POST["qty"]);
    $pricePerOne = $conn->real_escape_string($_POST["pricePerOne"]);
    $reasonForQty = $conn->real_escape_string($_POST["reasonForQty"]);
    $team = $conn->real_escape_string($_POST["team"]);

    // Additional: Get username from session
    if (isset($_SESSION['username'])) {
        $username = $conn->real_escape_string($_SESSION['username']);
    } else {
        // Handle the case where the username is not available in the session
        $username = "UnknownUser";
    }

    // Calculate total price
    $totalPrice = $qty * $pricePerOne;

    // Insert data into the database
    $sql = "INSERT INTO items (itemName, codeType, itemCode, description, qty, pricePerOne, totalPrice, team, reasonForQty, MailSentUser)
            VALUES ('$itemName', '$codeType', '$itemCode', '$description', '$qty', '$pricePerOne', '$totalPrice', '$team', '$reasonForQty', '$username')";

    if ($conn->query($sql) === TRUE) {
        echo "<h2>Item Details</h2>";
        echo "<p><strong>Item Name:</strong> $itemName</p>";
        echo "<p><strong>Code Type:</strong> $codeType</p>";
        echo "<p><strong>Item Code:</strong> $itemCode</p>";
        echo "<p><strong>Description:</strong> $description</p>";
        echo "<p><strong>Quantity:</strong> $qty</p>";
        echo "<p><strong>Price Per One:</strong> $$pricePerOne</p>";
        echo "<p><strong>Total Price:</strong> $$totalPrice</p>";
        echo "<p><strong>Mail Sent User:</strong> $username</p>";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    // Close the database connection
    $conn->close();
} else {
    // Redirect to the form if accessed directly
    header("Location: sparepart.html");
    exit();
}
?>
