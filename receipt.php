<?php
include '../db.php';

// Get the sale ID from the URL
$sale_id = isset($_GET['sale_id']) ? $_GET['sale_id'] : null;

// Fetch sale and product details from the database
$sale_query = $conn->prepare("
    SELECT 
        s.sale_id,
        p.product_name,
        p.sell_price,
        s.quantity,
        s.total_price,
        s.sale_date
    FROM sales s
    JOIN products p ON s.product_id = p.product_id
    WHERE s.sale_id = :sale_id
");
$sale_query->execute(['sale_id' => $sale_id]);
$sale = $sale_query->fetch(PDO::FETCH_ASSOC);

if (!$sale) {
    die("Sale not found.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <div class="card">
        <div class="card-header text-center">
            <h1>Receipt</h1>
        </div>
        <div class="card-body">
            <p><strong>Sale ID:</strong> <?php echo $sale['sale_id']; ?></p>
            <p><strong>Date:</strong> <?php echo $sale['sale_date']; ?></p>
            <table class="table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><?php echo $sale['product_name']; ?></td>
                        <td>₱<?php echo number_format($sale['sell_price'], 2); ?></td>
                        <td><?php echo $sale['quantity']; ?></td>
                        <td>₱<?php echo number_format($sale['total_price'], 2); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="card-footer text-center">
            <h3>Thank you for your purchase!</h3>
        </div>
    </div>
</div>
</body>
</html>
