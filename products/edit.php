<?php
include '../db.php';

$id = $_GET['id'];

// Fetch product details
$query = $conn->prepare("SELECT * FROM products WHERE product_id = ?");
$query->execute([$id]);
$product = $query->fetch(PDO::FETCH_ASSOC);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $product_name = $_POST['product_name'];
    $dosage = $_POST['dosage'];
    $buy_price = $_POST['buy_price'];
    $sell_price = $_POST['sell_price'];
    $stock = $_POST['stock'];
    $barcode = $_POST['barcode'];

    // Update the product
    $update_query = $conn->prepare("
        UPDATE products 
        SET product_name = ?, dosage = ?, buy_price = ?, sell_price = ?, stock = ?, product_barcode = ? 
        WHERE product_id = ?");
    $update_query->execute([$product_name, $dosage, $buy_price, $sell_price, $stock, $barcode, $id]);

    // Redirect to the index page after updating
    header('Location: /final_inv/products/ins.php'); // Adjust path if necessary
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h1>Edit Product</h1>
    <form method="POST">
        <div class="mb-3">
            <label>Product Name</label>
            <input type="text" name="product_name" value="<?php echo htmlspecialchars($product['product_name']); ?>" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Dosage</label>
            <input type="text" name="dosage" value="<?php echo htmlspecialchars($product['dosage']); ?>" class="form-control">
        </div>
        <div class="mb-3">
            <label>Buy Price</label>
            <input type="number" step="0.01" name="buy_price" value="<?php echo htmlspecialchars($product['buy_price']); ?>" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Sell Price</label>
            <input type="number" step="0.01" name="sell_price" value="<?php echo htmlspecialchars($product['sell_price']); ?>" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Stock</label>
            <input type="number" name="stock" value="<?php echo htmlspecialchars($product['stock']); ?>" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Barcode</label>
            <input type="text" name="barcode" value="<?php echo htmlspecialchars($product['product_barcode']); ?>" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Update Product</button>
    </form>
</div>
</body>
</html>
