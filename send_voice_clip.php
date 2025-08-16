<?php
session_start();

if (!isset($_FILES['audio']['tmp_name'], $_POST['session_id'], $_POST['role'])) {
    http_response_code(400);
    echo "Invalid request.";
    exit;
}

$session_id = $_POST['session_id'];
$role = $_POST['role'];

// Save to a temporary in-memory location (e.g., RAM disk) or just PHP temp
$tmpName = $_FILES['audio']['tmp_name'];
$dest = sys_get_temp_dir() . "/voice_{$session_id}_{$role}.webm";
move_uploaded_file($tmpName, $dest);
