<?php
session_start();
if (!isset($_SESSION['username']) || !isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];
$email = $_SESSION['email'];
$mobileno = $_SESSION['mobileno'];
$last_login = $_SESSION['last_login'] ?? date('M d, Y');
?>

<!DOCTYPE html>
<html>
<head>
<title>My Profile</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
<style>
body {
    font-family: 'Poppins', sans-serif;
    margin: 0;
    height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    background: 
        linear-gradient(rgba(0,0,0,0.4), rgba(0,0,0,0.7)),
        url('https://images.unsplash.com/photo-1521737604893-d14cc237f11d?auto=format&fit=crop&w=1920&q=80') 
        no-repeat center center fixed;
    background-size: cover;
    color: white;
    font-size: 20px;
}

/* Profile Card */
.profile-container {
    width: 450px;
    background: rgba(255, 255, 255, 0.08);
    backdrop-filter: blur(20px);
    padding: 35px;
    border-radius: 20px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.4);
    text-align: center;
    animation: fadeIn 1s ease-in-out;
}

/* Profile Image Glow */
.profile-container img {
    border-radius: 50%;
    margin-bottom: 15px;
    border: 4px solid #fff;
    box-shadow: 0 0 20px rgba(255,255,255,0.5);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.profile-container img:hover {
    transform: scale(1.05);
    box-shadow: 0 0 30px rgba(255,255,255,0.8);
}

/* Logout Button */
.logout-btn {
    display: inline-block;
    margin-top: 25px;
    padding: 12px 20px;
    background: linear-gradient(135deg, #355206ff , #750640ff);
    color: white;
    border-radius: 10px;
    text-decoration: none;
    font-weight: 600;
    transition: 0.3s ease;
}
.logout-btn:hover {
    background: linear-gradient(135deg,#6c0c22ff, #4f0a24ff);
    transform: translateY(-3px);
    box-shadow: 0 5px 15px rgba(255,75,43,0.5);
}

/* Fade-in animation */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>
</head>
<body>
<div class="profile-container">
    <img src="https://cdn-icons-png.flaticon.com/128/847/847969.png"
<?= urlencode($email) ?> alt="Profile Picture">
    <h2><?= htmlspecialchars($username) ?></h2>
    <p><b>Email:</b> <?= htmlspecialchars($email) ?></p>
    <p><b>Mobile:</b> <?= htmlspecialchars($mobileno) ?></p>
    <p><b>Last Login:</b> <?= htmlspecialchars($last_login) ?></p>
    <a href="logout.php" class="logout-btn">Logout</a>
</div>
</body>
</html>
