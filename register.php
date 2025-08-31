<?php
include 'conn.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $mobileno = trim($_POST['mobileno']);
    $password = $_POST['password'];

    // Simple server-side validation
    $error = "";
    if(empty($username) || empty($email) || empty($mobileno) || empty($password)){
        $error = "All fields are required!";
    } elseif (!preg_match("/^\d{10}$/", $mobileno)) {
        $error = "Mobile number must be 10 digits!";
    } else {
        // Check if email already exists
        $check_email = "SELECT userid FROM user WHERE email='$email'";
        $result = mysqli_query($conn, $check_email);
        if(mysqli_num_rows($result) > 0){
            $error = "Email already registered!";
        } else {
            // Store password directly (NOT SECURE)
            $insert = "INSERT INTO user (username, email, mobileno, password) VALUES ('$username', '$email', '$mobileno', '$password')";
            if(mysqli_query($conn, $insert)){
                echo "<script>alert('Registration successful!'); window.location.href='login.php';</script>";
                exit();
            } else {
                $error = "Error registering user!";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Register</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href='register.css'>
</head>
<body>
<div class="form-container">
  <h2>Create Account</h2>

  <?php if(isset($error) && $error != ""){ echo "<div class='error-message show'>{$error}</div>"; } ?>

  <form id="registerForm" method="POST" action=""  novalidate>
    <div class="input-group">
      <input type="text" name="username" id="username" placeholder="Username" required>
      <label for="username">Username</label>
      <div id="usernameError" class="error-message"></div>
    </div>

    <div class="input-group">
      <input type="email" name="email" id="email" placeholder="Email" required>
      <label for="email">Email</label>
      <div id="emailError" class="error-message"></div>
    </div>

    <div class="input-group">
      <input type="text" name="mobileno" id="mobileno" placeholder="Mobile Number" required>
      <label for="mobileno">Mobile Number</label>
      <div id="mobileError" class="error-message"></div>
    </div>

    <div class="input-group">
      <input type="password" name="password" id="password" placeholder="Password" required>
      <label for="password">Password</label>
      <div id="passwordError" class="error-message"></div>
    </div>

    <div class="input-group">
      <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm Password" required>
      <label for="confirm_password">Confirm Password</label>
      <div id="confirmPasswordError" class="error-message"></div>
    </div>

    <button type="submit">Register</button>
  </form>
  <p>Already have an account? <a href="login.php">Login</a></p>
</div>

<script>
document.getElementById("registerForm").addEventListener("submit", function(e) {
    let valid = true;

    // Clear all previous error messages
    document.querySelectorAll(".error-message").forEach(el => {
        el.classList.remove("show");
        el.innerText = "";
    });

    let username = document.getElementById("username").value.trim();
    let email = document.getElementById("email").value.trim();
    let mobile = document.getElementById("mobileno").value.trim();
    let password = document.getElementById("password").value;
    let confirm_password = document.getElementById("confirm_password").value;

    // Username validation
    if (!username) {
        let err = document.getElementById("usernameError");
        err.innerText = "⚠ Username is required.";
        err.classList.add("show");
        valid = false;
    } else if (username.length < 3) {
        let err = document.getElementById("usernameError");
        err.innerText = "⚠ Username must be at least 3 characters.";
        err.classList.add("show");
        valid = false;
    }

    // Email validation
    if (!email) {
        let err = document.getElementById("emailError");
        err.innerText = "📧 Email is required.";
        err.classList.add("show");
        valid = false;
    } else if (!/^[^ ]+@[^ ]+\.[a-z]{2,3}$/.test(email)) {
        let err = document.getElementById("emailError");
        err.innerText = "📧 Please enter a valid email.";
        err.classList.add("show");
        valid = false;
    }

    // Mobile validation
    if (!mobile) {
        let err = document.getElementById("mobileError");
        err.innerText = "📱 Mobile number is required.";
        err.classList.add("show");
        valid = false;
    } else if (!/^\d{10}$/.test(mobile)) {
        let err = document.getElementById("mobileError");
        err.innerText = "📱 Please enter a valid 10-digit mobile number.";
        err.classList.add("show");
        valid = false;
    }

    // Password validation
    if (!password) {
        let err = document.getElementById("passwordError");
        err.innerText = "🔑 Password is required.";
        err.classList.add("show");
        valid = false;
    } else if (password.length < 3) {
        let err = document.getElementById("passwordError");
        err.innerText = "🔑 Password must be at least 3 characters.";
        err.classList.add("show");
        valid = false;
    }

    // Confirm password validation
    if (!confirm_password) {
        let err = document.getElementById("confirmPasswordError");
        err.innerText = "❌ Confirm Password is required.";
        err.classList.add("show");
        valid = false;
    } else if (password !== confirm_password) {
        let err = document.getElementById("confirmPasswordError");
        err.innerText = "❌ Passwords do not match.";
        err.classList.add("show");
        valid = false;
    }

    // Stop form submission if invalid
    if (!valid) {
        e.preventDefault();
    }
});

</script>
</body>
</html>
