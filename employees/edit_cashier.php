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

    // Fetch cashier details
    $query = $conn->prepare("SELECT * FROM users WHERE id = :id AND role = 'cashier'");
    $query->execute([':id' => $id]);
    $employee = $query->fetch(PDO::FETCH_ASSOC);

    if (!$employee) {
        echo "<div class='alert alert-danger'>Cashier not found.</div>";
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $sex = $_POST['sex'];
    $address = $_POST['address'];
    $contact = $_POST['contact'];
    $email = $_POST['email'];
    $password = $_POST['password']; // No hashing of the password (for your current requirement)

    // Handle picture upload (optional update)
    $profilePictureUrl = $employee['profile_picture']; // Keep existing picture if not updated
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $pictureName = basename($_FILES['profile_picture']['name']);
        $picturePath = $uploadDir . $pictureName;

        if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $picturePath)) {
            $profilePictureUrl = '/final_inv/uploads/' . $pictureName; // Public path to the picture
        } else {
            echo "<div class='alert alert-danger'>Failed to upload the profile picture.</div>";
        }
    }

    try {
        // Update the cashier details
        $updateQuery = $conn->prepare("
            UPDATE users SET name = :name, sex = :sex, address = :address, contact = :contact, 
            email = :email, password = :password, profile_picture = :profile_picture
            WHERE id = :id AND role = 'cashier'
        ");
        $updateQuery->execute([
            ':id' => $id,
            ':name' => $name,
            ':sex' => $sex,
            ':address' => $address,
            ':contact' => $contact,
            ':email' => $email,
            ':password' => $password, // Store password as plain text
            ':profile_picture' => $profilePictureUrl
        ]);

        // Redirect to the manage employees page
        header("Location: manage_employees.php");
        exit;
    } catch (PDOException $e) {
        echo "<div class='alert alert-danger'>Error: " . $e->getMessage() . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Cashier</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/final_inv/includes/navbar.php'; ?>
    <div class="container mt-5">
        <h1>Edit Cashier</h1>

        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($employee['name']) ?>" required>
            </div>
            <div class="mb-3">
                <label for="sex" class="form-label">Sex</label>
                <select class="form-control" id="sex" name="sex" required>
                    <option value="Male" <?= $employee['sex'] == 'Male' ? 'selected' : '' ?>>Male</option>
                    <option value="Female" <?= $employee['sex'] == 'Female' ? 'selected' : '' ?>>Female</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="address" class="form-label">Address</label>
                <input type="text" class="form-control" id="address" name="address" value="<?= htmlspecialchars($employee['address']) ?>" required>
            </div>
            <div class="mb-3">
                <label for="contact" class="form-label">Contact</label>
                <input type="text" class="form-control" id="contact" name="contact" value="<?= htmlspecialchars($employee['contact']) ?>" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($employee['email']) ?>" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" value="<?= htmlspecialchars($employee['password']) ?>" required>
            </div>
            <div class="mb-3">
                <label for="profile_picture" class="form-label">Profile Picture</label>
                <input type="file" class="form-control" id="profile_picture" name="profile_picture">
                <?php if ($employee['profile_picture']): ?>
                    <img src="<?= htmlspecialchars($employee['profile_picture']) ?>" alt="Profile Picture" width="100" class="mt-2">
                <?php endif; ?>
            </div>
            <button type="submit" class="btn btn-primary">Update Cashier</button>
        </form>
    </div>
</body>
</html>
