<?php


session_start();
include '../db.php';
include '../cashier/c_navbar.php';

// Ensure cashier is logged in
if (!isset($_SESSION['cashier_id'])) {
    die("Error: Cashier not logged in.");
}

$cashier_id = $_SESSION['cashier_id'];

// Initialize the "cart" session
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}
$receipt = null; // Holds receipt data to display after completing the sale

// Add product to the cart when a barcode is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['barcode'])) {
    $barcode = $_POST['barcode'];

    // Fetch product by barcode
    $query = $conn->prepare("SELECT * FROM products WHERE product_barcode = :barcode");
    $query->execute(['barcode' => $barcode]);
    $product = $query->fetch(PDO::FETCH_ASSOC);

    if ($product) {
        $found = false;
        foreach ($_SESSION['cart'] as &$item) {
            if ($item['product_id'] === $product['product_id']) {
                $item['quantity']++;
                $found = true;
                break;
            }
        }

        if (!$found) {
            $_SESSION['cart'][] = [
                'product_id' => $product['product_id'],
                'product_name' => $product['product_name'],
                'sell_price' => $product['sell_price'],
                'quantity' => 1,
            ];
        }
    } else {
        $error = "Product with barcode $barcode not found.";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_product_id'])) {
    $product_id_to_remove = $_POST['remove_product_id'];
    foreach ($_SESSION['cart'] as $key => $item) {
        if ($item['product_id'] == $product_id_to_remove) {
            unset($_SESSION['cart'][$key]);
            break;
        }
    }
    $_SESSION['cart'] = array_values($_SESSION['cart']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_sale'])) {
    if (!empty($_SESSION['cart'])) {
        $total_price = 0;
        $total_profit = 0;
        $receipt_items = [];

        $conn->beginTransaction();

        try {
            foreach ($_SESSION['cart'] as $item) {
                $query = $conn->prepare("SELECT * FROM products WHERE product_id = :product_id");
                $query->execute(['product_id' => $item['product_id']]);
                $product = $query->fetch(PDO::FETCH_ASSOC);

                if ($product && $product['stock'] >= $item['quantity']) {
                    $profit = $item['quantity'] * ($product['sell_price'] - $product['buy_price']);
                    $total_price += $item['quantity'] * $product['sell_price'];
                    $total_profit += $profit;

                    $update_stock = $conn->prepare("UPDATE products SET stock = stock - :quantity WHERE product_id = :product_id");
                    $update_stock->execute([
                        'quantity' => $item['quantity'],
                        'product_id' => $item['product_id']
                    ]);

                    $insert_sale = $conn->prepare("
                        INSERT INTO sales (product_id, quantity, profit, total_price, sale_date) 
                        VALUES (:product_id, :quantity, :profit, :total_price, NOW())
                    ");
                    $insert_sale->execute([
                        'product_id' => $item['product_id'],
                        'quantity' => $item['quantity'],
                        'profit' => $profit,
                        'total_price' => $item['quantity'] * $product['sell_price']
                    ]);

                    $receipt_items[] = [
                        'product_name' => $item['product_name'],
                        'quantity' => $item['quantity'],
                        'price' => $item['sell_price'],
                        'subtotal' => $item['sell_price'] * $item['quantity'],
                    ];
                } else {
                    throw new Exception("Insufficient stock for product: " . $product['product_name']);
                }
            }

            $conn->commit();

            $_SESSION['cart'] = [];

            $receipt = [
                'items' => $receipt_items,
                'total_price' => $total_price,
                'date' => date('Y-m-d H:i:s'),
            ];
        } catch (Exception $e) {
            $conn->rollBack();
            $error = $e->getMessage();
        }
    } else {
        $error = "Cart is empty.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sell Product</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h1>Sell Products</h1>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>

    <form action="sell.php" method="POST" class="mb-3">
        <div class="input-group">
            <input type="text" name="barcode" class="form-control" placeholder="Scan or enter barcode" autofocus required>
            <button type="submit" class="btn btn-primary">Add to Cart</button>
        </div>
    </form>

    <?php if (!empty($_SESSION['cart'])): ?>
        <h2>Cart</h2>
        <table class="table table-bordered">
            <thead>
            <tr>
                <th>Product Name</th>
                <th>Sell Price</th>
                <th>Quantity</th>
                <th>Subtotal</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>
            <?php 
            $grand_total = 0;
            foreach ($_SESSION['cart'] as $item): 
                $subtotal = $item['sell_price'] * $item['quantity'];
                $grand_total += $subtotal;
            ?>
                <tr>
                    <td><?php echo $item['product_name']; ?></td>
                    <td>₱<?php echo number_format($item['sell_price'], 2); ?></td>
                    <td><?php echo $item['quantity']; ?></td>
                    <td>₱<?php echo number_format($subtotal, 2); ?></td>
                    <td>
                        <form action="sell.php" method="POST" style="display:inline;">
                            <input type="hidden" name="remove_product_id" value="<?php echo $item['product_id']; ?>">
                            <button type="submit" class="btn btn-danger btn-sm">Remove</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
            <tfoot>
            <tr>
                <th colspan="3">Grand Total</th>
                <th colspan="2">₱<?php echo number_format($grand_total, 2); ?></th>
            </tr>
            </tfoot>
        </table>
    <?php endif; ?>

    <form action="sell.php" method="POST">
        <button type="submit" name="submit_sale" class="btn btn-success">Complete Sale</button>
    </form>

    <?php if ($receipt): ?>
        <div class="mt-5">
            <h2>Receipt</h2>
            <div class="border p-4 rounded">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4>Store Name</h4>
                        <p>Address: 1234 Main St.<br>Contact: (123) 456-7890</p>
                    </div>
                    <div>
                        <p><strong>Date:</strong> <?php echo $receipt['date']; ?></p>
                        <p><strong>Receipt ID:</strong> <?php echo uniqid(); ?></p>
                    </div>
                </div>
                <hr>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Quantity</th>
                            <th>Price</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($receipt['items'] as $item): ?>
                            <tr>
                                <td><?php echo $item['product_name']; ?></td>
                                <td><?php echo $item['quantity']; ?></td>
                                <td>₱<?php echo number_format($item['price'], 2); ?></td>
                                <td>₱<?php echo number_format($item['subtotal'], 2); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="3" class="text-end">Total</th>
                            <th>₱<?php echo number_format($receipt['total_price'], 2); ?></th>
                        </tr>
                    </tfoot>
                </table>
                <div class="text-center mt-4">
                    <h5 class="text-success">PAID</h5>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.min.js"></script>
</body>
</html>
