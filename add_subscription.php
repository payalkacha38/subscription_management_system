<?php
session_start();
include 'conn.php'; // Database connection

// Check if user is logged in
if (!isset($_SESSION['userid'])) {
    header("Location: login.php");
    exit();
}

$userid = $_SESSION['userid'];
$success = '';
$error = '';
$name = $price = $renewal_date = $cycle = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $price = trim($_POST['price']);
    $renewal_date = $_POST['renewal_date'];
    $cycle = $_POST['cycle'];

    if ($name != '' && $price != '' && $renewal_date != '' && $cycle != '') {
        $sql = "INSERT INTO subscriptions (userid, name, price, renewal_date, cycle) 
                VALUES ('$userid', '$name', '$price', '$renewal_date', '$cycle')";
        if (mysqli_query($conn, $sql)) {
            $success = "Subscription added successfully!";
            header('subscrip.php');
            // Clear form values
            $name = $price = $renewal_date = $cycle = '';
        } else {
            $error = "Failed to add subscription. Please try again.";
        }
    } else {
        $error = "Please fill in all fields.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Subscription</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">

    <style>
        body {
            background: url('img9.jpg') no-repeat center center/cover;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Poppins', sans-serif;
        }
        body::before {
            content: "";
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: -1;
        }
        .form-container {
            background: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(15px);
            border-radius: 20px;
            padding: 3rem;
            width: 100%;
            max-width: 600px;
            box-shadow: 0px 10px 25px rgba(0,0,0,0.5);
            color: #fff;
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .form-container:hover {
            transform: translateY(-5px);
            box-shadow: 0px 12px 30px rgba(0,0,0,0.6);
        }
        .form-container h2 {
            text-align: center;
            margin-bottom: 2rem;
            font-weight: bold;
            color: #ffcc70;
        }
        .form-control, .form-select {
            background: rgba(255, 255, 255, 0.9);
            color: #000;
            border: 1px solid #ccc;
            border-radius: 10px;
            padding: 0.75rem 1rem;
            width: 100%;
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
            background: linear-gradient(45deg, #8aa3f6ff, #03906fff);
            color: white;
            font-weight: bold;
            border: none;
            transition: all 0.3s;
            padding: 0.75rem 1rem;
            border-radius: 10px;
        }
        .btn-custom:hover {
            background: linear-gradient(45deg, #6a2005ff, #460266ff);
            transform: translateY(-3px) scale(1.02);
            box-shadow: 0 6px 15px rgba(255,117,140,0.5);
        }
    </style>
</head>
<body>

<div class="form-container">
    <h2><i class="bi bi-plus-circle"></i> Add Subscription</h2>

    <?php if ($success != '') { echo '<div class="alert alert-success">'.$success.'</div>'; } ?>
    <?php if ($error != '') { echo '<div class="alert alert-danger">'.$error.'</div>'; } ?>

    <form method="POST" action="">
        <div class="mb-3">
            <input type="text" name="name" class="form-control" placeholder="Subscription Name" value="<?= $name ?>" required>
        </div>
        <div class="mb-3">
            <input type="number" name="price" class="form-control" placeholder="Price" value="<?= $price ?>" required>
        </div>
        <div class="mb-3">
            <input type="date" name="renewal_date" class="form-control" id="renewalDate" value="<?= $renewal_date ?>" required>
        </div>
        <div class="mb-3">
            <select name="cycle" class="form-select" id="cycleSelect" required>
                <option value="">Select Cycle</option>
                <option value="Monthly" <?= ($cycle=='Monthly') ? 'selected' : '' ?>>Monthly</option>
                <option value="Yearly" <?= ($cycle=='Yearly') ? 'selected' : '' ?>>Yearly</option>
            </select>
        </div>
        <button type="submit" class="btn btn-custom w-100">Add Subscription</button>
    </form>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Auto-set renewal date based on cycle -->
<script>
document.getElementById('cycleSelect').addEventListener('change', function() {
    let cycle = this.value;
    let renewalInput = document.getElementById('renewalDate');
    let today = new Date();

    if (cycle === 'Monthly') {
        today.setMonth(today.getMonth() + 1);
    } else if (cycle === 'Yearly') {
        today.setFullYear(today.getFullYear() + 1);
    } else {
        renewalInput.value = '';
        return;
    }

    let month = (today.getMonth() + 1).toString().padStart(2, '0');
    let day = today.getDate().toString().padStart(2, '0');
    let year = today.getFullYear();

    renewalInput.value = `${year}-${month}-${day}`;
});
</script>

</body>
</html>
