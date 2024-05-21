<?php
session_start();

if (!isset($_SESSION['user_name'])) {
    header('Location: inv_login.php');
    exit();
}  

include "conn.php";

// Request Query
  if(isset($_GET['who'])) {
      $who = $_GET['who'];
      $item_data = explode('&', $_GET['item_id_name']); // Split item_id_name
      $item_id = $item_data[0]; // First part is item_id
      $item_name = $item_data[1]; // Second part is item_name
      $supplier = $_GET['supplier'];
      $date = $_GET['date'];
      $date_required = $_GET['date_required'];
      $reason = $_GET['reason'];
      $quantity = $_GET['quantity'];
      $unit_price = $_GET['unit_price'];
      $total = $_GET['total'];
  
      // Insert data into database
      $query = "INSERT INTO inv_requests (who, item_id, item, supplier, date, date_required, reason, quantity, unit_price, total, status) 
      VALUES ('$who', '$item_id', '$item_name', '$supplier', '$date', '$date_required', '$reason', '$quantity', '$unit_price', '$total', 'Pending')";
      
      if ($conn->query($query) === TRUE) {
          echo "New record created successfully";
      } else {
          echo "Error: " . $query . "<br>" . $conn->error;
      }
  }
// DELETE QUERY
 if (isset($_GET["del_id"])) {
     $conn->query("DELETE FROM inv_requests WHERE id='{$_GET['del_id']}'");
     header("Location: inv_request.php");
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
<!------------------------------------------Sidebar------------------------------------------------->
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
                <h2>Pending Requests</h2>
        <button class="btn btn-primary mb-3" onclick="openDialog()">Make Request</button>
        <div style="max-height: 500px; overflow-y: auto;">
        <table class="table table-bordered table-dark table-hover table-striped">
            <thead>
                <tr>
                    <th>Requestor</th>
                    <th>Item</th>
                    <th>Supplier</th>
                    <th>Date Made</th>
                    <th>Date Required</th>
                    <th>Reason</th>
                    <th>Quantity</th>
                    <th>Unit Price</th>
                    <th>Total Price</th>
                    <th>Status</th>
                    <th>Cancel</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $result = $conn->query("SELECT * FROM inv_requests WHERE status='Pending' ORDER BY id");
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>{$row['who']}</td>";
                    echo "<td>{$row['item']}</td>";
                    echo "<td>{$row['supplier']}</td>";
                    echo "<td>{$row['date']}</td>";
                    echo "<td>{$row['date_required']}</td>";
                    echo "<td>{$row['reason']}</td>";
                    echo "<td>{$row['quantity']}kg</td>";
                    echo "<td>₱{$row['unit_price']}</td>";
                    echo "<td>₱{$row['total']}</td>";
                    echo "<td>{$row['status']}</td>";
                    echo "<td><button class='btn btn-danger btn-sm' onclick='mydel({$row['id']})'>x</button></td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
        </div>
                </main>
            </div>
        </div>
    </div>
<!----------------------------Request Dialog------------------------->    
  <dialog id="request_dialog" class="request_item" style="width: 80%; max-width: 1200px;">
    <form action="inv_request.php" method="get" id="request_form">
        <div class="modal-header">
            <h5 class="modal-title"><i>"REQUEST DELIVERY"</i></h5>
        </div>
        <div class="modal-body">
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="who">Requestor</label>
                    <input class="form-control" type='text' value='<?php echo $_SESSION['user_name']; ?>' name='who' required>
                </div>
                <div class="form-group col-md-6">
                    <label for="supplier">Supplier</label>
                    <input class="form-control" type='text' name='supplier' required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="item_id_name">Items:</label>
                    <select class="form-control" name="item_id_name" required>
                        <?php 
                            $result = $conn->query("SELECT * FROM inv_items");
                            while ($row = $result->fetch_assoc()) {
                                echo "<option value='$row[id]&$row[item_name]'>$row[item_name]</option>";
                            }
                        ?>
                    </select>
                </div>
                <div class="form-group col-md-6">
                    <label for="date">Date</label>
                    <input class="form-control" value='<?php echo date("d-m-Y"); ?>' type='text' name='date' required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="date_required">Date Required</label>
                    <input class="form-control" type='date' name='date_required' required>
                </div>
                <div class="form-group col-md-6">
                    <label for="reason">Reason:</label>
                    <input class="form-control" type='text' name='reason' required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="quantity">Quantity (kg):</label>
                    <input class="form-control" type='number' name='quantity' id="quantity" onchange="calculateTotal()" required>
                </div>
                <div class="form-group col-md-6">
                    <label for="unit_price">Unit Price(₱):</label>
                    <input class="form-control" type='number' name='unit_price' id="unit_price" onchange="calculateTotal()" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="total">Total Price</label>
                    <input class="form-control" type='number' name='total' id="total" readonly required>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <input class="btn btn-primary" type='submit' name='add_item' value='Add'>
            <button class="btn btn-secondary" type="button" onclick="closeDialog()">Close</button>
        </div>
    </form>
  </dialog>
<script>
        function mydel(myid) {
        if (confirm("Cancel Request?")) {
        window.location.href = "inv_request.php?del_id=" + myid;
        }
        }
        function openDialog() {
        var dialog = document.getElementById('request_dialog');
        dialog.showModal();
        }
        function closeDialog() {
        var edialog = document.getElementById('request_dialog');
        edialog.close();
        }
        function calculateTotal() {
        var quantity = document.getElementById('quantity').value;
        var unitPrice = document.getElementById('unit_price').value;
        var total = parseFloat(quantity) * parseFloat(unitPrice);
        document.getElementById('total').value = total.toFixed(2); // Round to 2 decimal places
    }
</script>
<!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
