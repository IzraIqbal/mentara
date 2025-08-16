<?php
require 'db.php';
session_start();

if (!isset($_GET['id'])) {
    die("Access denied.");
}
$therapist_id = intval($_GET['id']);

// Fetch pending or rescheduled sessions assigned to this therapist
$stmt = $conn->prepare("SELECT s.session_id, s.session_date, s.mode, s.therapy_type, s.status, u.name AS client_name 
                        FROM sessions s
                        JOIN users u ON s.client_id = u.id
                        WHERE s.therapist_id = ? AND s.status IN ('pending', 'rescheduled')
                        ORDER BY s.session_date ASC");
$stmt->execute([$therapist_id]);
$sessions = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $session_id = $_POST['session_id'];
    $action = $_POST['action']; // approve or reschedule
    $new_date = $_POST['new_date'] ?? null;

    if ($action === 'approve') {
        $update = $conn->prepare("UPDATE sessions SET status = 'approved' WHERE session_id = ?");
        $update->execute([$session_id]);
    } elseif ($action === 'reschedule' && $new_date) {
        $update = $conn->prepare("UPDATE sessions SET status = 'rescheduled', session_date = ? WHERE session_id = ?");
        $update->execute([$new_date, $session_id]);
    }

    header("Location: manage-availability.php?id=$therapist_id");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Bookings - Therapist Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Winky+Sans&display=swap" rel="stylesheet">
    <style>
        body {
             font-family: "Winky Sans", sans-serif;
            background-color: beige;
            padding: 30px;
            color: #486569;
        }

        h1 {
            text-align: center;
            margin-bottom: 40px;
            color: #486569;
        }

        table {
            width: 90%;
            max-width: 1000px;
            margin: auto;
            border-collapse: collapse;
            background-color: white;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            border-radius: 12px;
            overflow: hidden;
        }

        th {
            background-color: #486569;
            color: white;
            text-align: left;
            padding: 15px;
            font-size: 16px;
        }

        td {
            padding: 14px 15px;
            border-bottom: 1px solid #e0e0e0;
        }

        tr:last-child td {
            border-bottom: none;
        }

        form {
            display: inline-block;
        }

        input[type="datetime-local"] {
            padding: 6px 8px;
            border: 1px solid #ccc;
            border-radius: 6px;
            margin-bottom: 5px;
            background-color: #f4f4f4;
            color: #486569;
        }

        button {
            padding: 8px 14px;
            border: none;
            border-radius: 6px;
            font-weight: bold;
            cursor: pointer;
        }

        .approve-btn {
            background-color: #486569;
            color: white;
            margin-right: 5px;
        }

        .approve-btn:hover {
            background-color: #394e54;
        }

        .reschedule-btn {
            background-color: #8ab2c0;
            color: white;
        }

        .reschedule-btn:hover {
            background-color: #6b9da9;
        }

        a {
            display: block;
            width: fit-content;
            margin: 40px auto 0;
            padding: 10px 18px;
            background-color: #486569;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            transition: background-color 0.3s ease;
        }

        a:hover {
            background-color: #394e54;
        }

        p {
            text-align: center;
            font-size: 16px;
        }
    </style>
</head>
<body>
    <h1>Manage Booking Requests</h1>

    <?php if (count($sessions) === 0): ?>
        <p>No pending or rescheduled bookings at the moment.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Client</th>
                    <th>Session Date</th>
                    <th>Mode</th>
                    <th>Therapy Type</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($sessions as $s): ?>
                <tr>
                    <td><?= htmlspecialchars($s['client_name']) ?></td>
                    <td><?= date('Y-m-d H:i', strtotime($s['session_date'])) ?></td>
                    <td><?= htmlspecialchars($s['mode']) ?></td>
                    <td><?= htmlspecialchars($s['therapy_type']) ?></td>
                    <td><?= ucfirst($s['status']) ?></td>
                    <td>
                        <form method="post">
                            <input type="hidden" name="session_id" value="<?= $s['session_id'] ?>">
                            <button type="submit" name="action" value="approve" class="approve-btn">Approve</button>
                        </form>
                        <form method="post">
                            <input type="hidden" name="session_id" value="<?= $s['session_id'] ?>">
                            <input type="datetime-local" name="new_date" required>
                            <button type="submit" name="action" value="reschedule" class="reschedule-btn">Reschedule</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
    <?php
// Fetch approved and upcoming sessions
$now = date('Y-m-d H:i:s');
$stmt2 = $conn->prepare("SELECT s.session_id, s.session_date, s.mode, s.therapy_type, s.status, u.name AS client_name, s.client_id 
                         FROM sessions s
                         JOIN users u ON s.client_id = u.id
                         WHERE s.therapist_id = ? AND s.status = 'approved' AND s.session_date >= ?
                         ORDER BY s.session_date ASC");
$stmt2->execute([$therapist_id, $now]);
$approved_sessions = $stmt2->fetchAll();
?>

<?php if (count($approved_sessions) > 0): ?>
    <h1>Approved Upcoming Sessions</h1>
    <table>
        <thead>
            <tr>
                <th>Client</th>
                <th>Session Date</th>
                <th>Mode</th>
                <th>Therapy Type</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($approved_sessions as $s): ?>
            <tr>
                <td><?= htmlspecialchars($s['client_name']) ?></td>
                <td><?= date('Y-m-d H:i', strtotime($s['session_date'])) ?></td>
                <td><?= htmlspecialchars($s['mode']) ?></td>
                <td><?= htmlspecialchars($s['therapy_type']) ?></td>
                <td>
                    <?php if ($s['mode'] == 'audio'): ?>
                        <a href="attend-audio-session.php?client_id=<?= $s['client_id'] ?>&session_date=<?= urlencode($s['session_date']) ?>" target="_blank" class="conduct-link">Conduct Audio Session</a>
                    <?php elseif ($s['mode'] == 'video'): ?>
                        <a href="attend-video-session.php?client_id=<?= $s['client_id'] ?>&session_date=<?= urlencode($s['session_date']) ?>" target="_blank" class="conduct-link">Conduct Video Session</a>
                    <?php elseif ($s['mode'] == 'chat'): ?>
                       <a href="attend-chat-session.php?session_id=<?= $s['session_id'] ?>" target="_blank" class="conduct-link">Start Chat</a>

                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>


    <a href="therapist-dashboard.php?id=<?= $therapist_id ?>">Back to Dashboard</a>
</body>
</html>