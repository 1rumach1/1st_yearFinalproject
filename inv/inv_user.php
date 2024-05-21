<?php
// SESSION
 session_start();
 
 if (!isset($_SESSION['user_name'])) {
     header('Location: inv_login.php');
     exit();
 }
 
 $user_name = $_SESSION['user_name'];
 include "conn.php";
 
// Query to get the user's details from the database
 $sql = "SELECT * FROM users WHERE user_name = '$user_name'";
 $result = mysqli_query($conn, $sql);
 
 if ($result && mysqli_num_rows($result) > 0) {
     $user = mysqli_fetch_assoc($result);
 } else {
     echo "No user found.";
     exit();
 }
 
 if (isset($_POST["newUsername"])) {
     $newUsername = $_POST["newUsername"];
     $userpin = $_POST["userpin"];
     $firstName = $_POST["firstName"];
     $lastName = $_POST["lastName"];
     $address = $_POST["address"];
     $email = $_POST["email"];
 
     // Check if an image is uploaded
     if (isset($_FILES["userImage"]) && $_FILES["userImage"]["error"] === UPLOAD_ERR_OK) {
         $userImage = addslashes(file_get_contents($_FILES["userImage"]["tmp_name"]));
         // Update the user with the new image
         $query = "UPDATE users SET user_name = '$newUsername', user_pin = '$userpin', f_name = '$firstName', l_name = '$lastName', address = '$address', email = '$email', image = '$userImage' WHERE user_name = '$user_name'";
     } else {
         // Update the user without changing the image
         $query = "UPDATE users SET user_name = '$newUsername', user_pin = '$userpin', f_name = '$firstName', l_name = '$lastName', address = '$address', email = '$email' WHERE user_name = '$user_name'";
     }
 
     if (mysqli_query($conn, $query) === TRUE) {
         // Update the session variable
         $_SESSION['user_name'] = $newUsername;
         
         echo "<script>
                 if (confirm('Account successfully updated. Click OK to continue.')) {
                     window.location.href = 'user.php';
                 }
               </script>";
     } else {
         echo "Error: " . $query . "<br>" . mysqli_error($conn);
     }
 }
 mysqli_close($conn);
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
        .form-group.side-by-side {
            display: flex;
            justify-content: space-between;
        }
        .form-group.side-by-side input {
            width: 48%;
        }
        .card-header img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            margin-right: 10px;
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
                        <li class="nav-item">
                            <a class="nav-link" href="#">Logout</a>
                        </li>
                    </ul>
                </div>
            </nav>

            <div class="col-md-9 ml-sm-auto col-lg-10">
                <!-- Header -->
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

                <!-- Main Content -->
                <main role="main" class="px-md-4">
                    <div class="card mt-4">
                        <div class="card-header d-flex align-items-center">
                            <img src='data:image/jpeg;base64,<?php echo base64_encode($user['image']); ?>' width='100' height='100' class='img-thumbnail'>
                            <h5 class="mb-0">Edit Account</h5>
                        </div>
                        <div class="card-body">
                            <form method="post" action="user.php" enctype="multipart/form-data">
                                <div class="form-group side-by-side">
                                    <input type="text" class="form-control" placeholder="Username" name="newUsername" value="<?php echo htmlspecialchars($user['user_name']); ?>" required>
                                    <input type="password" class="form-control" placeholder="Password" name="userpin" required>
                                </div>
                                <div class="form-group side-by-side">
                                    <input type="text" class="form-control" placeholder="First Name" name="firstName" value="<?php echo htmlspecialchars($user['f_name']); ?>" required>
                                    <input type="text" class="form-control" placeholder="Last Name" name="lastName" value="<?php echo htmlspecialchars($user['l_name']); ?>" required>
                                </div>
                                <div class="form-group side-by-side">
                                    <input type="text" class="form-control" placeholder="Address" name="address" value="<?php echo htmlspecialchars($user['address']); ?>" required>
                                    <input type="email" class="form-control" placeholder="Email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                                </div>
                                <div class="form-group">
                                    <input type="file" class="form-control-file" id="userImage" name="userImage" accept="image/jpeg, image/png">
                                </div>
                                <button type="submit" class="btn btn-primary btn-block">Update Account</button>
                            </form>
                        </div>
                    </div>
                </main>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
