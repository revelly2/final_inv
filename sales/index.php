<?php
include '../db.php';
include $_SERVER['DOCUMENT_ROOT'] . '/final_inv/includes/navbar.php';


// Query the latest 25 sales records
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
<div class="container mt-5">
    <h1>Sales</h1>

    <!-- Button to trigger the modal -->
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#salesModal">
        View Sales (Latest 25)
    </button>

    <!-- Sales Modal -->
    <div class="modal fade" id="salesModal" tabindex="-1" aria-labelledby="salesModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="salesModalLabel">Latest 25 Sales</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <?php if (!empty($sales)): ?>
                        <table class="table table-bordered">
                            <thead>
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
                                        <td><?php echo $sale['product_name']; ?></td>
                                        <td><?php echo $sale['dosage']; ?></td>
                                        <td><?php echo $sale['quantity']; ?></td>
                                        <td>₱<?php echo number_format($sale['total_price'], 2); ?></td>
                                        <td>₱<?php echo number_format($sale['profit'], 2); ?></td>
                                        <td><?php echo $sale['sale_date']; ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p>No sales records found.</p>
                    <?php endif; ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
