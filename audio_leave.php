<?php
require 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') exit;

$session_id = $_POST['session_id'];
$user_id = $_POST['user_id'];

$stmt = $conn->prepare("UPDATE audio_sessions SET status = 'disconnected' WHERE session_id = ? AND user_id = ?");
$stmt->execute([$session_id, $user_id]);

echo json_encode(['status' => 'disconnected']);
