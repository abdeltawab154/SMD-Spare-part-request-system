<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <title>Item Details</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f8f9fa;
            margin: 0;
        }

        .header {
            background-color: #2A2E30;
            padding: 10px 0;
            margin-bottom: 20px;
        }

        .header .container {
            display: flex;
            align-items: center;
        }

        .logo {
            width: 200px;
            height: auto;
            margin-right: 20px;
        }

        .header-title {
            color: #fff;
            margin: 0;
        }

        .header-links {
            margin-left: auto;
            display:  inline-flex;
            align-items: center;
            color: #fff;
        }

        .header-links p {
            margin: 0;
        }

        .header-links a {
            color: #fff;
            margin-left: 20px;
            text-decoration: none;
        }

        .header-links a:hover {
            text-decoration: underline;
        }

        .container {
            padding: 20px;
            max-width: 2200px;
            margin: auto;
        }

        form {
            margin-top: 20px;
        }

        .modal-header {
            background-color: #3498db;
            color: #fff;
        }

        #successImage {
            max-width: 100%;
            height: auto;
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
                <div class="col-md-8">
                    <h1 class="header-title">SparePart Request System</h1>
                </div>
                <div class="col-md-2 header-links">
                    <?php
                    session_start();
                    if (isset($_SESSION['name'])) {
                        echo '<p>Welcome, ' . $_SESSION['name'] . '!</p>';
                        echo '<a href="display.php"><i class="fas fa-sign-out-alt"></i> Mangement</a>';
                        echo '<a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>';
                    } else {
                        echo '<a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>';
                    }
                    ?>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content Container -->
    <div class="container">
        <form id="itemForm">
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="itemName">Item Name:</label>
                    <input type="text" class="form-control" id="itemName" name="itemName" placeholder="Enter Item Name" required>
                </div>
                <div class="form-group col-md-6">
                    <label for="codeType">Code Type:</label>
                    <select class="form-control" id="codeType" name="codeType" required>
                        <option value="IMK Code">IMK Code</option>
                        <option value="CIS Code">CIS Code</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="itemCode">Item Code:</label>
                <input type="text" class="form-control" id="itemCode" name="itemCode" placeholder="Enter Item Code" required>
            </div>

            <div class="form-group">
                <label for="description">Description:</label>
                <textarea class="form-control" id="description" name="description" placeholder="Enter Description" rows="3" required></textarea>
            </div>

            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="qty">Quantity:</label>
                    <input type="number" class="form-control" id="qty" name="qty" placeholder="Enter Quantity" required>
                </div>
                <div class="form-group col-md-6">
                    <label for="pricePerOne">Price Per One:</label>
                    <input type="number" step="0.01" class="form-control" id="pricePerOne" name="pricePerOne" placeholder="Enter Price Per One" required>
                </div>
            </div>

            <div class="form-group">
                <label for="team">Team:</label>
                <select class="form-control" id="team" name="team" required>
                    <option value="PBA">PBA</option>
                    <option value="Production">Production</option>
                    <option value="TSI">TSI</option>
                    <option value="PQS">PQS</option>
                    <option value="Repair">Repair</option>
                </select>
            </div>

            <div class="form-group">
                <label for="reasonForQty">Reason for Quantity:</label>
                <input type="text" class="form-control" id="reasonForQty" name="reasonForQty" placeholder="Enter Reason for Quantity" required>
            </div>

            <button type="button" class="btn btn-primary btn-block" id="submitBtn">
                <i class="fas fa-check"></i> Submit
            </button>
        </form>
    </div>

    <!-- Result Modal and JavaScript -->
    <div class="modal fade" id="resultModal" tabindex="-1" role="dialog" aria-labelledby="resultModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="resultModalLabel">Item Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p id="successMessage">Item details added successfully!</p>
                    <img src="submit.jpg" alt="Success Image" id="successImage">
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            $('#submitBtn').on('click', function () {
                if (validateForm()) {
                    handleFormSubmission();
                    $('#resultModal').modal('show');
                    setTimeout(function () {
                        $('#resultModal').modal('hide');
                    }, 5000);
                }
            });

            function handleFormSubmission() {
                $.ajax({
                    type: "POST",
                    url: "process.php",
                    data: $("#itemForm").serialize(),
                    success: function (response) {
                        console.log(response);
                    },
                    error: function (error) {
                        console.log(error);
                    }
                });
            }

            function validateForm() {
                var isValid = true;
                $('#itemForm input, #itemForm select, #itemForm textarea').each(function () {
                    if (!$(this).val()) {
                        isValid = false;
                        return false;
                    }
                });
                return isValid;
            }
        });
    </script>
</body>
</html>
