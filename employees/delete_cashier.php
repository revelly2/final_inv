<?php
session_start();
include '../db.php'; // Database connection

// Ensure the user is an admin before proceeding
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    try {
        // Prepare the delete query
        $deleteQuery = $conn->prepare("DELETE FROM users WHERE id = :id AND role = 'cashier'");
        $deleteQuery->execute([':id' => $id]);

        // Redirect back to manage employees page after deletion
        header("Location: manage_employees.php");
        exit;
    } catch (PDOException $e) {
        echo "<div class='alert alert-danger'>Error: " . $e->getMessage() . "</div>";
    }
} else {
    echo "<div class='alert alert-danger'>No ID specified for deletion.</div>";
}
?>

