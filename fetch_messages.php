<?php
require 'db.php';

$session_id = $_GET['session_id'];

$stmt = $conn->prepare("SELECT sender_role, message, sent_at FROM messages WHERE session_id = ? ORDER BY sent_at ASC");
$stmt->execute([$session_id]);
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode($messages);
?>
