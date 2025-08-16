<?php
require 'db.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_POST['user_id'];
    $user_role = $_POST['user_role'];
    $subject = trim($_POST['subject']);
    $message = trim($_POST['message']);

    if (!empty($subject) && !empty($message)) {
        $stmt = $conn->prepare("INSERT INTO inquiries (user_id, user_role, subject, message, submitted_at) VALUES (?, ?, ?, ?, NOW())");
        $stmt->execute([$user_id, $user_role, $subject, $message]);

        header("Location: client-dashboard.php?id=" . urlencode($user_id) . "&msg=Inquiry sent");
        exit();
    } else {
        echo "All fields are required.";
    }
} else {
    die("Invalid request.");
}
