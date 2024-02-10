<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <title>Display Item Details</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            padding-top: 20px;
            padding-bottom: 50px;
            background-color: #f8f9fa;
        }

        .container {
            max-width: 2600px;
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

        .user-info {
            color: #ffffff;
            font-size: 1.2rem;
            margin-bottom: 0;
        }

        .table-container {
            overflow-x: auto;
            margin-top: 20px;
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
            white-space: nowrap;
        }

        th {
            background-color: #2A2E30;
            color: #ffffff;
        }

        td {
            background-color: #f8f9fa;
        }

        tr:hover {
            background-color: #f5f5f5;
        }

        .form-group {
            margin-bottom: 20px;
        }
        
    </style>
</head>

<body>

    <!-- Header Section -->
    <header class="header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-2">
                    <img src="logo.jpg" alt="Your Logo" class="logo">
                </div>
                <div class="col-md-7">
                    <h1 class="header-title">SparePart Request System - Display Data</h1>
                </div>
                <div class="col-md-3 text-right">
                    <?php
                    session_start();
                    $name = isset($_SESSION['name']) ? $_SESSION['name'] : 'Guest';
                    $username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Guest';
                    ?>
                    <p class="user-info"><i class="fas fa-user"></i> <?= $name ?></p>
                    <a href="logout.php" class="btn btn-danger">Logout <i class="fas fa-sign-out-alt"></i></a>
                    <a href="sparepart.php" class="btn btn-primary">Spare Parts Request <i class="fas fa-cogs"></i></a>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content Container -->
    <div class="container mt-4">
        <div class="table-container">
            <div class="form-group">
                <label for="searchInput">Search:</label>
                <input type="text" id="searchInput" class="form-control">
            </div>
            <h2 class="text-center mb-4">Item Details</h2>

            <?php
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

            $stmt = $pdo->prepare("SELECT * FROM items ORDER BY id DESC");
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (count($result) > 0) {
                $csrf_token = bin2hex(random_bytes(32));
                $_SESSION['csrf_token'] = $csrf_token;
                ?>
                <form method="post" action="update_status.php" class="mt-3">
                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover">
                            <thead class="thead-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Item Name</th>
                                    <th>Code Type</th>
                                    <th>Item Code</th>
                                    <th>Description</th>
                                    <th>Quantity</th>
                                    <th>Price Per One</th>
                                    <th>Total Price</th>
                                    <th>Team</th>
                                    <th>Reason for Quantity</th>
                                    <th>Request Date</th>
                                    <th>PR Approval Status </th>
                                    <th>PR Status Decision Date</th>
                                    <th>Comment About PR</th>
                                    <th>PONumberStatus</th>
                                    <th>PONumberDate</th>
                                    <th>Comment About PO</th>
                                    <th>Receive Status</th>
                                    <th>Receive Date</th>
                                    <th>Comment About Receiving</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($result as $row) : ?>
                                    <tr class="item-row">
                                        <td><?= $row['id'] ?></td>
                                        <td><?= $row['itemName'] ?></td>
                                        <td><?= $row['codeType'] ?></td>
                                        <td><?= $row['itemCode'] ?></td>
                                        <td><?= $row['description'] ?></td>
                                        <td><?= $row['qty'] ?></td>
                                        <td><?= $row['pricePerOne'] ?></td>
                                        <td><?= $row['totalPrice'] ?></td>
                                        <td><?= $row['team'] ?></td>
                                        <td><?= $row['reasonForQty'] ?></td>
                                        <td><?= $row['MailSentDate'] ?></td>
                                        <td>
                                            <select name="prStatus[<?= $row['id'] ?>]" class="form-control">
                                                <option value="Pending" <?= ($row['prStatus'] == 'Pending') ? 'selected' : '' ?>>Pending</option>
                                                <option value="Approved" <?= ($row['prStatus'] == 'Approved') ? 'selected' : '' ?>>Approved</option>
                                                <option value="Rejected" <?= ($row['prStatus'] == 'Rejected') ? 'selected' : '' ?>>Rejected</option>
                                                <option value="RFQ" <?= ($row['prStatus'] == 'RFQ') ? 'selected' : '' ?>>RFQ</option>
                                                <option value="Cancel" <?= ($row['prStatus'] == 'Cancel') ? 'selected' : '' ?>>Cancel</option>
                                            </select>
                                        </td>
                                        <td><?= $row['prStatusDate'] ?></td>
                                        <td>
                                            <textarea name="CommentAboutPR[<?= $row['id'] ?>]" class="form-control" placeholder="Add a comment"><?= $row['CommentAboutPR'] ?></textarea>
                                        </td>
                                        <td>
                                            <select name="PONumberStatus[<?= $row['id'] ?>]" class="form-control">
                                                <option value="Pending" <?= ($row['PONumberStatus'] == 'Pending') ? 'selected' : '' ?>>Pending</option>
                                                <option value="Approved" <?= ($row['PONumberStatus'] == 'Approved') ? 'selected' : '' ?>>Approved</option>
                                                <option value="Rejected" <?= ($row['PONumberStatus'] == 'Rejected') ? 'selected' : '' ?>>Rejected</option>
                                                <option value="RFQ" <?= ($row['PONumberStatus'] == 'RFQ') ? 'selected' : '' ?>>RFQ</option>
                                                <option value="Cancel" <?= ($row['PONumberStatus'] == 'Cancel') ? 'Cancel' : '' ?>>Cancel</option>
                                            </select>
                                        </td>
                                        <td><?= $row['PONumberDate'] ?></td>
                                        <td>
                                            <textarea name="commentAboutPO[<?= $row['id'] ?>]" class="form-control" placeholder="Add a comment"><?= $row['CommentAboutPO'] ?></textarea>
                                        </td>
                                        <td>
                                            <select name="recieveStatus[<?= $row['id'] ?>]" class="form-control">
                                                <option value="Pending" <?= ($row['recieveStatus'] == 'Pending') ? 'selected' : '' ?>>Pending</option>
                                                <option value="Received" <?= ($row['recieveStatus'] == 'Received') ? 'selected' : '' ?>>Received</option>
                                                <option value="Not Received" <?= ($row['recieveStatus'] == 'Not Received') ? 'selected' : '' ?>>Not Received</option>
                                            </select>
                                        </td>
                                        <td><?= $row['recieveDate'] ?></td>
                                        <td>
                                            <textarea name="CommentAboutReceving[<?= $row['id'] ?>]" class="form-control" placeholder="Add a comment"><?= $row['CommentAboutReceving'] ?></textarea>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <?php
                            function checkUpdatesExist($result, $statusColumn, $commentColumn)
                            {
                                foreach ($result as $row) {
                                    if ($row[$statusColumn] !== $row[$commentColumn] && !empty($row[$statusColumn])) {
                                        return true;
                                    }
                                }
                                return false;
                            }

                            $authorizedUsers = ['mohamed.ahm', 'admin'];
                            if (isset($_SESSION['username']) && in_array($_SESSION['username'], $authorizedUsers)) {
                                $updatesExistPR = checkUpdatesExist($result, 'prStatus', 'CommentAboutPR');
                                $updatesExistPO = checkUpdatesExist($result, 'PONumberStatus', 'CommentAboutPO');
                                $updatesExistReceive = checkUpdatesExist($result, 'recieveStatus', 'CommentAboutReceving');

                                if ($updatesExistPR || $updatesExistPO || $updatesExistReceive) {
                            ?>

                                    <button type="submit" name="update_po_status" class="btn btn-success mr-2">Update Status <i class="fas fa-file-invoice-dollar"></i></button>

                            <?php
                                } else {
                                    echo '<p class="text-warning">No updates available for the selected actions.</p>';
                                }
                            } else {
                                echo '<p class="text-danger">Unauthorized Access - You do not have permission to update.</p>';
                            }
                            ?>
                        </div>
                    </div>
                </form>
                <?php
            } else {
                echo '<p class="text-center">No data available.</p>';
            }
            ?>
        </div>
    </div>

    <div class="modal fade" id="itemDetailsModal" tabindex="-1" role="dialog" aria-labelledby="itemDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="itemDetailsModalLabel">Item Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Content of the modal will be dynamically filled with row data -->
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery and Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.getElementById('searchInput').addEventListener('input', function () {
                let searchTerm = this.value.toLowerCase();
                filterTable(searchTerm);
            });

            document.querySelectorAll('.item-row').forEach(function (row) {
                row.addEventListener('click', function (event) {
                    if (event.target.cellIndex < 3) {
                        displayItemDetails(this);
                    }
                });
            });

            function filterTable(searchTerm) {
                let rows = document.querySelectorAll('.item-row');
                rows.forEach(function (row) {
                    let rowText = row.innerText.toLowerCase();
                    row.style.display = rowText.includes(searchTerm) ? '' : 'none';
                });
            }

            function displayItemDetails(row) {
                let rowData = Array.from(row.cells).map(cell => cell.textContent.trim());

                let modalBody = document.querySelector('#itemDetailsModal .modal-body');
                modalBody.innerHTML = '';

                let dataLabels = ['ID', 'Item Name', 'Code Type', 'Item Code', 'Description', 'Quantity', 'Price Per One', 'Total Price', 'Team', 'Reason for Quantity', 'Request Date', 'PR Approval Status', 'PR Status Decision Date', 'Comment About PR', 'PONumberStatus', 'PONumberDate', 'Comment About PO', 'Receive Status', 'Receive Date', 'Comment About Receiving'];

                for (let i = 0; i < dataLabels.length; i++) {
                    let rowDetail = document.createElement('p');
                    rowDetail.innerHTML = `<strong>${dataLabels[i]}:</strong> ${rowData[i]}`;
                    modalBody.appendChild(rowDetail);
                }

                $('#itemDetailsModal').modal('show');
            }
        });
    </script>
</body>

</html>
