<?php
session_start();

if (!isset($_SESSION['admin_name'])) {
    header('Location: admin.php');
    exit();
}  
$admin_name = $_SESSION['admin_name'];
include "conn.php";

// History
 if (isset($_GET["amt"])) {
    $amt = $_GET["amt"];
    $type = $_GET["type"];

    if ($type == "pullout") {
        $conn->query("INSERT INTO cash_history (who, type, in_amount, out_amount) 
                      VALUES ('$admin_name', '$type', 0, '$amt')");
    } elseif ($type == "deposit") {
        $conn->query("INSERT INTO cash_history (who, type, in_amount, out_amount) 
                      VALUES ('$admin_name', '$type', '$amt', 0)");
    }
 }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Home</title>
    <!-- Bootstrap CSS -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <style>
        body {
            background-color: #AFDDE5;
        }
        /* Header Styles */
        .navbar {
            background-color: #116466;
            border-radius: 50px;
        }
        .navbar-brand {
            color: #FFFFFF;
            font-weight: bold;
            border-radius: 50%;
        }
        .navbar-brand img {
            border-radius: 50%;
        }
        /* Sidebar Styles */
        .sidebar {
            background-color: #003135;
            height: 100vh;
            padding: 0;
        }
        .sidebar-sticky .nav-link {
            font-weight: bold;
            color: #FFFFFF;
        }
        .sidebar-heading {
            font-size: 1.2rem;
        }
        /* Ensure collapsed content fills the width of the sidebar */
        .collapse.show {
            width: 100%;
        }
        .sidebar-heading {
            padding-bottom: 20px;
            border-bottom: 2px solid white;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-3 col-lg-2 d-md-block sidebar">
                <div class="sidebar-sticky">
                    <h6 class="sidebar-heading d-flex align-items-center px-3 mt-4 mb-1 text-white">
                        NAVIGATION
                    </h6>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="adm_users.php">USERS</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" type="button" data-toggle="collapse" data-target="#collapseSale" aria-expanded="false" aria-controls="collapseSale">SALES</a>
                            <div class="collapse" id="collapseSale">
                                <ul class="nav flex-column ml-3">
                                    <li class="nav-item">
                                        <a class="nav-link" href="adm_prod.php">Product Information</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="adm_cash.php">Cashier Information</a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" type="button" data-toggle="collapse" data-target="#collapsIn" aria-expanded="false" aria-controls="collapsIn">INVENTORY</a>
                            <div class="collapse" id="collapsIn">
                                <ul class="nav flex-column ml-3">
                                    <li class="nav-item">
                                        <a class="nav-link" href="adm_cat.php">Categories</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="adm_item.php">Items</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="adm_request.php">Requests</a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="admin.php">Logout</a>
                        </li>
                    </ul>
                </div>
            </nav>

            <div class="col-md-9 ml-sm-auto col-lg-10">
                <!-- Header -->
                <nav class="navbar navbar-expand-lg navbar-dark">
                    <a class="navbar-brand" href="adm_home.php">
                        <img src="rice_icon.png" alt="Rice Icon" height="30" class="mr-2 rounded-circle"> Lupe's Bigasan
                    </a>
                    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarNav">
                        <ul class="navbar-nav ml-auto">
                        </ul>
                    </div>
                </nav>

                <!-- Main Content -->
                <main role="main" class="px-md-4">
                    <div class="table table-responsive">
                        <h2>Cashier Information</h2>
                        <form action="">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Date Today</span>
                                </div>
                                <input class="form-control col-sm-2" type="text" name="Date" id="Date" required readonly value="<?php echo date('Y-m-d'); ?>">

                                <div class="input-group-prepend">
                                    <span class="input-group-text">₱</span>
                                </div>
                                <input class="form-control col-sm-2" type="number" name="_amt" id="_amt" required>

                                <div class="input-group-prepend">
                                    <span class="input-group-text">Type</span>
                                </div>
                                <select class="form-control col-sm-2" name="_type" id="_type" required>
                                    <option value="deposit">Deposit</option>
                                    <option value="pullout">Pullout</option>
                                </select>
                                <div class="input-group-append">
                                    <button class="btn btn-primary" type="button" onclick="Commit()">Commit</button>
                                </div>
                            </div>
                        </form>

                        <div style="max-height: 250px; overflow-y: auto;">
                            <table class="table table-bordered table-dark table-hover table-striped">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>Time</th>
                                        <th>Person</th>
                                        <th>Type</th>
                                        <th>In-Amount</th>
                                        <th>Out-Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        $query = "SELECT * FROM cash_history WHERE _date = CURRENT_DATE() ORDER BY id DESC";
                                        $result = $conn->query($query);
                                        
                                        // Initialize overall price
                                        $overall_price = 0;
                                        
                                        if ($result->num_rows > 0) {
                                            while ($row = $result->fetch_assoc()) {
                                                // Update overall price
                                                $overall_price += $row["in_amount"] - $row["out_amount"];
                                                
                                                echo "<tr>";
                                                echo "<td>" . $row["_time"] . "</td>";
                                                echo "<td>" . $row["who"] . "</td>";
                                                echo "<td>" . $row["type"] . "</td>";
                                                echo "<td>₱" . $row["in_amount"] . "</td>";
                                                echo "<td>₱" . $row["out_amount"] . "</td>";
                                                echo "</tr>";
                                            }
                                        } else {
                                            echo "<tr><td colspan='6'>No Sales</td></tr>";
                                        }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="form-group">
                            <label for="overall"><h4>Cashier Balance</h4></label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">₱</span>
                                </div>
                                <input class="form-control col-sm-2" type="number" id="overall" required readonly value="<?php echo $overall_price; ?>">
                            </div>
                        </div>
                    </div>
                </main>
            </div>
        </div>
    </div>
<script>
    function Commit() {
        if (confirm("Push Transaction?")) {
            var amt = document.getElementById('_amt').value;
            var type = document.getElementById('_type').value;
            var url = 'adm_cash.php?amt=' + amt + "&type=" + type;
            window.location.href = url;
        }
    }
</script>
<!-- Bootstrap JS and dependencies -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
