<?php
include 'conn.php';

$id = $_GET['id'] ?? null;

if (!$id) {
    die("Invalid subscription ID");
}

$result = mysqli_query($conn, "SELECT * FROM subscriptions WHERE id=$id");
$row = mysqli_fetch_assoc($result);

if (!$row) {
    die("Subscription not found");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $renewal_date = $_POST['renewal_date'];
    $cycle = $_POST['cycle'];

    mysqli_query($conn, "UPDATE subscriptions SET 
        name='$name', price=$price, renewal_date='$renewal_date', cycle='$cycle' 
        WHERE id=$id");

    header("Location: subscrip.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit Subscription</title>

<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body {
    background: url('img6.jpg') no-repeat center center/cover;
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    font-family: 'Poppins', sans-serif;
}
.form-container {
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(15px);
    border-radius: 20px;
    padding: 2.5rem;
    width: 100%;
    max-width: 600px; /* Increased width */
    box-shadow: 0px 10px 25px rgba(0,0,0,0.4);
    color: #000;
    transition: transform 0.3s, box-shadow 0.3s;
}
.form-container:hover {
    transform: translateY(-5px);
    box-shadow: 0px 12px 30px rgba(0,0,0,0.5);
}
.form-container h2 {
    text-align: center;
    margin-bottom: 2rem;
    font-weight: bold;
    color: #ffcc70;
}
.form-control, .form-select {
    background: rgba(244, 235, 243, 0.9);
    color: black;
    border: 1px solid #ccc;
    border-radius: 10px;
    padding: 0.75rem 1rem;
    width: 100%; /* full width */
    transition: all 0.3s;
}
.form-control::placeholder {
    color: #555;
}
.form-control:hover, .form-select:hover,
.form-control:focus, .form-select:focus {
    border-color: #ff758c;
    box-shadow: 0 0 8px rgba(255,117,140,0.5);
    outline: none;
}
.btn-custom {
    background: linear-gradient(45deg, #6a2f48ff, #540513ff);
    color: white;
    font-weight: bold;
    border: none;
    transition: all 0.3s;
    padding: 0.75rem 1rem;
    border-radius: 10px;
}
.btn-custom:hover {
    background: linear-gradient(45deg, #ff758c, #ff7eb3);
    transform: translateY(-3px) scale(1.02);
    box-shadow: 0 6px 15px rgba(255,117,140,0.5);
}
</style>

</head>
<body>

<div class="form-container">
    <h2><i class="bi bi-pencil-square"></i> Edit Subscription</h2>
    <form method="POST">
        <div class="mb-3">
            <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($row['name']) ?>" required>
        </div>
        <div class="mb-3">
            <input type="number" name="price" class="form-control" value="<?= htmlspecialchars($row['price']) ?>" required>
        </div>
        <div class="mb-3">
            <input type="date" name="renewal_date" class="form-control" value="<?= htmlspecialchars($row['renewal_date']) ?>" required>
        </div>
        <div class="mb-3">
            <select name="cycle" class="form-select" required>
                <option value="">Select Cycle</option>
                <option value="Monthly" <?= ($row['cycle'] == 'Monthly') ? 'selected' : '' ?>>Monthly</option>
                <option value="Yearly" <?= ($row['cycle'] == 'Yearly') ? 'selected' : '' ?>>Yearly</option>
            </select>
        </div>
        <button type="submit" class="btn btn-custom w-100">Update Subscription</button>
    </form>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">

</body>
</html>
