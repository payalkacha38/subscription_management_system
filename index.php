<?php
session_start();
include 'conn.php'; // Your DB connection

// Check login
if (!isset($_SESSION['username']) || !isset($_SESSION['email']) || !isset($_SESSION['userid'])) {
    header("Location: login.php");
    exit();
}

// User info
$userid = $_SESSION['userid'];
$username = $_SESSION['username'];
$email = $_SESSION['email'];
$last_login = $_SESSION['last_login'] ?? date('M d, Y');
$mobileno = $_SESSION['mobileno'];

// === Dynamic Data Logic using userid ===

// Active subscriptions (fetch all for View More feature)
$subResult = mysqli_query($conn, "SELECT * FROM subscriptions WHERE userid='$userid' ORDER BY renewal_date ASC");
$activeSubs = [];
while($row = mysqli_fetch_assoc($subResult)) {
    $activeSubs[] = $row;
}

// Upcoming payments
$paymentResult = mysqli_query($conn, "SELECT * FROM subscriptions WHERE userid='$userid' AND renewal_date >= CURDATE() ORDER BY renewal_date ASC");
$payments = [];
while($row = mysqli_fetch_assoc($paymentResult)) $payments[] = $row;

// Statistics
$statsResult = mysqli_query($conn, "SELECT name, COUNT(*) as count FROM subscriptions WHERE userid='$userid' GROUP BY name");
$labels = $data = [];
while($row = mysqli_fetch_assoc($statsResult)) {
    $labels[] = $row['name'];
    $data[] = $row['count'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Subscription Dashboard</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<link href="index.css" rel="stylesheet">
</head>

<body>

<header>
<h1>My Dashboard</h1>
<nav>
<ul>
<li><a href="subscrip.php"><span class="material-icons">subscriptions</span>Subscriptions</a></li>
<li><a href="#payments"><span class="material-icons">payment</span>Payments</a></li>
<li><a href="#settings"><span class="material-icons">settings</span>Settings</a></li>
<a href="profile.php" class="navbar-profile" style="text-decoration:none; color:white;">
    <img src="https://cdn-icons-png.flaticon.com/128/847/847969.png"<?= urlencode($email) ?> alt="Profile" />
    <span><?= htmlspecialchars($username) ?></span>
</a>

<li><button class="toggle-btn" id="darkModeBtn" onclick="toggleDarkMode()">
<span class="material-icons" id="modeIcon">brightness_6</span>
</button></li>
</ul>
</nav>
</header>

<section class="profile">
<div class="profile-info">
<h2><?= htmlspecialchars($username) ?></h2>
<p>Premium Member • Last login: <?= htmlspecialchars($last_login) ?></p>
<p>Email: <?= htmlspecialchars($email) ?></p>
<p>mobileno: <?= htmlspecialchars($mobileno) ?></p>
</div>
</section>

<section class="notifications">
<div class="note">⚠️ Your payment is due in 3 days <button onclick="this.parentElement.remove()">Dismiss</button></div>
<div class="note">🎉 Special offer: Upgrade to Gold & save 20% <button onclick="this.parentElement.remove()">Dismiss</button></div>
</section>

<div class="search-bar">
<input type="text" id="searchInput" placeholder="Search subscriptions..." onkeyup="filterCards()" />
</div>

<main class="container">

<!-- Active Subscription -->
<section class="card" id="subscription">
<h2><span class="material-icons">subscriptions</span> Active Subscription</h2>
<?php if($activeSubs): ?>
<ul id="activeSubsList">
<?php 
$maxActiveDisplay = 3;
foreach($activeSubs as $index => $s): 
    if($index >= $maxActiveDisplay) break;
?>
<li>
Subscription: <strong><?= htmlspecialchars($s['name']) ?></strong><br>
Amount: <strong>₹<?= htmlspecialchars($s['price']) ?></strong><br>
Renewal Date: <strong><?= date("M d, Y", strtotime($s['renewal_date'])) ?></strong>
</li>
<?php endforeach; ?>
</ul>

<?php if(count($activeSubs) > $maxActiveDisplay): ?>
<button id="viewMoreActiveBtn" style="margin-top:10px;padding:5px 10px;cursor:pointer;border:none;border-radius:5px;background:#509086;;color:#000;font-weight:bold;">
View More
</button>
<script>
let remainingActiveSubs = <?= json_encode(array_slice($activeSubs, $maxActiveDisplay)) ?>;
document.getElementById('viewMoreActiveBtn').addEventListener('click', function() {
    let list = document.getElementById('activeSubsList');
    remainingActiveSubs.forEach(s => {
        let li = document.createElement('li');
        li.innerHTML = `Subscription: <strong>${s.name}</strong><br>
                        Amount: <strong>₹${s.price}</strong><br>
                        Renewal Date: <strong>${new Date(s.renewal_date).toLocaleDateString('en-GB', {
                            day: '2-digit', month: 'short', year: 'numeric'
                        })}</strong>`;
        list.appendChild(li);
    });
    this.style.display = 'none';
});
</script>
<?php endif; ?>
<?php else: ?>
<p>No active subscriptions.</p>
<?php endif; ?>
</section>

<!-- Upcoming Payments -->
<section class="card" id="payments">
<h2><span class="material-icons">payment</span> Upcoming Payments</h2>
<?php if($payments): ?>
<ul id="paymentsList">
<?php 
$maxDisplay = 3;
foreach($payments as $index => $p): 
    if($index >= $maxDisplay) break;
?>
<li>
Subscription: <strong><?= htmlspecialchars($p['name']) ?></strong><br>
Amount: <strong>₹<?= htmlspecialchars($p['price']) ?></strong><br>
Renewal Date: <strong><?= date("M d, Y", strtotime($p['renewal_date'])) ?></strong>
</li>
<?php endforeach; ?>
</ul>

<?php if(count($payments) > $maxDisplay): ?>
<button id="viewMoreBtn" style="margin-top:10px;padding:5px 10px;cursor:pointer;border:none;border-radius:5px;background:#509086;color:#000;font-weight:bold;">
View More
</button>
<script>
let remainingPayments = <?= json_encode(array_slice($payments, $maxDisplay)) ?>;
document.getElementById('viewMoreBtn').addEventListener('click', function() {
    let list = document.getElementById('paymentsList');
    remainingPayments.forEach(p => {
        let li = document.createElement('li');
        li.innerHTML = `Subscription: <strong>${p.name}</strong><br>
                        Amount: <strong>₹${p.price}</strong><br>
                        Renewal Date: <strong>${new Date(p.renewal_date).toLocaleDateString('en-GB', {
                            day: '2-digit', month: 'short', year: 'numeric'
                        })}</strong>`;
        list.appendChild(li);
    });
    this.style.display = 'none';
});
</script>
<?php endif; ?>
<?php else: ?>
<p>No upcoming payments.</p>
<?php endif; ?>
</section>

<!-- Statistics -->
<section class="card" id="stats">
<h2><span class="material-icons">bar_chart</span> Statistics</h2>
<canvas id="myChart"></canvas>
</section>

</main>

<footer class="footer">
  <div class="footer-container" style="max-width:1400px; padding:2rem 3rem;">
    
    <!-- About Section -->
    <div class="footer-section">
      <h3>About My Dashboard</h3>
      <p>Manage your subscriptions, payments, and account settings easily in one place. Stay organized and never miss a renewal date.</p>
      <p>Our goal is to make subscription management simple, transparent, and beautiful.</p>
    </div>

    <!-- Quick Links -->
    <div class="footer-section">
      <h4>Quick Links</h4>
      <ul>
        <li><a href="#subscription">Subscriptions</a></li>
        <li><a href="#payments">Payments</a></li>
        <li><a href="#stats">Statistics</a></li>
        <li><a href="#settings">Settings</a></li>
        <li><a href="profile.php">Profile</a></li>
      </ul>
    </div>

    <!-- Contact Section -->
    <div class="footer-section">
      <h4>Contact Us</h4>
      <p>Email: support@mydashboard.com</p>
      <p>Phone: +91 12345 67890</p>
      <p>Address: 123 Dashboard Street, City, Country</p>
    </div>

    <!-- Newsletter Section -->
    <div class="footer-section">
      <h4>Subscribe to Our Newsletter</h4>
      <p>Get updates, tips, and exclusive offers delivered to your inbox.</p><br>
      <form style="display:flex;gap:0.5rem;">
        <input type="email" placeholder="Your Email" style="padding:.5rem; border-radius:5px; border:none; outline:none; flex:1;">
        <button type="submit" style="padding:.5rem 1rem; border:none; border-radius:5px; background:#0bc5e6ff; color:#000; font-weight:bold; cursor:pointer;">Subscribe</button>
      </form>
    </div>

    <!-- Social Media -->
    <div class="footer-section">
      <h4>Follow Us</h4>
      <div class="social-icons">
        <a href="#"><span class="material-icons">facebook</span></a>
        <a href="#"><span class="material-icons">alternate_email</span></a>
        <a href="#"><span class="material-icons">camera_alt</span></a>
         <a href="#"><span class="material-icons">twitter</span></a>
        <!--<a href="#"><span class="material-icons">linkedin</span></a> -->
      </div>
    </div>

  </div>
  <div class="footer-bottom" style="text-align:center; margin-top:2rem; font-size:.85rem; border-top:1px solid rgba(255,255,255,0.2); padding-top:1rem;">
    &copy; 2025 My Dashboard | All Rights Reserved | Designed with ❤️
  </div>
</footer>


<script>
function toggleDarkMode(){
    document.body.classList.toggle("dark-mode");
    const icon=document.getElementById("modeIcon");
    icon.textContent=document.body.classList.contains("dark-mode")?"wb_sunny":"brightness_3";
}

const ctx = document.getElementById('myChart');
new Chart(ctx, {
    type: 'doughnut',
    data: {
        labels: <?= json_encode($labels) ?>,
        datasets: [{
            data: <?= json_encode($data) ?>,
            backgroundColor: ['#ff6384','#36a2eb','#ffcd56','#ff9f40','#4bc0c0'],
            borderColor: '#fff', // optional: white borders between slices
            borderWidth: 2
        }]
    },
    options: {
        plugins: {
            legend: {
                labels: {
                    color: '#0bc5e6ff', // Legend labels font color
                    font: {
                        size: 14,
                        weight: '600'
                    }
                }
            },
            tooltip: {
                bodyColor: '#000',      // Tooltip text color
                titleColor: 'rgba(185, 30, 120, 1)',     // Tooltip title color
                backgroundColor: 'rgba(255,255,255,0.8)',
                titleFont: {size: 14, weight: 'bold'},
                bodyFont: {size: 13}
            }
        },
        responsive: false,
        maintainAspectRatio: false
    }
});


function filterCards(){
    let input=document.getElementById("searchInput").value.toLowerCase();
    document.querySelectorAll(".card").forEach(card=>{
        card.style.display=card.innerText.toLowerCase().includes(input)?"block":"none";
    });
}
</script>
</body>
</html>
