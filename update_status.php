<?php
// update_pr_status.php

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
        echo "Error: " . $e->getMessage();
        die();
    }

    // Validate PO status data
    if (isset($_POST['PONumberStatus']) && is_array($_POST['PONumberStatus'])) {
        // Iterate through the submitted PO number status values and update the database
        foreach ($_POST['PONumberStatus'] as $itemID => $PONumberStatus) {
            // Check the current 'poNumberStatus' value in the database
            $stmtCheck = $pdo->prepare("SELECT PONumberStatus FROM items WHERE id = :itemID");
            $stmtCheck->bindParam(':itemID', $itemID);
            $stmtCheck->execute();
            $currentPoStatus = $stmtCheck->fetchColumn();

            // Update the 'poNumberStatus' and 'poNumberDate' columns only if the status is different
            if ($currentPoStatus !== $PONumberStatus) {
                $stmt = $pdo->prepare("UPDATE items SET PONumberStatus = :PONumberStatus, poNumberDate = NOW() WHERE id = :itemID");
                $stmt->bindParam(':PONumberStatus', $PONumberStatus);
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
    }

    // Validate PR status data
    if (isset($_POST['prStatus']) && is_array($_POST['prStatus'])) {
        // Iterate through the submitted PR status values and update the database
        foreach ($_POST['prStatus'] as $itemID => $prStatus) {
            // Check the current 'prStatus' value in the database
            $stmtCheck = $pdo->prepare("SELECT prStatus FROM items WHERE id = :itemID");
            $stmtCheck->bindParam(':itemID', $itemID);
            $stmtCheck->execute();
            $currentStatus = $stmtCheck->fetchColumn();

            // Update the 'prStatus' and 'prStatusDate' columns only if the status is different
            if ($currentStatus !== $prStatus) {
                $stmt = $pdo->prepare("UPDATE items SET prStatus = :prStatus, prStatusDate = NOW() WHERE id = :itemID");
                $stmt->bindParam(':prStatus', $prStatus);
                $stmt->bindParam(':itemID', $itemID);
                $stmt->execute();

                // Include a comment about the PR status update
                if (isset($_POST['CommentAboutPR'][$itemID])) {
                    $comment = $_POST['CommentAboutPR'][$itemID];
                    $stmtComment = $pdo->prepare("UPDATE items SET CommentAboutPR = :comment WHERE id = :itemID");
                    $stmtComment->bindParam(':comment', $comment);
                    $stmtComment->bindParam(':itemID', $itemID);
                    $stmtComment->execute();
                }
            }
        }
    }

    // Validate Receive status data
    if (isset($_POST['recieveStatus']) && is_array($_POST['recieveStatus'])) {
        // Iterate through the submitted receive status values and update the database
        foreach ($_POST['recieveStatus'] as $itemID => $recieveStatus) {
            // Check the current 'recieveStatus' value in the database
            $stmtCheck = $pdo->prepare("SELECT recieveStatus FROM items WHERE id = :itemID");
            $stmtCheck->bindParam(':itemID', $itemID);
            $stmtCheck->execute();
            $currentRecieveStatus = $stmtCheck->fetchColumn();

            // Update the 'recieveStatus' and 'recieveDate' columns only if the status is different
            if ($currentRecieveStatus !== $recieveStatus) {
                $stmt = $pdo->prepare("UPDATE items SET recieveStatus = :recieveStatus, recieveDate = NOW() WHERE id = :itemID");
                $stmt->bindParam(':recieveStatus', $recieveStatus);
                $stmt->bindParam(':itemID', $itemID);
                $stmt->execute();

                // Include a comment about the receive status update
                if (isset($_POST['CommentAboutReceving'][$itemID])) {
                    $comment = $_POST['CommentAboutReceving'][$itemID];
                    $stmtComment = $pdo->prepare("UPDATE items SET CommentAboutReceving = :comment WHERE id = :itemID");
                    $stmtComment->bindParam(':comment', $comment);
                    $stmtComment->bindParam(':itemID', $itemID);
                    $stmtComment->execute();
                }
            }
        }
    }

    // Redirect back to the original page
    header("Location: display.php");
    exit();
} else {
    die("Invalid status form submission.");
}
?>
