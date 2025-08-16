<?php
require 'db.php';
session_start();

if (!isset($_GET['id'])) {
    die("Access denied.");
}

$client_id = $_GET['id'];

// Fetch resources available (you may filter by therapy_type if desired)
$stmt = $conn->prepare("SELECT r.*, t.name AS therapist_name FROM resources r JOIN therapists t ON r.therapist_id = t.id ORDER BY r.uploaded_date DESC");
$stmt->execute();
$resources = $stmt->fetchAll();

// Fetch reminders for this client
$reminder_stmt = $conn->prepare("SELECT r.*, t.name AS therapist_name FROM reminders r JOIN therapists t ON r.therapist_id = t.id WHERE r.client_id = ? ORDER BY r.send_date DESC");
$reminder_stmt->execute([$client_id]);
$reminders = $reminder_stmt->fetchAll();

?>


<!DOCTYPE html>
<html>
<head>
    <title>Your Resources & Reminders</title>
    <link href="https://fonts.googleapis.com/css2?family=Winky+Sans&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        * {
            box-sizing: border-box;
            font-family: "Winky Sans", sans-serif;
        }

        body {
            background: #f0f4f8;
            margin: 0;
            padding: 40px;
            color: #333;
        }

        .container {
            max-width: 950px;
            margin: auto;
        }

        h2 {
            color: #2c3e50;
            font-size: 1.8rem;
            margin-top: 50px;
            border-left: 6px solid #8ab2c0;
            padding-left: 15px;
        }

        .card {
            background: #ffffff;
            padding: 25px 30px;
            margin: 20px 0;
            border-radius: 12px;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.08);
            transition: transform 0.2s ease;
        }

        .card:hover {
            transform: translateY(-3px);
        }

        .resource i,
        .reminder i {
            font-size: 24px;
            margin-right: 10px;
            color: #8ab2c0;
        }

        .resource .info,
        .reminder .info {
            display: flex;
            align-items: center;
            margin-bottom: 12px;
        }

        .card strong {
            color: #34495e;
        }

        .download-link {
            display: inline-block;
            margin-top: 12px;
            background: #8ab2c0;
            color: white;
            padding: 10px 18px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 500;
            transition: background-color 0.3s ease;
        }

        .download-link:hover {
            background-color: #789fad;
        }

        .date {
            display: block;
            margin-top: 6px;
            font-size: 0.9em;
            color: #6b7280;
        }

        .reminder {
            background: #fff9ec;
            border-left: 5px solid #f0ad4e;
        }

        .reminder i {
            color: #f0ad4e;
        }

        .back-link {
            display: inline-block;
            margin-top: 40px;
            background-color: #405e62;
            color: white;
            padding: 10px 20px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 500;
            transition: background-color 0.3s ease;
        }

        .back-link:hover {
            background-color: #2c3e50;
        }

        .message-content {
            margin-top: 10px;
            line-height: 1.5;
        }

    </style>
</head>
<body>
<div class="container">
    <h2><i class="fas fa-folder-open"></i> Resources Shared by Therapists</h2>
    <?php if (empty($resources)) : ?>
        <p>No resources available at the moment.</p>
    <?php else: ?>
        <?php foreach ($resources as $res): ?>
            <div class="card resource">
                <div class="info"><i class="fas fa-book-medical"></i> <strong>Description:</strong>&nbsp;<?= htmlspecialchars($res['description']) ?></div>
                <div class="info"><i class="fas fa-brain"></i> <strong>Therapy Type:</strong>&nbsp;<?= htmlspecialchars($res['therapy_type']) ?></div>
                <div class="info"><i class="fas fa-user-md"></i> <strong>Uploaded by:</strong>&nbsp;Dr. <?= htmlspecialchars($res['therapist_name']) ?></div>
                <span class="date"><i class="fas fa-calendar-alt"></i> Uploaded on: <?= htmlspecialchars($res['uploaded_date']) ?></span><br>
                <a href="<?= htmlspecialchars($res['file_url']) ?>" target="_blank" class="download-link"><i class="fas fa-download"></i> View/Download</a>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <h2><i class="fas fa-bell"></i> Your Reminders & Alerts</h2>
    <?php if (empty($reminders)) : ?>
        <p>No reminders at the moment.</p>
    <?php else: ?>
        <?php foreach ($reminders as $rem): ?>
            <div class="card reminder">
                <div class="info"><i class="fas fa-exclamation-circle"></i> <strong>From Dr. <?= htmlspecialchars($rem['therapist_name']) ?>:</strong></div>
                <div class="message-content"><?= nl2br(htmlspecialchars($rem['message'])) ?></div>
                <span class="date"><i class="fas fa-clock"></i> Sent on: <?= htmlspecialchars($rem['send_date']) ?></span>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <a href="client-dashboard.php?id=<?= $client_id ?>" class="back-link"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
</div>
</body>
</html>