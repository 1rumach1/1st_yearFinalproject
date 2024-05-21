<?php
//SESSION
  session_start();
  
  if (!isset($_SESSION['user_name'])) {
      header('Location: inv_login.php');
      exit();
  }  
  $user_name = $_SESSION['user_name'];
  include "conn.php";
// History
  if (isset($_GET["item_quan"])) {
    $item_id = $_GET["item_id"];
    $item_name = $_GET["item_name"];
    $trans_type = $_GET["trans_type"];
    $item_quan = $_GET["item_quan"];

    if ($trans_type == "PullOut" || $trans_type == "Wasteges") {
        $conn->query("INSERT INTO inv_history (item_id, who, item, type, in_quantity, out_quantity) 
                      VALUES ('$item_id', '$user_name', '$item_name', '$trans_type', 0, '$item_quan')");
    } elseif ($trans_type == "Delivery" || $trans_type == "Transfer") {
        $conn->query("INSERT INTO inv_history (item_id, who, item, type, in_quantity, out_quantity) 
                      VALUES ('$item_id', '$user_name', '$item_name', '$trans_type', '$item_quan', 0 )");
    }
 } 
// VARIANCE
   // Collect the form data
   if (isset($_GET["phy_count"])) {
      $vitem_id = $_GET['vitem_id'];
      $user_name = $_GET['user_name'];
      $item_name = $_GET['item_name'];
      $phy_count = $_GET['phy_count'];
      $sys_count = $_GET['sys_count'];
      
      // Calculate the variance
      $variance = $sys_count - $phy_count;
      
      // Determine the result
      if ($variance > 0) {
          $result = 'Surplus';
      } elseif ($variance < 0) {
          $result = 'Deficit';
      } else {
          $result = 'Equal';
      }
      
      // Create the SQL query to insert data
      $sql = "INSERT INTO inv_variance (date_time, item_id, who, item, phy_count, sys_count, variance, type) 
      VALUES (NOW(), $vitem_id, '$user_name', '$item_name', '$phy_count', '$sys_count', '$variance', '$result')";

      
      // Execute the query
      if ($conn->query($sql) === TRUE) {
          echo "Record saved successfully";
      } else {
          echo "Error: " . $sql . "<br>" . $conn->error;
      }
      header("Location: inv_items.php?vitem_id=$vitem_id&item_name=$item_name&balance=$sys_count");
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
<!---------------------------------Sidebar---------------------->
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
                <!-- Sort by Category -->
                <div class="category-sort mt-3">
                    <form action="inv_items.php" method="get" class="form-inline">
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
<!-- Display Table---------------------------------->
                <div class="display-items mt-3">
                    <h3>Inventory Items</h3>
                    <div style="max-height: 400px; overflow-y: auto;">
                    <table class="table table-bordered table-dark table-hover table-striped">
                        <thead class="thead-dark">
                            <tr>
                                <th>Item Image</th>
                                <th>Item Code</th>
                                <th>Item Name</th>
                                <th>Floor Price</th>
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
                                $query = "SELECT * FROM inv_items WHERE cat_id='$category_id' ORDER BY id DESC";
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
                                echo "<td>{$row['item_code']}</td>";
                                echo "<td>{$row['item_name']}</td>";
                                echo "<td>₱{$row['item_price']}</td>";
                                echo "<td>{$balance}kg</td>";
                                echo "<td>
                                        <button class='btn btn-info btn-sm' onclick='prodhis({$row['id']}, \"{$row['item_name']}\")'>History</button>
                                        <button class='btn btn-primary btn-sm' onclick='variance({$row['id']}, \"{$row['item_name']}\", {$balance})'>Variance</button>

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
                </main>
            </div>
        </div>
    </div>
<!-------------------------History FORM------------------------------->
    <dialog id="his-dialog" class="item_his" style="width: 80vw; max-height: 80vh; overflow-y: auto;">
        <div class="modal-header">
            <h5 class="modal-title">ITEM HISTORY"</h5>
        </div>
        <b>Item:</b> <?php echo $_GET["item_name"]; ?>
        <form method="get" action="inv_items.php" class="form-inline">
            <div class="form-group mr-2">
                <input type="hidden" name="item_id" value="<?php echo $_GET["item_id"]; ?>">
                <input type="hidden" name="adm_name" value="<?php echo $user_name; ?>">
                <input type="hidden" name="item_name" value="<?php echo $_GET["item_name"]; ?>">
                <label for="item_quan" class="mr-2">Quantity:</label>
                <input class="form-control" type="number" name="item_quan" required>
            </div>
            <div class="form-group mr-2">
                <label for="trans_type" class="mr-2">Transaction Type:</label>
                <select class="form-control" name="trans_type">
                    <option value="PullOut">PullOut</option>
                    <option value="Wasteges">Wasteges</option>
                    <option value="Transfer">Transfer</option>
                </select>
            </div>
            <input class="btn btn-primary mr-2" type="submit" value="Add">
            <button class="btn btn-secondary" type="button" onclick="closeHisDialog()">Close</button>
        </form>
        <table class="table table-bordered  table-hover table-striped">
            <thead>
                <tr>
                    <th>Date and Time</th>
                    <th>Person</th>
                    <th>Item</th>
                    <th>Type</th>
                    <th>In</th>
                    <th>Out</th>
                    <th>Balance</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $item_id = $_GET['item_id'];
                $query = "SELECT * FROM inv_history WHERE item_id='" . $item_id . "' ORDER BY id DESC";
                $result = $conn->query($query);                
                $balance = 0;
                while ($row = $result->fetch_assoc()) {
                    $balance += $row["in_quantity"] - $row["out_quantity"];
                    echo "<tr>";
                    echo "<td>" . $row['date_time'] . "</td>";
                    echo "<td>" . $row['who'] . "</td>";
                    echo "<td>" . $row['item'] . "</td>";
                    echo "<td>" . $row['type'] . "</td>";
                    echo "<td>" . $row['in_quantity'] . "kg</td>";
                    echo "<td>" . $row['out_quantity'] . "kg</td>";
                    echo "<td>" . $balance . "</td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    </dialog>
<!------------Variance Form---------------------->
    <dialog id="var-dialog" class="item_his" style="width: 80vw; max-height: 80vh; overflow-y: auto;">
        <div class="modal-header">
            <h5 class="modal-title">ITEM Variance</h5>
        </div>
        <b>Item:</b> <?php echo htmlspecialchars($_GET["item_name"], ENT_QUOTES, 'UTF-8'); ?>
        <form method="get" action="inv_items.php" class="form-inline">
            <div class="form-group mr-2">
                <input type="hidden" name="vitem_id" value="<?php echo htmlspecialchars($_GET["vitem_id"], ENT_QUOTES, 'UTF-8'); ?>">
                <input type="hidden" name="user_name" value="<?php echo htmlspecialchars($user_name, ENT_QUOTES, 'UTF-8'); ?>">
                <input type="hidden" name="item_name" value="<?php echo htmlspecialchars($_GET["item_name"], ENT_QUOTES, 'UTF-8'); ?>">
                <label for="sys_count" class="mr-2">SYSTEM COUNT:</label>
                <input class="form-control" type="number" name="sys_count" value="<?php echo htmlspecialchars($_GET["balance"], ENT_QUOTES, 'UTF-8'); ?>" required readonly>
                <label for="phy_count" class="mr-2">PHYSICAL COUNT:</label>
                <input class="form-control" type="number" name="phy_count" required>
            </div>
            <input class="btn btn-primary mr-2" type="submit" value="Save">
            <button class="btn btn-secondary" type="button" onclick="closeVarDialog()">Close</button>
        </form>
        <table class="table table-bordered table-hover table-striped">
            <thead>
                <tr>
                    <th>Date and Time</th>
                    <th>Person</th>
                    <th>Item</th>
                    <th>System Count</th>
                    <th>Physical Count</th>
                
                    <th>Variance</th>
                    <th>Result</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Fetch and display existing records
                $vitem_id = $_GET["vitem_id"];
                $result = $conn->query("SELECT * FROM inv_variance WHERE item_id='" . $_GET["vitem_id"] . "' ORDER BY id DESC");
                
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>{$row['date_time']}</td>"; 
                    echo "<td>{$row['who']}</td>"; 
                    echo "<td>{$row['item']}</td>"; 
                    echo "<td>{$row['sys_count']}kg</td>"; 
                    echo "<td>{$row['phy_count']}kg</td>"; 
                    echo "<td>{$row['variance']}kg</td>"; 
                    echo "<td>{$row['type']}</td>"; 
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    </dialog>

<!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
<script>
        function prodhis(item_id,item_name) {
        if (confirm("Check Item History?")) {
                window.location.href = "inv_items.php?item_id=" + item_id + "&item_name=" + item_name;
          }
        }
        function closeHisDialog() {
        var edialog = document.getElementById('his-dialog');
        edialog.close();
        }
        function variance(item_id,item_name,balance) {
        if (confirm("Compute Variance?")) {
                window.location.href = "inv_items.php?vitem_id=" + item_id + "&item_name=" + item_name + "&balance=" + balance;
          }
        }
        <?php if (isset($_GET["item_id"])) { ?>
            var hdialog = document.getElementById('his-dialog');
            hdialog.showModal();
        <?php } ?>
        <?php if (isset($_GET["vitem_id"])) { ?>
            var vdialog = document.getElementById('var-dialog');
            vdialog.showModal();
        <?php } ?>
        function closeVarDialog() {
            document.getElementById('var-dialog').close();
        }
</script>
</html>
