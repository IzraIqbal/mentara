<?php
session_start();
require 'db.php';

// Only allow Admins
if (!isset($_GET['id'])) {
    die("Access denied.");
}

$id = $_GET['id'];
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ? AND user_type = 'Admin'");
$stmt->execute([$id]);
$admin = $stmt->fetch();

if (!$admin) {
    die("Admin not found.");
}

// Handle form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $message = trim($_POST['message']);
    $date = date("Y-m-d H:i:s");

    if ($title && $message) {
        $announcement = [
            'title' => $title,
            'message' => $message,
            'author' => $admin['name'],
            'date' => $date
        ];

        $file = 'announcements.json';
        $existing = file_exists($file) ? json_decode(file_get_contents($file), true) : [];
        array_unshift($existing, $announcement); // Add new announcement at top
        file_put_contents($file, json_encode($existing, JSON_PRETTY_PRINT));
        $success = "Announcement posted successfully!";
    } else {
        $error = "Please fill in both fields.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Post Announcement</title>
      <link
      href="https://fonts.googleapis.com/css2?family=Winky+Sans:ital,wght@0,300..900;1,300..900&display=swap"
      rel="stylesheet"
    />
    <style>
        body { font-family: "Winky Sans", sans-serif;; padding: 40px; background: #f9f9f9; }
        .container { max-width: 600px; margin: auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        h2 { color: #2c3e50; margin-bottom: 20px; }
        input, textarea { width: 100%; padding: 10px; margin-bottom: 15px; border: 1px solid #ccc; border-radius: 6px; }
        button { background: #486569; color: white; padding: 10px 20px; border: none; border-radius: 6px; cursor: pointer; }
        button:hover { background: #365057; }
        .message { padding: 10px; margin-bottom: 10px; border-radius: 6px; }
        .success { background: #d4edda; color: #155724; }
        .error { background: #f8d7da; color: #721c24; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Post an Announcement</h2>

        <?php if (!empty($success)): ?>
            <div class="message success"><?= htmlspecialchars($success) ?></div>
        <?php elseif (!empty($error)): ?>
            <div class="message error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST">
            <label>Title:</label>
            <input type="text" name="title" required>

            <label>Message:</label>
            <textarea name="message" rows="5" required></textarea>

            <button type="submit">Submit Announcement</button>
        </form>
    </div>
</body>
</html>
