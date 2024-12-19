<?php
// get_low_stock.php

include '../db.php';

// Fetch low-stock products (stock <= 10)
$low_stock_query = $conn->prepare("SELECT product_name, stock FROM products WHERE stock <= 10");
$low_stock_query->execute();
$low_stock_products = $low_stock_query->fetchAll(PDO::FETCH_ASSOC);

// Store the low-stock products in the session for later use
$_SESSION['low_stock_products'] = $low_stock_products;

?>
