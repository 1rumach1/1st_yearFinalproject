<?php
session_start();

if (!isset($_SESSION['user_name'])) {
    header('Location: cash_login.php');
    exit();
}  
$user_name = $_SESSION['user_name'];
include "conn.php";
date_default_timezone_set('Asia/Manila');

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchasing Inventory</title>
    <!-- Bootstrap CSS -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <style>
        body{
            background-color:#AFDDE5;
        }
        /* Header Styles */
        .navbar {
            background-color: #116466; 
            border-radius: 50px;
        }
        .navbar-brand {
            color: #FFFFFF; /* White color */
            font-weight: bold;
            border-radius: 50%;
        }
        .navbar-brand img {
            border-radius: 50%;
        }
        /* Sidebar Styles */
        .sidebar {
            background-color: #003135; 
            height: 100vh; /* Set the sidebar height to 100% of the viewport height */
            padding: 0;
        }
        .sidebar-sticky .nav-link {
            font-weight: bold;
            color: #FFFFFF; /* White color */
        }
        .sidebar-heading {
            font-size: 1.2rem;
        }

        /* Ensure collapsed content fills the width of the sidebar */
        .collapse.show {
            width: 100%;
        }
        .sidebar-heading{
            padding-bottom: 20px;
            border-bottom: 2px solid white;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
<!---------------------------------------Sidebar------------------------------------------>
            <nav class="col-md-3 col-lg-2 d-md-block sidebar">
                <div class="sidebar-sticky">
                    <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-white">
                        NAVIGATION
                    </h6>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="user.php">USER</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="terminal.php">TERMINAL REPORT</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="pos.php">POINT OF SALES</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="cart.php">CART</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" type="button" data-toggle="collapse" data-target="#collapsIn" aria-expanded="false" aria-controls="collapsIn">REPORTS ^</a>
                            <div class="collapse" id="collapsIn">
                            <ul class="nav flex-column ml-3">
                                <li class="nav-item">
                                    <a class="nav-link" href="hourly.php">Hourly Report</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="sales.php">Sales Report</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="cashier_rep.php">Cashier's Report</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="product.php">Product Report</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="out.php">Cash Report</a>
                                </li>
                            </ul>
                            </div>
                        </li>
                        <!-- Updated link -->
                        <li class="nav-item">
                            <a class="nav-link" href="cash_login.php">Logout</a>
                        </li>
                    </ul>
                </div>
            </nav>

            <div class="col-md-9 ml-sm-auto col-lg-10">
<!------------------------------------------------- Header ------------------->
                <nav class="navbar navbar-expand-lg navbar-dark">
                    <a class="navbar-brand" href="cashier.php">
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

<!-------------------------------- Main Content --------------------------------------->
                <main role="main" class="px-md-4">
                    <div class=" table table-responsive ">
                        <h2>Product Report</h2>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Date Today</span>
                            </div>
                            <input class="form-control col-sm-2" type="text" name="Date" id="Date" required readonly value="<?php echo date('Y-m-d'); ?>">
                        </div>

<!-------------------select product-------->
                        <form action="product.php" method="get" class="form-inline">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <label class="input-group-text" for="prod_id">Products</label>
                                </div>
                                <select class="custom-select form-control" id="prod_id" name="prod_id" onchange="this.form.submit()">
                                <option value="">All Products</option>
                                <?php
                                    $prod_query = "SELECT * FROM inv_items";
                                    $prod_result = $conn->query($prod_query);
                                    while ($prod_row = $prod_result->fetch_assoc()) {
                                        $selected = (isset($_GET['prod_id']) && $_GET['prod_id'] == $prod_row['id']) ? 'selected' : '';
                                        echo "<option value='{$prod_row['id']}' $selected>{$prod_row['item_name']}</option>";
                                    }
                                ?>
                                </select>

                            </div>
                        </form>
                    
<!--------------------------------------display table------------------------------->
 <div style="max-height: 250px; overflow-y: auto;">
    <table class="table table-bordered table-dark table-hover table-striped">
        <thead class="thead-dark">
            <tr>
                <th>Time</th>
                <th>Person</th>
                <th>Item Name</th>
                <th>Item Price</th>
                <th>Quantity</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <?php
                // Assuming $conn is your MySQLi connection
                $prod_id = isset($_GET['prod_id']) ? $_GET['prod_id'] : '';

                if ($prod_id === '') {
                    $query = "SELECT * FROM sales WHERE report='' AND _date = CURRENT_DATE() AND cart='out' ORDER BY _time DESC" ;
                } else {
                    $query = "SELECT * FROM sales WHERE report='' AND _date = CURRENT_DATE() AND cart='out' AND item_id='$prod_id' ORDER BY _time DESC";
                }

                $result = $conn->query($query);

                if ($result->num_rows > 0) {
                    $overall_price = 0;
                    while ($row = $result->fetch_assoc()) {
                        $overall_price += $row["total"];
                        echo "<tr>";
                        echo "<td>" . $row["_time"] . "</td>";
                        echo "<td>" . $row["who"] . "</td>";
                        echo "<td>" . $row["item_name"] . "</td>";
                        echo "<td>₱" . $row["item_price"] . "</td>";
                        echo "<td>" . $row["quantity"] . "kg</td>";
                        echo "<td>₱" . $row["total"] . "</td>";
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
                            <label for="overall"><h4>Report Total</h4></label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">₱</span>
                                </div>
                                <input class="form-control col-sm-2" type="number" id="overall" required readonly value="<?php echo $overall_price  ?>">
                                <div class="input-group-append">
                                    <button class="btn btn-primary" onclick="print()">Print Product Report</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </main>
            </div>
        </div>
    </div>
<script>
        function print() {
            var overall = document.getElementById('overall').value;
            var prod_id = document.getElementById('prod_id').value;

            var url = 'p_report.php?overall=' + overall + "&prod_id=" + prod_id;      
            window.open(url);
        }
</script>
<!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
