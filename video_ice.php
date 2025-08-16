<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $stmt = $conn->prepare("SELECT candidate FROM ice_candidates WHERE session_id = ? AND role = ?");
    $stmt->execute([$_GET['session_id'], $_GET['role']]);
    $rows = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo json_encode(array_map('json_decode', $rows));
} else {
    $data = json_decode(file_get_contents("php://input"), true);
    $stmt = $conn->prepare("INSERT INTO ice_candidates (session_id, role, candidate) VALUES (?, ?, ?)");
    $stmt->execute([$data['session_id'], $data['role'], json_encode($data['candidate'])]);
}
