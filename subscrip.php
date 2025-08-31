<?php
session_start();
include 'conn.php'; // DB connection

// Redirect if not logged in
if (!isset($_SESSION['userid'])) {
    header("Location: login.php");
    exit();
}

$userid = $_SESSION['userid'];

// Log login once per session
if (!isset($_SESSION['login_logged'])) {
    $stmt = $conn->prepare("INSERT INTO login (userid, login_time) VALUES (?, NOW())");
    $stmt->bind_param("i", $userid);
    $stmt->execute();
    $_SESSION['login_logged'] = true;
}

// Fetch user subscriptions
$sql = "SELECT id, name, price, renewal_date, cycle 
        FROM subscriptions 
        WHERE userid = ? 
        ORDER BY renewal_date ASC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userid);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Subscription Dashboard</title>

<!-- Google Material Icons -->
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
<link href="subscrip.css" rel="stylesheet">
</head>
<body>
  
<header>
  <h1>My Subscriptions</h1>
  <div class="header-actions">
    <a href="add_subscription.php" class="add-btn">
      <span class="material-icons" style="vertical-align: middle;">add</span>
      Add Subscription
    </a>
    <button class="toggle-btn" id="darkModeBtn" onclick="toggleDarkMode()">
      <span class="material-icons" id="modeIcon">brightness_6</span>
    </button>
  </div>
</header>


<main class="container">
<?php
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "
        <div class='card'>
          <h2><span class='material-icons'>subscriptions</span> {$row['name']}</h2>
          <div class='details'>
            <p><strong>Price:</strong> {$row['price']}</p>
            <p><strong>Renewal Date:</strong> " . date("M d, Y", strtotime($row['renewal_date'])) . "</p>
            <p><strong>Cycle:</strong> {$row['cycle']}</p>
          </div>
          <div class='action-btns'>
            <a href='edit_subscription.php?id={$row['id']}' class='edit-btn'>Edit</a>
            <a href='delete_subscription.php?id={$row['id']}' class='delete-btn' onclick=\"return confirm('Are you sure you want to delete this subscription?');\">Delete</a>
          </div>
        </div>
        ";
    }
} else {
    echo "<p>No subscriptions found for your account.</p>";
}
?>
</main>

<footer>
  &copy; 2025 Subscription Dashboard. All rights reserved.
</footer>

<script>
function toggleDarkMode() {
  document.body.classList.toggle("dark-mode");
  const icon = document.getElementById("modeIcon");
  icon.textContent = document.body.classList.contains("dark-mode") ? "wb_sunny" : "brightness_3";
}
</script>
</body>
</html>
