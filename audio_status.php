<?php
require 'db.php';
session_start();

$session_id = $_GET['session_id'];
$role = $_GET['role']; // 'client' or 'therapist'

$stmt = $conn->prepare("SELECT status FROM audio_sessions WHERE session_id = ? AND user_role = ?");
$stmt->execute([$session_id, $role]);
$data = $stmt->fetch();

$status = $data ? $data['status'] : 'disconnected';
echo json_encode(['status' => $status]);
