<?php
session_start();

if (!isset($_SESSION['user_name'])) {
    header('Location: cash_login.php');
    exit();
}  
$user_name = $_SESSION['user_name'];
include "conn.php";

// ADD to CART
 if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $item_id = $_POST['item_id'];
    $item_name = $_POST['item_name'];
    $item_price = $_POST['item_price'];
    $quantity = $_POST['item_quan'];
    $total = $_POST['total_price'];
    $cart = 'in';  // Assuming cart and report are empty for now
    $report = '';

    $sql = "INSERT INTO sales (_date, _time, item_id, item_name, who, item_price, quantity, total, cart, report)
            VALUES (CURDATE(), CURTIME(), '$item_id', '$item_name', '$user_name', '$item_price', '$quantity', '$total', '$cart', '$report')";

  if ($conn->query($sql) === TRUE) {
      echo '<script>alert("Record inserted successfully");</script>';
  } else {
      echo '<script>alert("Error: ' . $sql . '<br>' . $conn->error . '");</script>';
  }
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
<!-- Sort by Category -->
                <div class="category-sort mt-3">
                    <form action="pos.php" method="get" class="form-inline">
                        <label for="category_id" class="mr-2">Sort by Category:</label>
                        <select class="form-control" name="category_id" id="category_id" onchange="this.form.submit()">
                            <option value="">All Categories</option>
                            <?php
                            $category_query = "SELECT * FROM inv_cat";
                            $category_result = $conn->query($category_query);
                            while ($category_row = $category_result->fetch_assoc()) {
                                $selected = (isset($_GET['category_id']) && $_GET['category_id'] == $category_row['id']) ? 'selected' : '';
                                echo "<option value='{$category_row['id']}' $selected>{$category_row['category']}</option>";
                            }
                            ?>
                        </select>
                    </form>
                </div>
<!-- Display Table -->
                    <div class="display-items mt-3">
                        <h3>Items for Sale</h3>
                        <div class="overflow-auto" style=" max-height: 400px; overflow-y: auto;">
                        <table class="table table-bordered table-dark table-hover ">
                            <thead class="thead-dark">
                                <tr>
                                    <th>Item Image</th>
                                    <th>Item Name</th>
                                    <th>Peso per kg.</th>
                                    <th>System Count</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $category_id = isset($_GET['category_id']) ? $_GET['category_id'] : '';
                    
                                if ($category_id === '') {
                                    // If "All Categories" is selected, fetch all items
                                    $query = "SELECT * FROM inv_items";
                                } else {
                                    // If a specific category is selected, filter by cat_id
                                    $query = "SELECT * FROM inv_items WHERE cat_id='$category_id'";
                                }
                                $result = $conn->query($query);
                                while ($row = $result->fetch_assoc()) {
                                    $item_id = $row['id'];
                    
                                    // Subquery to calculate the balance
                                    $balanceQuery = "SELECT SUM(in_quantity) - SUM(out_quantity) AS balance 
                                                     FROM inv_history 
                                                     WHERE item_id='$item_id'";
                                    $balanceResult = $conn->query($balanceQuery);
                                    $balanceRow = $balanceResult->fetch_assoc();
                                    $balance = $balanceRow['balance'] ?? 0;
                    
                                    echo "<tr>";
                                    echo "<td><img src='data:image/jpeg;base64," . base64_encode($row['image_data']) . "' width='100' height='100' class='img-thumbnail'></td>";
                                    echo "<td>{$row['item_name']}</td>";
                                    echo "<td>₱{$row['item_price']}</td>";
                                    echo "<td>{$balance}kg</td>";
                                    echo "<td>
                                            <button class='btn btn-primary btn-sm' onclick='addcart({$row['id']}, \"{$row['item_name']}\",{$row['item_price']})'>Add to Cart</button> 
                                          </td>";
                                    echo "</tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                        </div>
                    </div>
                </main>
            </div>
        </div>
    </div>
<!-----------------------------------Quantity Dialog------------------------------------->
 <dialog id="his-dialog" class="item_his" style="width: 200px; max-height: 80vh; overflow-y: auto;">
    <div class="modal-header">
        <h5 class="modal-title">How Many?</h5>
    </div>
    <b>Item:</b> <?php echo htmlspecialchars($_GET["item_name"]); ?>
    <form method="post" action="pos.php">
        <div class="form-group">
            <input type="hidden" name="item_id" value="<?php echo htmlspecialchars($_GET["citem_id"]); ?>">
            <input type="hidden" name="item_name" value="<?php echo htmlspecialchars($_GET["item_name"]); ?>">
        </div>
        <div class="form-group">
            <label for="item_price">Price per kg:</label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text">₱</span>
                </div>
                <input class="form-control col-sm-10" type="number" id="item_price" name="item_price" required readonly value="<?php echo htmlspecialchars($_GET["item_price"]); ?>">
            </div>
        </div>
        <div class="form-group">
            <label for="item_quan">Quantity:</label>
            <input class="form-control col-sm-10" type="number" id="item_quan" name="item_quan" required oninput="computeTotal()">
        </div>
        <div class="form-group">
            <label for="total_price">Total Price:</label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text">₱</span>
                </div>
                <input class="form-control col-sm-10" type="number" id="total_price" name="total_price" readonly>
            </div>
        </div>
        <input class="btn btn-primary mr-2" type="submit" value="Add">
        <button class="btn btn-secondary" type="button" onclick="closeHisDialog()">Close</button>
    </form>


 </dialog>

<script>
        function computeTotal() {
        // Get the item price and quantity values
        var itemPrice = parseFloat(document.getElementById('item_price').value);
        var itemQuantity = parseFloat(document.getElementById('item_quan').value);
        var totalPrice = itemPrice * itemQuantity;
        document.getElementById('total_price').value = totalPrice.toFixed(2); 
        }
        function addcart(item_id, item_name, item_price) {
        if (confirm("Add item to Cart?")) {
            var url = "pos.php?citem_id=" + item_id +  "&item_name=" + item_name + "&item_price=" + item_price;
            window.location.href = url;
        }
        }
        function closeHisDialog() {
        var edialog = document.getElementById('his-dialog');
        edialog.close();
        }
        <?php if (isset($_GET["citem_id"])) { ?>
            var hdialog = document.getElementById('his-dialog');
            hdialog.showModal();
        <?php } ?>
</script>
<!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
