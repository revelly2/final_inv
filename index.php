<?php
session_start();
include 'db.php';

// Handle login
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email']; // Email instead of username
    $password = $_POST['password']; // Plain password

    // Check if email and password match in the database
    $query = $conn->prepare("SELECT * FROM users WHERE email = :email AND password = :password");
    $query->execute(['email' => $email, 'password' => $password]);
    $user = $query->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Set session variables
        $_SESSION['user_id'] = $user['id']; 
        $_SESSION['role'] = $user['role'];
        $_SESSION['user_name'] = $user['name']; // Storing user name for further use

        // Redirect based on user role
        if ($user['role'] === 'admin') {
            header('Location: admin_dashboard.php'); // Redirect to admin dashboard
        } elseif ($user['role'] === 'cashier') {
            header('Location: cashier/cashier_dashboard.php'); // Redirect to cashier dashboard
        }
        exit;
    } else {
        $error = "Invalid email or password"; // Error message for incorrect credentials
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #6c5ce7;
            font-family: 'Arial', sans-serif;
            height: 100vh;
        }

        .login-container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100%;
        }

        .login-form {
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }

        .login-form h1 {
            color: #6c5ce7;
            margin-bottom: 20px;
        }

        .btn-primary {
            background-color: #6c5ce7;
            border: none;
        }

        .btn-primary:hover {
            background-color: #4834d4;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <form method="POST" class="login-form">
            <h1>Login</h1>
            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Login</button>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.min.js"></script>
</body>
</html>
