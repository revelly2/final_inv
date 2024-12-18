<?php
include '../db.php'; // Ensure correct path
include '../cashier/c_navbar.php';

// Fetch the latest 25 sales records
$query_sales = $conn->query("
    SELECT 
        s.sale_id,
        p.product_name,
        p.dosage,
        s.quantity,
        s.total_price,
        s.profit,
        s.sale_date
    FROM sales s
    JOIN products p ON s.product_id = p.product_id
    ORDER BY s.sale_date DESC
    LIMIT 25
");
$sales = $query_sales->fetchAll(PDO::FETCH_ASSOC);

// Fetch the total daily sales and profit
$query_totals = $conn->query("
    SELECT 
        SUM(total_price) AS daily_total_sales,
        SUM(profit) AS daily_total_profit
    FROM sales
    WHERE DATE(sale_date) = CURDATE()
");
$totals = $query_totals->fetch(PDO::FETCH_ASSOC);

// Set totals to 0 if no records exist
$daily_total_sales = $totals['daily_total_sales'] ?? 0;
$daily_total_profit = $totals['daily_total_profit'] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction List</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
<div class="container mt-5">
    <h1 class="mb-4">Transaction List</h1>

    <!-- Display Daily Totals -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="p-3 bg-success text-white rounded">
                <h4>Daily Total Sales</h4>
                <h2>₱<?php echo number_format($daily_total_sales, 2); ?></h2>
            </div>
        </div>
        <div class="col-md-6">
            <div class="p-3 bg-info text-white rounded">
                <h4>Daily Total Profit</h4>
                <h2>₱<?php echo number_format($daily_total_profit, 2); ?></h2>
            </div>
        </div>
    </div>

    <!-- Display Transactions -->
    <?php if (!empty($sales)): ?>
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Product Name</th>
                        <th>Dosage</th>
                        <th>Quantity</th>
                        <th>Total Price</th>
                        <th>Profit</th>
                        <th>Sale Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($sales as $sale): ?>
                        <tr>
                            <td><?php echo $sale['sale_id']; ?></td>
                            <td><?php echo htmlspecialchars($sale['product_name']); ?></td>
                            <td><?php echo htmlspecialchars($sale['dosage']); ?></td>
                            <td><?php echo $sale['quantity']; ?></td>
                            <td>₱<?php echo number_format($sale['total_price'], 2); ?></td>
                            <td>₱<?php echo number_format($sale['profit'], 2); ?></td>
                            <td><?php echo date('Y-m-d H:i', strtotime($sale['sale_date'])); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p class="text-danger">No transaction records found.</p>
    <?php endif; ?>
</div>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.min.js"></script>

</body>
</html>
