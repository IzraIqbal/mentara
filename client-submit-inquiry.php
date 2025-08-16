<?php
require 'db.php';
session_start();

if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'Client') {
    header("Location: login.php");
    exit();
}


$email = $_SESSION['email'];  // Must exist in session
$role = 'Client'; // or dynamically assign based on your logic

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message = trim($_POST['message'] ?? '');

    if (empty($message)) {
        $error = "Please fill in the message.";
    } else {
        // Insert without user_id to avoid foreign key issue
        $stmt = $conn->prepare("INSERT INTO inquiries (message, email, role, submitted_at) VALUES (?, ?, ?, NOW())");
        $inserted = $stmt->execute([$message, $email, $role]);

        if ($inserted) {
            $success = "Your inquiry has been submitted successfully.";
        } else {
            $error = "Failed to submit your inquiry. Please try again later.";
        }
    }
}
?>





<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Submit Inquiry | Mentara</title>
      <link
      href="https://fonts.googleapis.com/css2?family=Winky+Sans:ital,wght@0,300..900;1,300..900&display=swap"
      rel="stylesheet"
    />
    <style>
        body { font-family: Winky Sans, sans-serif; max-width: 600px; margin: 50px auto; padding: 20px; background-color:beige;align-items: center;text-align: center;}
        label { display: block; margin-top: 15px; font-weight: bold; color:#405e62}
        h1{
            color:#486569;text-align: center;
        }
        img{
             height: 150px;
        }
        input[type="text"], textarea { width: 100%; padding: 8px; margin-top: 5px;border-radius: 10px; }
        button { margin-top: 20px; padding: 10px 15px;   background: linear-gradient(90deg, #405e62, #8ab2c0);; color: white; border: none; border-radius: 5px; cursor: pointer;font-family: Winky Sans, sans-serif; }
        .error { color: red; margin-top: 15px; }
        .success { color: green; margin-top: 15px; }
    </style>
</head>
<body>
    <img src="images/clientdashboardinquiry_image_login (1).png" >
    <h1>Submit an Inquiry</h1>

    <?php if ($error): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

   <form method="POST">
  <label for="message">Your Inquiry:</label><br>
  <textarea name="message" id="message" required></textarea><br>
  <button type="submit">Submit</button>
</form>
</body>
</html>
