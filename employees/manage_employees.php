<?php

include '../db.php'; // Database connection

// Fetch all cashiers (employees) from the `users` table
$query = $conn->prepare("SELECT * FROM users WHERE role = 'cashier'");
$query->execute();
$employees = $query->fetchAll(PDO::FETCH_ASSOC);

// Handle form submission for adding a new cashier
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $sex = $_POST['sex'];
    $address = $_POST['address'];
    $contact = $_POST['contact'];
    $email = $_POST['email'];
    $password = $_POST['password']; // No hashing of the password

    // Handle picture upload
    $profilePictureUrl = null;
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../uploads/'; // Ensure this directory exists
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true); // Create directory if it doesn't exist
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
        // Insert new cashier into `users` table
        $insertQuery = $conn->prepare("
            INSERT INTO users (name, sex, address, contact, email, password, profile_picture, role) 
            VALUES (:name, :sex, :address, :contact, :email, :password, :profile_picture, 'cashier')
        ");
        $insertQuery->execute([
            ':name' => $name,
            ':sex' => $sex,
            ':address' => $address,
            ':contact' => $contact,
            ':email' => $email,
            ':password' => $password, // Store password as plain text
            ':profile_picture' => $profilePictureUrl
        ]);

        // Redirect to refresh the page
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
    <title>Manage Employees</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/final_inv/includes/navbar.php'; ?>
    <div class="container mt-5">
        <h1 class="mb-4">Manage Employees</h1>

        <!-- Form to add a new cashier -->
        <form method="POST" enctype="multipart/form-data" class="mb-4">
            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="mb-3">
                <label for="sex" class="form-label">Sex</label>
                <select class="form-control" id="sex" name="sex" required>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="address" class="form-label">Address</label>
                <input type="text" class="form-control" id="address" name="address" required>
            </div>
            <div class="mb-3">
                <label for="contact" class="form-label">Contact</label>
                <input type="text" class="form-control" id="contact" name="contact" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <div class="mb-3">
                <label for="profile_picture" class="form-label">Profile Picture</label>
                <input type="file" class="form-control" id="profile_picture" name="profile_picture">
            </div>
            <button type="submit" class="btn btn-primary">Add Cashier</button>
        </form>

        <!-- List of existing cashiers -->
        <h2 class="mb-3">Existing Cashiers</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Sex</th>
                    <th>Address</th>
                    <th>Contact</th>
                    <th>Email</th>
                    <th>Profile Picture</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($employees as $employee): ?>
                    <tr>
                        <td><?= htmlspecialchars($employee['name']) ?></td>
                        <td><?= htmlspecialchars($employee['sex']) ?></td>
                        <td><?= htmlspecialchars($employee['address']) ?></td>
                        <td><?= htmlspecialchars($employee['contact']) ?></td>
                        <td><?= htmlspecialchars($employee['email']) ?></td>
                        <td>
                            <?php if (!empty($employee['profile_picture'])): ?>
                                <img src="<?= htmlspecialchars($employee['profile_picture']) ?>" alt="Profile Picture" width="50">
                            <?php else: ?>
                                N/A
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="view_cashier.php?id=<?= $employee['id'] ?>" class="btn btn-info btn-sm">View</a>
                            <a href="delete_cashier.php?id=<?= $employee['id'] ?>" class="btn btn-danger btn-sm">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.min.js"></script>
</body>
</html>
