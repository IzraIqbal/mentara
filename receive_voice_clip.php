<?php
$session_id = $_GET['session_id'];
$role = $_GET['role']; // sender's role = opposite of receiver

$path = sys_get_temp_dir() . "/voice_{$session_id}_{$role}.webm";

if (file_exists($path)) {
    header('Content-Type: audio/webm');
    readfile($path);
    unlink($path); // DELETE after sending for privacy
} else {
    http_response_code(204); // No Content
}
