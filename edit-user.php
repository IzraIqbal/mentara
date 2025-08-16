<?php
require 'db.php';
session_start();

if (!isset($_GET['id']) || !isset($_GET['admin'])) {
    die("Access denied.");
}

$user_id = $_GET['id'];
$admin_id = $_GET['admin'];

// Fetch user data
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!$user) {
    die("User not found.");
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $role = $_POST['role'];

    $update = $conn->prepare("UPDATE users SET name = ?, email = ?, user_type = ? WHERE id = ?");
    $update->execute([$name, $email, $role, $user_id]);

    header("Location: manage-users.php?id=$admin_id");
    exit;
}

// Handle delete
if (isset($_POST['delete'])) {
    $delete = $conn->prepare("DELETE FROM users WHERE id = ?");
    $delete->execute([$user_id]);

    header("Location: manage-users.php?id=$admin_id");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit User | Admin Panel</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&family=Winky+Sans&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        body {
            background-color: #f5f5dc;
            font-family: 'Inter', 'Winky Sans', sans-serif;
            color: #486569;
            padding: 40px 20px;
        }

        .container {
            max-width: 600px;
            margin: auto;
            background: #ffffff;
            padding: 30px 40px;
            border-radius: 16px;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            color: #405e62;
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-top: 15px;
            font-weight: bold;
        }

        input, select {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border-radius: 8px;
            border: 1px solid #ccc;
        }

        .btn-group {
            display: flex;
            justify-content: center;
            margin-top: 30px;
        }

        button, .back-link {
            padding: 10px 18px;
            font-size: 16px;
            border-radius: 8px;
            border: none;
            cursor: pointer;
        }

        .save-btn {
            background-color: #8ab2c0;
            color: white;
        }

        .save-btn:hover {
            background-color: #384549ff;
        }

        .delete-btn {
            background-color: #e53e3e;
            color: white;
        }

        .delete-btn:hover {
            background-color: #c53030;
        }

        .back-link {
            background-color: #8ab2c0;
            color: #2d3748;
            text-decoration: none;
            text-align: center;
            display: inline-block;
            margin-top: 20px;
        }

        .back-link:hover {
            background-color: #cbd5e0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Edit User Details</h2>

        <form method="POST">
            <label for="name">Name:</label>
            <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" required>

            <label for="email">Email:</label>
            <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>

            <label for="role">User Role:</label>
            <select name="role" required>
                <option value="Client" <?= $user['user_type'] === 'Client' ? 'selected' : '' ?>>Client</option>
                <option value="Therapist" <?= $user['user_type'] === 'Therapist' ? 'selected' : '' ?>>Therapist</option>
                <option value="Admin" <?= $user['user_type'] === 'Admin' ? 'selected' : '' ?>>Admin</option>
            </select>

            <div class="btn-group">
                <button type="submit" class="save-btn"><i class="fas fa-save"></i> Save Changes</button>
               
            </div>
        </form>

        <a href="manage-users.php?id=<?= $admin_id ?>" class="back-link"><i class="fas fa-arrow-left"></i> Back to User List</a>
    </div>
</body>
</html>
