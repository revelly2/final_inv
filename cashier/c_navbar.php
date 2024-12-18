<?php
// Start session only if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include '../db.php'; // Database connection

// Assuming the cashier's name is stored in the session
$cashier_name = $_SESSION['cashier_name'] ?? 'Cashier';

// Get today's date
$date_today = date('Y-m-d');

// Fetch today's sales (total profit)
$query_daily_sales = $conn->prepare("
    SELECT SUM(total_price) AS daily_sales 
    FROM sales 
    WHERE DATE(sale_date) = :date_today
");
$query_daily_sales->execute(['date_today' => $date_today]);
$daily_sales = $query_daily_sales->fetch(PDO::FETCH_ASSOC)['daily_sales'] ?? 0;

// Fetch the number of daily transactions (limit 10 latest)
$query_daily_transactions = $conn->prepare("
    SELECT COUNT(*) AS daily_transactions 
    FROM sales 
    WHERE DATE(sale_date) = :date_today
");
$query_daily_transactions->execute(['date_today' => $date_today]);
$daily_transactions = $query_daily_transactions->fetch(PDO::FETCH_ASSOC)['daily_transactions'] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cashier Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Custom Navbar Styles */
        .custom-navbar {
            background-color: #7400b8;
            transition: background-color 0.3s ease-in-out;
        }

        .custom-navbar .navbar-brand {
            color: #ffffff !important;
            font-weight: bold;
            transition: color 0.3s ease;
        }

        .custom-navbar .nav-link {
            color: #ffffff !important;
            font-size: 1.1rem;
            transition: color 0.3s ease;
        }

        .custom-navbar .nav-link:hover {
            color: #48bfe3 !important;
            text-decoration: underline;
        }

        .custom-navbar .navbar-toggler {
            border: none;
        }

        .custom-navbar .navbar-toggler-icon {
            background-color: #ffffff;
        }

        /* Dropdown Menu Styling */
        .custom-navbar .dropdown-menu {
            background-color: #5e60ce;
            border-radius: 10px;
            animation: fadeIn 0.5s ease-in-out;
        }

        .custom-navbar .dropdown-item {
            color: #ffffff;
            font-size: 1.1rem;
            padding: 15px;
            transition: background-color 0.3s ease, transform 0.3s ease;
        }

        .custom-navbar .dropdown-item:hover {
            background-color: #5390d9;
            transform: scale(1.05);
        }

        /* Divider Styling */
        .custom-navbar .dropdown-divider {
            border-color: #5390d9;
        }

        /* Profile Dropdown Animation */
        @keyframes fadeIn {
            0% {
                opacity: 0;
                transform: translateY(-10px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Responsive Navbar Animations */
        @media (max-width: 991px) {
            .custom-navbar .navbar-collapse {
                animation: slideIn 0.5s ease-out;
            }
        }

        @keyframes slideIn {
            0% {
                opacity: 0;
                transform: translateX(100%);
            }
            100% {
                opacity: 1;
                transform: translateX(0);
            }
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark custom-navbar">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">My Store</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav me-auto">
        <li class="nav-item">
          <a class="nav-link" href="../cashier/cashier_dashboard.php">Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="../cashier/c_sell.php">Sell Product</a>
        </li>
      </ul>

      <!-- Profile Section -->
      <ul class="navbar-nav ms-auto">
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="profileDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            <?php echo htmlspecialchars($cashier_name); ?>
          </a>
          <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
            <li class="dropdown-item">
              <strong>Daily Sales: </strong>₱<?php echo number_format($daily_sales, 2); ?>
            </li>
            <li class="dropdown-item">
              <strong>Daily Transactions: </strong><?php echo $daily_transactions; ?>
            </li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item text-danger" href="../cashier/logout.php">Logout</a></li>
          </ul>
        </li>
      </ul>
    </div>
  </div>
</nav>

<!-- Bootstrap JS and Popper.js -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.min.js"></script>

</body>
</html>
