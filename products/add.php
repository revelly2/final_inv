<?php
include '../db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $product_name = $_POST['product_name'];
    $dosage = $_POST['dosage'];
    $buy_price = $_POST['buy_price'];
    $sell_price = $_POST['sell_price'];
    $stock = $_POST['stock'];
    $barcode = $_POST['barcode'];

    $query = $conn->prepare("INSERT INTO products (product_name, dosage, buy_price, sell_price, stock, product_barcode) 
                             VALUES (?, ?, ?, ?, ?, ?)");
    $query->execute([$product_name, $dosage, $buy_price, $sell_price, $stock, $barcode]);

    header('Location: index.php');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h1>Add Product</h1>
    <form method="POST">
        <div class="mb-3">
            <label>Product Name</label>
            <input type="text" name="product_name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Dosage</label>
            <input type="text" name="dosage" class="form-control">
        </div>
        <div class="mb-3">
            <label>Buy Price</label>
            <input type="number" step="0.01" name="buy_price" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Sell Price</label>
            <input type="number" step="0.01" name="sell_price" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Stock</label>
            <input type="number" name="stock" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Barcode</label>
            <input type="text" name="barcode" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Add Product</button>
    </form>
</div>
</body>
</html>
