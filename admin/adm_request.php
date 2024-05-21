<?php
session_start();

if (!isset($_SESSION['admin_name'])) {
    header('Location: admin.php');
    exit();
}  

include "conn.php";
// DELETE QUERY
 if (isset($_GET["del_id"])) {
    $conn->query("DELETE FROM inv_requests WHERE id='{$_GET['del_id']}'");
    header("Location: adm_request.php");
    exit();
 }
// APPROVE QUERY
 if (isset($_GET["apr_id"])) {
    $conn->query("UPDATE inv_requests SET status='Approved' WHERE id='{$_GET['apr_id']}'");
    header("Location: adm_request.php");
    exit();
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
<!------------------------ Sidebar ------------------------------->
            <nav class="col-md-3 col-lg-2 d-md-block sidebar">
                <div class="sidebar-sticky">
                    <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-white">
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
                        <!-- Updated link -->
                        <li class="nav-item">
                            <a class="nav-link" href="admin.php">Logout</a>
                        </li>
                    </ul>
                </div>
            </nav>

            <div class="col-md-9 ml-sm-auto col-lg-10">
<!------------------------------------------------- Header ------------------->
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

<!-------------------------------- Main Content --------------------------------------->
                <main role="main" class="px-md-4">
                <h2>Pending Requests</h2>
        <table class="table table-bordered table-dark table-stripped table-hover">
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
                    <th>Approval</th>
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
                    echo "<td><button class='btn btn-primary btn-sm' onclick='myapp({$row['id']})'>Approve</button>
                              <button class='btn btn-danger btn-sm' onclick='mydel({$row['id']})'>Cancel</button>
                          </td>";
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
        function mydel(myid) {
        if (confirm("Cancel Request?")) {
        window.location.href = "adm_request.php?del_id=" + myid;
        }
        }
        function myapp(myid) {
        if (confirm("Approve Request?")) {
        window.location.href = "adm_request.php?apr_id=" + myid;
        }
        }
</script>
    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
