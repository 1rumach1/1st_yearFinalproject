<?php
session_start();
// CONNECTION FILE
include "conn.php";
// QUERY FOR ADMIN LOG IN
if (isset($_POST["admin_name"]) && isset($_POST["admin_pass"])) {
    $admin_name = $_POST["admin_name"];
    $admin_pass = $_POST["admin_pass"];

    // Check for name & pass
    $result = $conn->query("SELECT * FROM admin WHERE admin='$admin_name' AND pass='$admin_pass'");
    if ($result->num_rows > 0) {
        // If match, set admin name in session
        $_SESSION['admin_name'] = $admin_name;

        // Redirect to admin dashboard or appropriate page
        header("Location: adm_home.php");
        exit();
    } else {
        // Wrong credentials
        echo "<script>alert('Invalid username or password. Please try again.');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login Page</title>
  <!-- Bootstrap CSS -->
  <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<!-- Login Form -->
  <div class="container mt-5">
    <div class="row justify-content-center">
      <div class="col-md-6">
        <h2 class="text-center mb-4">Admin Login</h2>
        <form method="post" action="admin.php">
          <div class="form-group">
            <input type="text" class="form-control" placeholder="Admin Username" name="admin_name" required>
          </div>
          <div class="form-group">
            <input type="password" class="form-control" placeholder="Admin Password" name="admin_pass" required>
          </div>
          <button type="submit" class="btn btn-primary btn-block">Login</button>
        </form>
      </div>
    </div>
  </div>
<!-- Bootstrap JS and dependencies -->
  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
