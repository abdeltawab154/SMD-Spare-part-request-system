<?php
// update_po_status.php

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    session_start();

    // Validate CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("CSRF token validation failed.");
    }

    // Connect to the database
    $host = "localhost";
    $dbname = "spare_part";
    $user = "root";
    $password = "root";

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Enable error reporting
    } catch (PDOException $e) {
        echo "Error: Unable to connect to the database. " . $e->getMessage();
        die();
    }

    // Iterate through the submitted PO number status values and update the database
    if (isset($_POST['poNumberStatus']) && isset($_POST['commentAboutPO'])) {
        foreach ($_POST['poNumberStatus'] as $itemID => $poStatus) {
            // Check the current 'poNumberStatus' value in the database
            $stmtCheck = $pdo->prepare("SELECT poNumberStatus FROM items WHERE id = :itemID");
            $stmtCheck->bindParam(':itemID', $itemID);
            $stmtCheck->execute();
            $currentPoStatus = $stmtCheck->fetchColumn();

            // Update the 'poNumberStatus' and 'poNumberDate' columns only if the status is different
            if ($currentPoStatus !== $poStatus) {
                $stmt = $pdo->prepare("UPDATE items SET poNumberStatus = :poStatus, poNumberDate = NOW() WHERE id = :itemID");
                $stmt->bindParam(':poStatus', $poStatus);
                $stmt->bindParam(':itemID', $itemID);
                $stmt->execute();

                // Include a comment about the PO status update
                if (isset($_POST['commentAboutPO'][$itemID]) && !empty($_POST['commentAboutPO'][$itemID])) {
                    $poComment = $_POST['commentAboutPO'][$itemID];
                    $stmtPoComment = $pdo->prepare("UPDATE items SET CommentAboutPO = :poComment WHERE id = :itemID");
                    $stmtPoComment->bindParam(':poComment', $poComment);
                    $stmtPoComment->bindParam(':itemID', $itemID);
                    $stmtPoComment->execute();
                }
            }
        }
    } else {
        die("Invalid form submission.");
    }

    // Redirect back to the original page
    header("Location: display.php");
    exit();
}
?>
