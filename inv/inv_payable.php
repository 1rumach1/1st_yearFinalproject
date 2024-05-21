<?php
session_start();

if (!isset($_SESSION['user_name'])) {
    header('Location: inv_login.php');
    exit();
}  
$user_name = $_SESSION['user_name'];
include "conn.php";

// Update and ADD QUERY
 if (isset($_GET["com_id"])) {
    $com_id = $_GET["com_id"];
    $who = $_GET["who"];
    $item_id = $_GET["item_id"];
    $item_name = $_GET["item_name"];
    $quantity = $_GET["quantity"];
    // Update status 
    $conn->query("UPDATE inv_requests SET status='Completed' WHERE id=$com_id");
    // Add to another table
    $conn->query("INSERT INTO inv_history (item_id, who, item, type, in_quantity, out_quantity) 
                      VALUES ('$item_id', '$who', '$item_name', 'Delivery', '$quantity', 0)");
    header("Location: inv_order.php");
    exit();
 }
// Fetch all rows to calculate the total
 $rows = [];
 $total_remain = 0;
 $result = $conn->query("SELECT * FROM inv_requests WHERE status='Ordered' ORDER BY id");
 while ($row = $result->fetch_assoc()) {
    $rows[] = $row;
    $total_remain += $row['remain'];
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
        body {
            background-color: #AFDDE5;
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
        .sidebar-heading {
            padding-bottom: 20px;
            border-bottom: 2px solid white;
        }
        /* Custom width for input */
        .custom-width {
            width: 150px; /* Adjust the width as needed */
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
                            <a class="nav-link" href="inv_user.php">USER</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" type="button" data-toggle="collapse" data-target="#collapsIn" aria-expanded="false" aria-controls="collapsIn">INVENTORY</a>
                            <div class="collapse" id="collapsIn">
                                <ul class="nav flex-column ml-3">
                                    <li class="nav-item">
                                        <a class="nav-link" href="inv_items.php">Items</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="inv_request.php">Purchase Request</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="inv_order.php">Purchase Order</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="inv_payable.php">Accounts Payable</a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                        <!-- Updated link -->
                        <li class="nav-item">
                            <a class="nav-link" href="inv_login.php">Logout</a>
                        </li>
                    </ul>
                </div>
            </nav>

            <div class="col-md-9 ml-sm-auto col-lg-10">
<!------------------------------------------------- Header ------------------->
                <nav class="navbar navbar-expand-lg navbar-dark">
                    <a class="navbar-brand" href="inventory.php">
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
                    <h2>Accounts Payable: 
                        <input class="form-control custom-width" value="₱<?php echo number_format($total_remain, 2); ?>" type="text" readonly required>
                    </h2>
                    <table class="table table-bordered table-dark table-hover table-striped">
                        <thead>
                            <tr>
                                <th>Requestor</th>
                                <th>Item</th>
                                <th>Supplier</th>
                                <th>Date Required</th>
                                <th>Reason</th>
                                <th>Quantity</th>
                                <th>Unit Price</th>
                                <th>Downpayment</th>
                                <th>Remaining Price</th>
                                <th>On Arrival</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach ($rows as $row) {
                                echo "<tr>";
                                echo "<td>{$row['who']}</td>";
                                echo "<td>{$row['item']}</td>";
                                echo "<td>{$row['supplier']}</td>";
                                echo "<td>{$row['date_required']}</td>";
                                echo "<td>{$row['reason']}</td>";
                                echo "<td>{$row['quantity']}kg</td>";
                                echo "<td>₱{$row['unit_price']}</td>";
                                echo "<td>₱{$row['down']}</td>";
                                echo "<td>₱{$row['remain']}</td>";
                                echo "<td><button class='btn btn-primary btn-sm' onclick='deliver({$row['id']},\"{$row['who']}\",{$row['item_id']},\"{$row['item']}\",{$row['quantity']})'>Pay & Store</button></td>";
                                echo "</tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </main>
            </div>
        </div>
    </div>
<script>
        function deliver(myid, who, item_id, item_name, quantity) {
            if (confirm("Pay and Commit to Inventory?")) {
                var encodedItemName = encodeURIComponent(item_name);
                var encodedWho = encodeURIComponent(who);
                var url = "inv_payable.php?com_id=" + myid + "&who=" + who + "&item_id=" + item_id + "&item_name=" + item_name + "&quantity=" + quantity;
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
