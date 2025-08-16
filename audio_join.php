<?php
require 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') exit;

$session_id = $_POST['session_id'];
$user_id = $_POST['user_id'];
$role = $_POST['role'];

$stmt = $conn->prepare("REPLACE INTO audio_sessions (session_id, user_id, user_role, status) VALUES (?, ?, ?, 'connected')");
$stmt->execute([$session_id, $user_id, $role]);

echo json_encode(['status' => 'connected']);
