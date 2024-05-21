<?php
session_start();

if (!isset($_SESSION['user_name'])) {
    header('Location: cash_login.php');
    exit();
}  
$user_name = $_SESSION['user_name'];
include "conn.php";

// DELETE QUERY
 if (isset($_GET["del_id"])) {
    $conn->query("DELETE FROM sales WHERE id='{$_GET['del_id']}'");
    header("Location: cart.php");
    exit();
 }

// FETCH DATA FROM SALES TABLE WHERE CART IS 'IN'
 $sql = "SELECT * FROM sales WHERE cart = 'in'";
 $result = $conn->query($sql);

 // Initialize an empty array to store fetched data
 $salesData = array();

// Fetch data and store in the array
 while ($row = $result->fetch_assoc()) {
     $salesData[] = $row;
 }

// UPDATE CART QUERY
 if (isset($_GET["up_cart"])) {
    foreach($salesData as $row) {
        $item_id = $row['item_id'];
        $item_name = $row['item_name'];
        $item_price = $row['item_price'];
        $quantity = $row['quantity'];
        $total = $row['total'];

        // Insert into inv_history table
        $conn->query("INSERT INTO inv_history (item_id, who, item, type, in_quantity, out_quantity) 
                      VALUES ('$item_id', '$user_name', '$item_name', 'sold', 0, $quantity)");
        
    }
    // Initialize total sales variable
       $totalSales = 0;
        // Calculate total sales
        foreach($salesData as $row) {
            $totalSales += $row['total'];
        }
        // Insert total sales into cash_history table
        $conn->query("INSERT INTO cash_history (who, type, in_amount, out_amount) 
                      VALUES ('$user_name', 'sale', $totalSales, 0)");
        

    // Update cart status
    $conn->query("UPDATE sales SET cart='out' WHERE cart='in'");
    
    // Redirect to pos.php after updating cart
    header("Location: pos.php");
    exit();
 }
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
                <div class="table-responsive">
                        <h2>Cart Contents</h2>
                    <div style="max-height: 330px; overflow-y: auto;">
                        <table class="table table-bordered table-dark table-hover table-striped">
                            <thead class="thead-dark">
                                <tr>
                                    <th>Item Name</th>
                                    <th>Item Price</th>
                                    <th>Quantity</th>
                                    <th>Total</th>
                                    <th>Cancel</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Output data of each row and calculate overall price
                                $result = $conn->query("SELECT * FROM sales WHERE cart = 'in' ORDER BY _time DESC");
                                // Check if there are rows returned
                                if ($result->num_rows > 0) {
                                    // Initialize overall price
                                    $overall_price = 0;
                                    // Loop through each row
                                    while ($row = $result->fetch_assoc()) {
                                        // Add to the overall price
                                        $overall_price += $row["total"];
                                        // Output table row for each item
                                        echo "<tr>";
                                        echo "<td>" . $row["item_name"] . "</td>";
                                        echo "<td>₱" . $row["item_price"] . "</td>";
                                        echo "<td>" . $row["quantity"] . "kg</td>";
                                        echo "<td>₱" . $row["total"] . "</td>";
                                        echo "<td><button class='btn btn-danger btn-sm' onclick='mydel(" . $row['id'] . ")'>Cancel</button></td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    // If no rows found
                                    echo "<tr><td colspan='5'>No items in the cart</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                        <!-- Overall Price -->
                        <div class="form-group">
                            <label for="overall"><h4>Overall Price</h4></label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">₱</span>
                                </div>
                                <input class="form-control col-sm-2" type="number" id="overall" required readonly value="<?php echo $overall_price; ?>">
                                <div class="input-group-append">
                                    <button class="btn btn-primary" onclick="openCheckoutDialog()">Checkout</button>
                                    <button class="btn btn-success" onclick="NextCustomer('out')">Next Customer</button>
                                </div>
                            </div>
                        </div>

                    </div>
                </main>
            </div>
        </div>
    </div>
<!---------CheckOut dialog----------------------->
    <dialog id="his-dialog" class="item_his" style="width: 200px; max-height: 80vh; overflow-y: auto;">
    <div class="modal-header">
        <h5 class="modal-title">Payment</h5>
    </div>
    <form id="receiptForm" method="post" action="cart.php" target="_blank">
        <div class="form-group">
            <label for="overall">Overall Price:</label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text">₱</span>
                </div>
                <input class="form-control col-sm-10" type="number" id="overall" name="overall" required readonly value="<?php echo $overall_price; ?>">
            </div>
        </div>
        <div class="form-group">
            <label for="pay">Pay:</label>
            <div class="input-group">
                 <div class="input-group-prepend">
                    <span class="input-group-text">₱</span>
                </div>
            <input class="form-control col-sm-10" type="number" id="pay" name="pay" required oninput="computeChange()">
            </div>
        </div>
        <div class="form-group">
            <label for="change">Change:</label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text">₱</span>
                </div>
                <input class="form-control col-sm-10" type="number" id="change" name="change" readonly>
            </div>
        </div>
        <button class="btn btn-primary" type="button" onclick="submitReceiptForm()">Checkout</button>
        <button class="btn btn-danger" type="button" onclick="closeHisDialog()">X</button>
    </form>
 </dialog>
<script>
    function computeChange() {
        var overall = parseFloat(document.getElementById('overall').value);
        var payment = parseFloat(document.getElementById('pay').value);
        var change =   payment - overall;
        document.getElementById('change').value = change.toFixed(2); 
    }
    function mydel(myid) {
        if (confirm("Cancel Order?")) {
            window.location.href = "cart.php?del_id=" + myid;
        }
    }
    function NextCustomer(status) {
        if (confirm("Create a new set of Orders?")) {
            window.location.href = "cart.php?up_cart=" + status;
        }
    }
    function openCheckoutDialog() {
        var hdialog = document.getElementById('his-dialog');
        hdialog.showModal();
    }
    function Checkout() {
        if (confirm("CheckOut?")) {
            window.location.href = "cart.php?check=";
        }
    }
    function submitReceiptForm() {
        var overall = document.getElementById('overall').value;
        var pay = document.getElementById('pay').value;
        var change = document.getElementById('change').value;
        var url = 'checkout.php?overall=' + overall + '&pay=' + pay + '&change=' + change;
        window.open(url, '_blank');
        // Redirect to cart.php
        window.location.href = 'cart.php';
    }
    <?php if (isset($_GET["check"])) { ?>
        var hdialog = document.getElementById('his-dialog');
        hdialog.showModal();
    <?php } ?>
    function closeHisDialog() {
        var edialog = document.getElementById('his-dialog');
        edialog.close();
    }
</script>
<!-- Bootstrap JS and dependencies -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
