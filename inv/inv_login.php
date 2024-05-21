<?php
// Start session
 session_start();

// CONNECTION FILE
 include "conn.php";

// QUERY FOR CREATE ACCOUNT
 if (isset($_POST["newUsername"])) { // Use $_POST instead of $_GET for form submissions

    $newUsername = $_POST["newUsername"];
    $userpin = $_POST["userpin"];
    $firstName = $_POST["firstName"];
    $lastName = $_POST["lastName"];
    $address = $_POST["address"];
    $email = $_POST["email"];
    
    // Check if an image is uploaded
    if(isset($_FILES["userImage"])) {
        $userImage = addslashes(file_get_contents($_FILES["userImage"]["tmp_name"]));
        // Insert the user into the database with the image
        $query = "INSERT INTO users (user_name, user_pin, f_name, l_name, address, email, image) 
                  VALUES ('$newUsername', '$userpin','$firstName','$lastName','$address','$email','$userImage')";
        if ($conn->query($query) === TRUE) {
            echo "<script>
                    if (confirm('Account successfully created. Click OK to continue.')) {
                        window.location.href = 'inv_login.php';
                    }
                  </script>";
        } else {
            echo "Error: " . $query . "<br>" . $conn->error;
        }
    } else {
        echo "Error: Image not uploaded.";
    }
 }
// QUERY FOR LOG IN
 if (isset($_POST["username"]) && isset($_POST["password"])) {
    $user_name = $_POST["username"];
    $user_pass = $_POST["password"];

    // check for name & pass
    $result = $conn->query("SELECT * FROM users WHERE user_name='$user_name' AND user_pin='$user_pass'");
    if ($result->num_rows > 0) {
        // Store username in session
        $_SESSION["user_name"] = $user_name;
        // Redirect to inventory page
        header("Location: inventory.php");
        exit();
    } else {
        // wrong something
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
        <h2 class="text-center mb-4">Inventory Login</h2>
        <form method="post" action="inv_login.php">
          <div class="form-group">
            <input type="text" class="form-control" placeholder="Username" name="username" required>
          </div>
          <div class="form-group">
            <input type="password" class="form-control" placeholder="Password" name="password" required>
          </div>
          <button type="submit" class="btn btn-primary btn-block">Login</button>
          <p class="mt-3 text-center">Don't have an account? <a href="#" data-toggle="modal" data-target="#registerModal">Register</a></p>
        </form>
      </div>
    </div>
  </div>

<!-- Register Modal -->
  <div class="modal fade" id="registerModal" tabindex="-1" aria-labelledby="registerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="registerModalLabel">Create Account</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form method="post" action="inv_login.php" enctype="multipart/form-data">
            <div class="form-group">
              <input type="text" class="form-control" placeholder="Username" name="newUsername" required>
            </div>
            <div class="form-group">
              <input type="password" class="form-control" placeholder="Password" name="userpin" required>
            </div>
            <div class="form-group">
              <input type="text" class="form-control" placeholder="First Name" name="firstName" required>
            </div>
            <div class="form-group">
              <input type="text" class="form-control" placeholder="Last Name" name="lastName" required>
            </div>
            <div class="form-group">
              <input type="text" class="form-control" placeholder="Address" name="address" required>
            </div>
            <div class="form-group">
              <input type="email" class="form-control" placeholder="Email" name="email" required>
            </div>
            <div class="form-group">
              <input type="file" class="form-control-file" id="userImage" name="userImage" accept="image/jpeg, image/png" required>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Create Account</button>
          </form>
        </div>
        <div class="modal-footer">
          <p class="mt-3 text-center">Already have an account? <a href="#" data-dismiss="modal" aria-label="Close">Login</a></p>
        </div>
      </div>
    </div>
  </div>

  <!-- Bootstrap JS and dependencies -->
  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
