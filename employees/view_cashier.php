<?php
// Start session
session_start();
include '../db.php'; // Database connection

// Get cashier ID from the query string
$cashier_id = $_GET['id'];

// Fetch cashier information
$query = $conn->prepare("SELECT * FROM users WHERE id = :id");
$query->execute([':id' => $cashier_id]);
$cashier = $query->fetch(PDO::FETCH_ASSOC);

// Fetch sales data
$dailySales = $conn->prepare("
    SELECT SUM(total_price) AS daily_sales 
    FROM sales 
    WHERE cashier_id = :cashier_id AND DATE(sale_date) = CURDATE()
");
$dailySales->execute([':cashier_id' => $cashier_id]);
$daily_sales = $dailySales->fetch(PDO::FETCH_ASSOC)['daily_sales'] ?? 0;

$weeklySales = $conn->prepare("
    SELECT SUM(total_price) AS weekly_sales 
    FROM sales 
    WHERE cashier_id = :cashier_id AND YEARWEEK(sale_date, 1) = YEARWEEK(CURDATE(), 1)
");
$weeklySales->execute([':cashier_id' => $cashier_id]);
$weekly_sales = $weeklySales->fetch(PDO::FETCH_ASSOC)['weekly_sales'] ?? 0;

$monthlySales = $conn->prepare("
    SELECT SUM(total_price) AS monthly_sales 
    FROM sales 
    WHERE cashier_id = :cashier_id AND MONTH(sale_date) = MONTH(CURDATE()) AND YEAR(sale_date) = YEAR(CURDATE())
");
$monthlySales->execute([':cashier_id' => $cashier_id]);
$monthly_sales = $monthlySales->fetch(PDO::FETCH_ASSOC)['monthly_sales'] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cashier Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include $_SERVER['DOCUMENT_ROOT'] . '/final_inv/includes/navbar.php'; ?>
    <div class="container mt-5">
        <h1>Cashier Profile</h1>
        <p><strong>Name:</strong> <?= htmlspecialchars($cashier['name']) ?></p>
        <p><strong>Sex:</strong> <?= htmlspecialchars($cashier['sex']) ?></p>
        <p><strong>Address:</strong> <?= htmlspecialchars($cashier['address']) ?></p>
        <p><strong>Contact:</strong> <?= htmlspecialchars($cashier['contact']) ?></p>
        
        <h3>Sales Data</h3>
        <p><strong>Daily Sales:</strong> ₱<?= number_format($daily_sales, 2) ?></p>
        <p><strong>Weekly Sales:</strong> ₱<?= number_format($weekly_sales, 2) ?></p>
        <p><strong>Monthly Sales:</strong> ₱<?= number_format($monthly_sales, 2) ?></p>
    </div>
</body>

</html>
