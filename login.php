<?php
session_start();
include 'conn.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($email) || empty($password)) {
        $errorMsg = "⚠️ Please fill all fields";
    } else {
        // Step 1: Check in 'user' table
        $checkRegister = "SELECT userid, username, mobileno, email 
                          FROM user 
                          WHERE email='$email' AND password='$password'";
        $regResult = mysqli_query($conn, $checkRegister);

        if ($regResult && mysqli_num_rows($regResult) > 0) {
            // ✅ User found
            $user = mysqli_fetch_assoc($regResult);

            $_SESSION['userid']   = $user['userid'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['mobileno'] = $user['mobileno'];
            $_SESSION['email']    = $user['email'];

            // Remember Me feature
            if (isset($_POST['rememberMe'])) {
                setcookie('rememberedEmail', $email, time() + (86400 * 30), "/"); // 30 days
            } else {
                setcookie('rememberedEmail', '', time() - 3600, "/");
            }

            // Step 2: Check if user already exists in 'login' table
            $checkLogin = "SELECT * FROM login WHERE userid='" . $user['userid'] . "'";
            $loginResult = mysqli_query($conn, $checkLogin);

            if ($loginResult && mysqli_num_rows($loginResult) == 0) {
                // Step 3: Insert into login table (must include userid for foreign key)
                $insertLogin = "INSERT INTO login (userid, email, password) 
                                VALUES ('" . $user['userid'] . "', '$email', '$password')";
                if (!mysqli_query($conn, $insertLogin)) {
                    die("Insert failed: " . mysqli_error($conn));
                }
            }

            // Redirect to home page
            header("Location: index.php");
            exit;

        } else {
            // ❌ Not registered
            $errorMsg = "❌ You are not registered. Please register first.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Login</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
<link href="login.css" rel="stylesheet">
</head>

<body>
<div class="form-container">
  <h2>Login</h2>
  <?php if (!empty($errorMsg)) echo "<p class='error-message'>$errorMsg</p>"; ?>
  <form id="loginForm" method="POST" novalidate>
    <div class="input-group">
      <input type="email" name="email" id="email" placeholder="Email"
             value="<?php echo isset($_COOKIE['rememberedEmail']) ? $_COOKIE['rememberedEmail'] : ''; ?>" required>
      <label for="email">Email</label>
      <div id="emailError" class="error-message"></div>
    </div>
    <div class="input-group">
      <input type="password" name="password" id="password" placeholder="Password" required>
      <label for="password">Password</label>
      <div id="passwordError" class="error-message"></div>
    </div>
    <div class="input-group" style="text-align:left; margin-bottom: 20px; display:flex; align-items:center;">
      <input type="checkbox" id="rememberMe" name="rememberMe" style="width:20px; height:20px; margin-right:12px; cursor:pointer;"
             <?php echo isset($_COOKIE['rememberedEmail']) ? 'checked' : ''; ?>>
      <label for="rememberMe" style="position: static; transform: none; color:#fff; font-size:20px; font-weight:600; margin:0; cursor:pointer;">
        Remember Me
      </label>
    </div>
    <button type="submit">Login</button>
  </form>
  <p>Don't have an account? <a href="register.php">Register</a></p>
</div>

<script>
document.getElementById("loginForm").addEventListener("submit", function(e) {
    let valid = true;
    document.querySelectorAll(".error-message").forEach(el => {
        if (el.id !== 'emailError' && el.id !== 'passwordError') return;
        el.classList.remove("show");
        el.innerText = "";
    });

    let email = document.getElementById("email").value.trim();
    let password = document.getElementById("password").value;

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

    if (!password) {
        let err = document.getElementById("passwordError");
        err.innerText = "🔑 Password is required.";
        err.classList.add("show");
        valid = false;
    }

    if (!valid) e.preventDefault();
});
</script>
</body>
</html>
