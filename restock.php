<?php
include '../db.php';
include '../navbar.php';

// Fetch products
$products_query = $conn->query("SELECT * FROM products");
$products = $products_query->fetchAll(PDO::FETCH_ASSOC);

// Check if restock form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['restock_quantity'])) {
    $product_id = $_POST['product_id'];
    $restock_quantity = $_POST['restock_quantity'];

    // Update the product's stock
    $update_query = $conn->prepare("UPDATE products SET stock = stock + :quantity WHERE product_id = :id");
    $update_query->bindValue(':quantity', $restock_quantity);
    $update_query->bindValue(':id', $product_id);
    $update_query->execute();

    // Reload the page to reflect the changes
    header('Location: ' . $_SERVER['REQUEST_URI']);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h1>Products</h1>
    <a href="add.php" class="btn btn-primary mb-3">Add New Product</a>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Dosage</th>
                <th>Buy Price</th>
                <th>Sell Price</th>
                <th>Stock</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($products as $product): ?>
                <tr>
                    <td><?php echo $product['product_id']; ?></td>
                    <td><?php echo $product['product_name']; ?></td>
                    <td><?php echo $product['dosage']; ?></td>
                    <td>₱<?php echo number_format($product['buy_price'], 2); ?></td>
                    <td>₱<?php echo number_format($product['sell_price'], 2); ?></td>
                    <td><?php echo $product['stock']; ?></td>
                    <td>
                        <!-- Edit Button -->
                        <a href="edit.php?id=<?php echo $product['product_id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                        <!-- Delete Button -->
                        <a href="delete.php?id=<?php echo $product['product_id']; ?>" class="btn btn-danger btn-sm">Delete</a>
                        <!-- Restock Button (Triggers Modal) -->
                        <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#restockModal" data-product-id="<?php echo $product['product_id']; ?>" data-product-name="<?php echo $product['product_name']; ?>">Restock</button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Restock Modal -->
<div class="modal fade" id="restockModal" tabindex="-1" aria-labelledby="restockModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="restockModalLabel">Restock Product</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form action="" method="POST">
          <input type="hidden" id="product_id" name="product_id">
          <div class="mb-3">
            <label for="restock_quantity" class="form-label">Quantity to Restock</label>
            <input type="number" class="form-control" id="restock_quantity" name="restock_quantity" min="1" required>
          </div>
          <button type="submit" class="btn btn-success">Restock</button>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Bootstrap JS and Popper.js -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.min.js"></script>

<!-- JavaScript to dynamically populate the modal with the correct product ID -->
<script>
    var restockModal = document.getElementById('restockModal')
    restockModal.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget; // Button that triggered the modal
        var productId = button.getAttribute('data-product-id'); // Extract info from data-* attributes
        var productName = button.getAttribute('data-product-name');
        
        // Update the modal's content
        var modalTitle = restockModal.querySelector('.modal-title');
        var productInput = restockModal.querySelector('#product_id');
        modalTitle.textContent = 'Restock ' + productName;
        productInput.value = productId;
    });
</script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.min.js"></script>

<!-- JavaScript to dynamically populate the modal with the correct product ID -->
</body>
</html>
