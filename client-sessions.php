<?php
require 'db.php';
session_start();



if (!isset($_GET['id'])) {
    die("Access denied.");
}

$client_id = intval($_GET['id']);

// Fetch client's sessions
$stmt = $conn->prepare("SELECT s.session_date, s.mode, s.therapy_type, s.status, t.name AS therapist_name 
                        FROM sessions s 
                        JOIN therapists t ON s.therapist_id = t.id 
                        WHERE s.client_id = ? 
                        ORDER BY s.session_date DESC");
$stmt->execute([$client_id]);
$sessions = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <title>My Sessions</title>
    <link href="https://fonts.googleapis.com/css2?family=Winky+Sans&display=swap" rel="stylesheet">
    <style>
        body {  font-family: "Winky Sans", sans-serif; max-width: 700px; margin: auto; padding: 20px; background: #f4f7fb; }
        table { width: 100%; border-collapse: collapse; background: white; border-radius: 8px; overflow: hidden; }
        th, td { padding: 12px 15px; border-bottom: 1px solid #ddd; }
        th { background-color: #8ab2c0; color: white; }
    </style>
</head>
<body>
    <h1 style="color:#486569">My Therapy Sessions</h1>
    <?php if (count($sessions) === 0): ?>
        <p>You have no therapy sessions booked.</p>
    <?php else: ?>
        <table>
            <thead>
    <tr>
        <th>Therapist</th>
        <th>Date & Time</th>
        <th>Mode</th>
        <th>Therapy Type</th>
        <th>Status</th>
        <th>Action</th> <!-- New column -->
    </tr>
</thead>
<tbody>
    <?php foreach ($sessions as $s): 
        $mode = strtolower($s['mode']);
        $sessionDateParam = urlencode($s['session_date']);
        $clientIdParam = urlencode($client_id);
        // Determine the attendance URL based on mode
        $attendUrl = '';
        if ($mode === 'video') {
            $attendUrl = "attend_video.php?session_date=$sessionDateParam&client_id=$clientIdParam";
        } elseif ($mode === 'audio') {
            $attendUrl = "attend_audio.php?session_date=$sessionDateParam&client_id=$clientIdParam";
        } elseif ($mode === 'chat') {
            $attendUrl = "attend_chat.php?session_date=$sessionDateParam&client_id=$clientIdParam";
        }
    ?>
        <tr>
            <td><?= htmlspecialchars($s['therapist_name']) ?></td>
            <td><?= date('Y-m-d H:i', strtotime($s['session_date'])) ?></td>
            <td><?= htmlspecialchars($s['mode']) ?></td>
            <td><?= htmlspecialchars($s['therapy_type']) ?></td>
            <td><?= ucfirst($s['status']) ?></td>
            <td>
                <?php if ($attendUrl && $s['status'] === 'approved'): ?>
                    <a href="<?= $attendUrl ?>" style="text-decoration:none;">
                        <button style="padding:6px 12px; background:#486569; color:#fff; border:none; border-radius:4px; cursor:pointer;">
                            Attend Session
                        </button>
                    </a>
                <?php else: ?>
                    <em>Unavailable</em>
                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; ?>
</tbody>
        </table>
    <?php endif; ?>
</body>
</html>
