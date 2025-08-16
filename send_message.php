<?php
require 'db.php';
session_start();

$session_id = $_POST['session_id'];
$sender_role = $_POST['sender_role']; // client or therapist
$message = trim($_POST['message']);

if ($session_id && $sender_role && $message) {
    $stmt = $conn->prepare("INSERT INTO messages (session_id, sender_role, message) VALUES (?, ?, ?)");
    $stmt->execute([$session_id, $sender_role, $message]);
    echo "success";
} else {
    echo "invalid";
}
?>
