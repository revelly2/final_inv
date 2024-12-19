<?php
include 'db.php'; // Ensure this path is correct
include $_SERVER['DOCUMENT_ROOT'] . '/final_inv/includes/navbar.php';

// Check if $conn is initialized
if (!isset($conn)) {
    die("Database connection not established. Check your db.php file.");
}

// Fetch low-stock products (e.g., stock ≤ 10)
$low_stock_query = $conn->query("SELECT product_name, stock FROM products WHERE stock <= 10");
$low_stock_products = $low_stock_query->fetchAll(PDO::FETCH_ASSOC);

// Fetch other necessary data for the dashboard
$total_sales_query = $conn->query("SELECT SUM(total_price) AS total_sales FROM sales");
$total_sales = $total_sales_query->fetch(PDO::FETCH_ASSOC)['total_sales'] ?? 0;

$total_profit_query = $conn->query("SELECT SUM(profit) AS total_profit FROM sales");
$total_profit = $total_profit_query->fetch(PDO::FETCH_ASSOC)['total_profit'] ?? 0;

$total_products_query = $conn->query("SELECT SUM(stock) AS total_products FROM products");
$total_products = $total_products_query->fetch(PDO::FETCH_ASSOC)['total_products'] ?? 0;

// Fetch sales data for the line chart (last 7 days)
$query_sales_data = $conn->query("SELECT DATE(sale_date) AS sale_date, SUM(total_price) AS daily_sales, SUM(profit) AS daily_profit, SUM(quantity) AS items_sold FROM sales GROUP BY DATE(sale_date) ORDER BY DATE(sale_date) DESC LIMIT 7");
$sales_data = $query_sales_data->fetchAll(PDO::FETCH_ASSOC);

// Prepare data for the line chart
$dates = [];
$sales = [];
$profits = [];
$items_sold = [];
foreach ($sales_data as $data) {
    $dates[] = $data['sale_date'];
    $sales[] = $data['daily_sales'];
    $profits[] = $data['daily_profit'];
    $items_sold[] = $data['items_sold'];
}
$dates = array_reverse($dates);
$sales = array_reverse($sales);
$profits = array_reverse($profits);
$items_sold = array_reverse($items_sold);

// Handle the date range filter for the doughnut chart
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-d', strtotime('-7 days'));
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');

// Fetch data for the doughnut chart (most sold products)
$query_product_sales = $conn->prepare("SELECT p.product_name, SUM(s.quantity) AS total_quantity_sold FROM sales s JOIN products p ON s.product_id = p.product_id WHERE DATE(s.sale_date) BETWEEN :start_date AND :end_date GROUP BY p.product_name ORDER BY total_quantity_sold DESC LIMIT 5");
$query_product_sales->execute(['start_date' => $start_date, 'end_date' => $end_date]);
$product_sales = $query_product_sales->fetchAll(PDO::FETCH_ASSOC);

// Prepare data for the doughnut chart
$product_names = [];
$product_quantities = [];
foreach ($product_sales as $product) {
    $product_names[] = $product['product_name'];
    $product_quantities[] = $product['total_quantity_sold'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        body {
            background-color: #f5f5f5;
            font-family: 'Arial', sans-serif;
        }

        .chart-container {
            position: relative;
            margin: auto;
            height: 400px;
            width: 100%;
        }

        .card {
            border: none;
            background: #fff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }

        .card-title {
            color: #6c5ce7;
        }

        .footer {
            background-color: #6c5ce7;
            color: #fff;
            text-align: center;
            padding: 10px 0;
        }
    </style>
</head>
<body>

<div class="container mt-5">
    <h1>Dashboard</h1>

    <!-- Total Sales, Profit, and Products in Stock -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card p-3">
                <h5 class="card-title">Total Sales</h5>
                <p class="card-text">₱<?php echo number_format($total_sales, 2); ?></p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card p-3">
                <h5 class="card-title">Total Profit</h5>
                <p class="card-text">₱<?php echo number_format($total_profit, 2); ?></p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card p-3">
                <h5 class="card-title">Products in Stock</h5>
                <p class="card-text"><?php echo $total_products; ?> Items</p>
            </div>
        </div>
    </div>

    <!-- Check if there are low-stock products -->
    <?php if (count($low_stock_products) > 0): ?>
        <script>
            window.onload = function() {
                // Show the low-stock modal
                var myModal = new bootstrap.Modal(document.getElementById('lowStockModal'), {
                    keyboard: false
                });
                myModal.show();
            };
        </script>
    <?php endif; ?>

    <!-- Low Stock Modal -->
    <div class="modal fade" id="lowStockModal" tabindex="-1" aria-labelledby="lowStockModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="lowStockModalLabel">Low Stock Alert</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <h5>Attention! The following products are low on stock:</h5>
                    <ul>
                        <?php foreach ($low_stock_products as $product): ?>
                            <li><?php echo htmlspecialchars($product['product_name']); ?> (Stock: <?php echo $product['stock']; ?>)</li>
                        <?php endforeach; ?>
                    </ul>
                    <p>Please restock these products to avoid stockouts.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Line Chart and Doughnut Chart -->
    <div class="row">
        <div class="col-md-6">
            <div class="chart-container">
                <canvas id="salesChart"></canvas>
            </div>
        </div>
        <div class="col-md-6">
            <div class="chart-container">
                <canvas id="doughnutChart"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="footer mt-5">
    <p>© 2024 My Store Dashboard. All Rights Reserved.</p>
</div>

<!-- Bootstrap JS and Popper.js -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.min.js"></script>

<!-- Chart.js Scripts -->
<script>
    // Line Chart
    const lineCtx = document.getElementById('salesChart').getContext('2d');
    const salesChart = new Chart(lineCtx, {
        type: 'line',
        data: {
            labels: <?php echo json_encode($dates); ?>,
            datasets: [
                {
                    label: 'Total Sales',
                    data: <?php echo json_encode($sales); ?>,
                    borderColor: '#6c5ce7',
                    backgroundColor: 'rgba(108, 92, 231, 0.2)',
                    borderWidth: 2,
                    tension: 0.4,
                },
                {
                    label: 'Total Profit',
                    data: <?php echo json_encode($profits); ?>,
                    borderColor: '#00cec9',
                    backgroundColor: 'rgba(0, 206, 201, 0.2)',
                    borderWidth: 2,
                    tension: 0.4,
                },
                {
                    label: 'Items Sold',
                    data: <?php echo json_encode($items_sold); ?>,
                    borderColor: '#fd79a8',
                    backgroundColor: 'rgba(253, 121, 168, 0.2)',
                    borderWidth: 2,
                    tension: 0.4,
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                },
            },
            scales: {
                y: {
                    beginAtZero: true,
                }
            }
        }
    });

    // Doughnut Chart
    const doughnutCtx = document.getElementById('doughnutChart').getContext('2d');
    const doughnutChart = new Chart(doughnutCtx, {
        type: 'doughnut',
        data: {
            labels: <?php echo json_encode($product_names); ?>,
            datasets: [
                {
                    label: 'Most Sold Products',
                    data: <?php echo json_encode($product_quantities); ?>,
                    backgroundColor: ['#6c5ce7', '#00cec9', '#fd79a8', '#fab1a0', '#ffeaa7'],
                    borderWidth: 1
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom',
                }
            }
        }
    });
</script>
</body>
</html>
