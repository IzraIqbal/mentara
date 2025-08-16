<?php
require 'db.php';
session_start();

if (!isset($_GET['id'])) {
    die("Access denied.");
}

$id = $_GET['id'];

// Fetch user data
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ? AND user_type = 'Client'");
$stmt->execute([$id]);
$user = $stmt->fetch();

if (!$user) {
    die("Client not found.");
}

// Handle form submission
$success = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        // Update password only if provided
        if (!empty($password)) {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET name = ?, email = ?, password = ? WHERE id = ?");
            $updated = $stmt->execute([$name, $email, $hashedPassword, $id]);
        } else {
            $stmt = $conn->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
            $updated = $stmt->execute([$name, $email, $id]);
        }

        if ($updated) {
            $success = "Account updated successfully.";
            // Refresh user data
            $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
            $stmt->execute([$id]);
            $user = $stmt->fetch();
        } else {
            $error = "Something went wrong. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Account | Mentara</title>
    <link href="https://fonts.googleapis.com/css2?family=Winky+Sans&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Winky Sans', sans-serif;
            background-color: #f5f5dc;
            padding: 40px;
            color: #2c3e50;
        }

        .container {
            max-width: 500px;
            margin: auto;
            background: #ffffff;
            padding: 30px;
            border-radius: 16px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        h2 {
            text-align: center;
            color: #486569;
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-top: 15px;
            font-weight: 600;
            color: #486569;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border-radius: 8px;
            border: 1px solid #ccc;
            font-size: 16px;
        }

        .btn {
            margin-top: 20px;
            background-color: #486569;
            color: white;
            padding: 12px 20px;
            width: 100%;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            cursor: pointer;
        }

        .btn:hover {
            background-color: #355357;
        }

        .msg {
            margin-top: 15px;
            text-align: center;
            font-weight: bold;
        }

        .success {
            color: green;
        }

        .error {
            color: crimson;
        }

        a.back-link {
            display: inline-block;
            margin-top: 20px;
            text-align: center;
            color: #486569;
            text-decoration: none;
            font-weight: bold;
        }

        a.back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Edit My Account</h2>

    <?php if ($success): ?>
        <p class="msg success"><?= $success ?></p>
    <?php elseif ($error): ?>
        <p class="msg error"><?= $error ?></p>
    <?php endif; ?>

    <form method="post">
        <label for="name">Full Name:</label>
        <input type="text" name="name" id="name" value="<?= htmlspecialchars($user['name']) ?>" required>

        <label for="email">Email Address:</label>
        <input type="email" name="email" id="email" value="<?= htmlspecialchars($user['email']) ?>" required>

        <label for="password">New Password:</label>
        <input type="password" name="password" id="password" placeholder="Leave blank to keep current">

        <label for="confirm_password">Confirm New Password:</label>
        <input type="password" name="confirm_password" id="confirm_password" placeholder="Leave blank to keep current">

        <button type="submit" class="btn">Update Account</button>
    </form>

    <a class="back-link" href="client-dashboard.php?id=<?= $user['id'] ?>">← Back to Dashboard</a>
</div>

</body>
</html>
