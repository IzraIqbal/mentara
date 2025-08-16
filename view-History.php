<?php
require 'db.php';
session_start();

if (!isset($_GET['id'])) {
    die("Client ID missing.");
}
$client_id = $_GET['id'];

$stmt = $conn->prepare("SELECT s.*, t.name AS therapist_name FROM sessions s
    JOIN therapists t ON s.therapist_id = t.id
    WHERE s.client_id = ?");
$stmt->execute([$client_id]);
$sessions = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Session History | Mentara</title>
    <meta charset="UTF-8">
    <link href="https://fonts.googleapis.com/css2?family=Winky+Sans&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
              font-family: "Winky Sans", sans-serif;
        }

        body {
            background-color: #f5f5dc;
            padding: 60px 20px;
            color: #486569;
        }

        .container {
            max-width: 900px;
            margin: auto;
        }

        h2 {
            text-align: center;
            margin-bottom: 40px;
            font-size: 32px;
            color: #2c3e50;
        }

        .session-card {
            background-color: #ffffff;
            border-radius: 12px;
            padding: 20px 25px;
            margin-bottom: 30px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            transition: transform 0.3s ease;
        }

        .session-card:hover {
            transform: translateY(-5px);
        }

        .session-card p {
            margin: 8px 0;
            font-size: 16px;
        }

        .session-card p strong {
            color: #2f4f4f;
        }

        .no-sessions {
            text-align: center;
            font-size: 18px;
            color: #888;
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 40px;
            text-decoration: none;
            font-weight: bold;
            color: #486569;
            border: 2px solid #486569;
            padding: 10px 18px;
            border-radius: 8px;
            width: fit-content;
            margin-left: auto;
            margin-right: auto;
            transition: 0.3s ease;
        }

        .back-link:hover {
            background-color: #486569;
            color: white;
        }

        footer {
            text-align: center;
            margin-top: 60px;
            padding-top: 20px;
            border-top: 1px solid #ccc;
            font-size: 14px;
            color: #888;
        }

        @media (max-width: 600px) {
            .session-card {
                padding: 15px;
            }

            h2 {
                font-size: 26px;
            }
        }
    </style>
</head>
<body>
<div class="container">
    <h2>My Therapy History</h2>

    <?php if (count($sessions) > 0): ?>
        <?php foreach ($sessions as $s): ?>
            <div class="session-card">
                <p><strong>Date:</strong> <?= $s['session_date'] ?></p>
                <p><strong>Mode:</strong> <?= $s['mode'] ?></p>
                <p><strong>Therapy Type:</strong> <?= $s['therapy_type'] ?></p>
                <p><strong>Therapist:</strong> Dr. <?= $s['therapist_name'] ?></p>
                <p><strong>Comments/Notes:</strong><br><?= nl2br(htmlspecialchars($s['status'])) ?></p>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p class="no-sessions">No session history found.</p>
    <?php endif; ?>

    <a href="client-dashboard.php?id=<?= $client_id ?>" class="back-link">← Back to Dashboard</a>
</div>

<footer>
    © <?= date('Y') ?> Mentara. All rights reserved.
</footer>
</body>
</html>
