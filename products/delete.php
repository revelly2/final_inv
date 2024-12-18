<?php
include '../db.php';

$id = $_GET['id'];

$query = $conn->prepare("DELETE FROM products WHERE product_id = ?");
$query->execute([$id]);

header('Location: index.php');
?>
