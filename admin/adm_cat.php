<?php
  session_start();
  if (!isset($_SESSION['admin_name'])) {
      header('Location: admin.php');
      exit();
  }
  // Retrieve admin name from session
  $admin_name = $_SESSION['admin_name'];
  include "conn.php";
  
  if (isset($_GET["add_cat"])) {
      $add_cat = $_GET["add_cat"];
      $conn->query("INSERT INTO inv_cat (category) VALUES ('$add_cat')");
  } elseif (isset($_GET["edit_id"]) && isset($_GET["edit_cat"])) {
      $edit_id = $_GET["edit_id"];
      $edit_cat = $_GET["edit_cat"];
      $conn->query("UPDATE inv_cat SET category='$edit_cat' WHERE id='$edit_id'");
      header("Location: adm_cat.php");
      exit();
  } elseif (isset($_GET["del_id"])) {
      $conn->query("DELETE FROM inv_cat WHERE id='{$_GET['del_id']}'");
      header("Location: adm_cat.php");
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
<!-- Sidebar -->
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
                <div class="container mt-4">
        <div class="modal" id="add_modal">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add Category</h5>
                        <button type="button" class="close" onclick="closeModal('add_modal')">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="adm_cat.php" method="get">
                            <input type="text" name="add_cat" class="form-control" placeholder="Enter new category">
                            <button type="submit" class="btn btn-primary btn-block mt-2">Add</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal" id="edit_modal">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Category</h5>
                        <button type="button" class="close" onclick="closeModal('edit_modal')">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="adm_cat.php" method="get">
                            <input type="text" name="edit_cat" class="form-control" value='<?php echo isset($_GET["oldcat"]) ? $_GET["oldcat"] : ''; ?>'>
                            <input type="hidden" name="edit_id" value='<?php echo isset($_GET["edit_id"]) ? $_GET["edit_id"] : ''; ?>'>
                            <button type="submit" class="btn btn-primary btn-block mt-2">Update</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <h2>Categories List</h2>
        <button class="btn btn-primary mb-3" onclick="displayModal('add_modal')">Add</button>
        <div style="max-height: 400px; overflow-y: auto;">
        <table class="table table-bordered table-dark table-hover table-striped">
            <thead>
                <tr>
                    <th>Delete</th>
                    <th>Edit</th>
                    <th>Category</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $result = $conn->query("SELECT * FROM inv_cat ORDER BY category");
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td><button class='btn btn-danger' onclick='mydel({$row['id']})'>Delete</button></td>";
                    echo "<td><button class='btn btn-warning' onclick='myupdate({$row['id']}, \"{$row['category']}\")'>Edit</button></td>";
                    echo "<td>{$row['category']}</td>";
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

    <script>
        function mydel(myid) {
            if (confirm("Delete this record?")) {
                window.location.href = "adm_cat.php?del_id=" + myid;
            }
        }

        function myupdate(id, oldcat) {
            if (confirm("Update this record?")) {
                window.location.href = "adm_cat.php?edit_id=" + id + "&oldcat=" + encodeURIComponent(oldcat);
            }
        }

        function displayModal(modalId) {
            let modal = document.getElementById(modalId);
            modal.style.display = 'block';
        }

        function closeModal(modalId) {
            let modal = document.getElementById(modalId);
            modal.style.display = 'none';
            if (modalId === 'edit_modal') {
                window.location.href = 'adm_cat.php';
            }
        }

        <?php if (isset($_GET["edit_id"])) { ?>
            displayModal('edit_modal');
        <?php } ?>
    </script>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
