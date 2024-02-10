<?php
// Include the database connection code (replace with your actual connection code)
$host = "localhost";
$dbname = "spare_part";
$user = "root";
$password = "root";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Error: Unable to connect to the database. " . $e->getMessage();
    die();
}

// Function to get the top 10 items
function getTopItems($pdo)
{
    $stmt = $pdo->prepare("SELECT itemCode, itemName, SUM(qty) as totalQty FROM items GROUP BY itemCode ORDER BY totalQty DESC LIMIT 10");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Function to get the number of items based on status
function getItemsCountByStatus($pdo, $status)
{
    $stmt = $pdo->prepare("SELECT COUNT(*) as itemCount FROM items WHERE prStatus = :status");
    $stmt->bindParam(':status', $status, PDO::PARAM_STR);
    $stmt->execute();
    return $stmt->fetchColumn();
}

// Function to get the total cost for received items by team within a time range
function getTotalCostForReceivedItemsByTeam($pdo, $startDate, $endDate)
{
    $stmt = $pdo->prepare("SELECT team, SUM(totalPrice) as totalCost FROM items
                            WHERE recieveDate BETWEEN :start_date AND :end_date
                            AND recieveStatus = 'Received'
                            GROUP BY team");
    $stmt->bindParam(':start_date', $startDate, PDO::PARAM_STR);
    $stmt->bindParam(':end_date', $endDate, PDO::PARAM_STR);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Function to get the number of received items
function getReceivedItemsCount($pdo)
{
    $stmt = $pdo->prepare("SELECT COUNT(*) as receivedItemsCount FROM items WHERE recieveStatus = 'Received'");
    $stmt->execute();
    return $stmt->fetchColumn();
}

// Function to get all items
function getAllItems($pdo)
{
    $stmt = $pdo->prepare("SELECT * FROM items");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Get data
$topItems = getTopItems($pdo);
$rejectedItemsCount = getItemsCountByStatus($pdo, 'Rejected');
$canceledItemsCount = getItemsCountByStatus($pdo, 'Canceled');
$receivedItemsCount = getReceivedItemsCount($pdo); // Updated to count based on recieveStatus
$notReceivedItemsCount = getItemsCountByStatus($pdo, 'Not Received');

// Get time range from the form submission
$startDate = isset($_POST['start-date']) ? $_POST['start-date'] : date('Y-m-d', strtotime('-1 week'));
$endDate = isset($_POST['end-date']) ? $_POST['end-date'] : date('Y-m-d');

// Get data with time filter for total cost for received items by team
$totalCostForReceivedItemsByTeam = getTotalCostForReceivedItemsByTeam($pdo, $startDate, $endDate);

$allItems = getAllItems($pdo);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <title>Data Analysis</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            padding-top: 20px;
            padding-bottom: 50px;
            background-color: #f8f9fa;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
        }

        .header {
            background-color: #2A2E30;
            color: #ffffff;
            padding: 10px 0;
        }

        .logo {
            max-width: 100%;
            height: auto;
        }

        .header-title {
            font-size: 1.5rem;
            margin-bottom: 0;
        }

        .analysis-container {
            margin-top: 20px;
        }

        .filter-form {
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th,
        td {
            padding: 16px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #2A2E30;
            color: #ffffff;
        }

        td {
            background-color: #f8f9fa;
        }
    </style>
</head>

<body>

    <!-- Header Section -->
    <header class="header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <img src="your-logo.png" alt="Your Logo" class="logo">
                </div>
                <div class="col-md-6 text-center">
                    <h1 class="header-title">Data Analysis</h1>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content Container -->
    <div class="container mt-4">
        <div class="analysis-container">

            <h2>Top 10 Requested Items</h2>
            <?php if (!empty($topItems)) : ?>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Item Code</th>
                            <th>Item Name</th>
                            <th>Total Quantity</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($topItems as $item) : ?>
                            <tr>
                                <td><?= $item['itemCode'] ?></td>
                                <td><?= $item['itemName'] ?></td>
                                <td><?= $item['totalQty'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else : ?>
                <p>No data available for top items.</p>
            <?php endif; ?>

            <h2>Item Status Summary</h2>
            <p>Number of Rejected Items: <?= $rejectedItemsCount ?></p>
            <p>Number of Canceled Items: <?= $canceledItemsCount ?></p>
            <p>Number of Received Items: <?= $receivedItemsCount ?></p>
            <p>Number of Not Received Items: <?= $notReceivedItemsCount ?></p>
            <!-- Filter Form -->
            <div class="filter-form">
                <form method="post">
                    <label for="start-date">Start Date:</label>
                    <input type="date" name="start-date" id="start-date" value="<?= $startDate ?>">
                    <label for="end-date">End Date:</label>
                    <input type="date" name="end-date" id="end-date" value="<?= $endDate ?>">
                    <button type="submit" class="btn btn-primary">Apply Filter</button>
                </form>
            </div>
            <h2>Total Cost for Received Items by Team</h2>
            <?php if (!empty($totalCostForReceivedItemsByTeam)) : ?>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Team</th>
                            <th>Total Cost</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($totalCostForReceivedItemsByTeam as $team) : ?>
                            <tr>
                                <td><?= $team['team'] ?></td>
                                <td><?= $team['totalCost'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else : ?>
                <p>No data available for total cost by team.</p>
            <?php endif; ?>

        </div>
    </div>

    <!-- jQuery and Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>
