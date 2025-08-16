<?php
require 'db.php';
session_start();

if (!isset($_GET['id'])) {
    die("Access denied.");
}

$client_id = intval($_GET['id']);

// Fetch approved therapists to show as options
$stmt = $conn->prepare("SELECT id, name, speciality FROM therapists WHERE status = 'approved'");
$stmt->execute();
$therapists = $stmt->fetchAll();

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $therapist_id = $_POST['therapist_id'];
    $session_date = $_POST['session_date'];
    $mode = $_POST['mode'];
    $therapy_type = $_POST['therapy_type'];

    // Simple validation
    if (!$therapist_id || !$session_date || !$mode || !$therapy_type) {
        $message = 'Please fill all fields.';
    } else {
        // Insert session booking request
        $insert = $conn->prepare("INSERT INTO sessions (client_id, therapist_id, session_date, mode, therapy_type) VALUES (?, ?, ?, ?, ?)");
        $insert->execute([$client_id, $therapist_id, $session_date, $mode, $therapy_type]);
        $message = "Booking request sent successfully!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Book Session</title>
    <link href="https://fonts.googleapis.com/css2?family=Winky+Sans&display=swap" rel="stylesheet">
    <style>
        body {
             font-family: "Winky Sans", sans-serif;
            background: #f4f7fb;
            margin: 0;
            padding: 0;
            color: #2c3e50;
        }

        .hero {
            background-image: url('images/bg_image_login.png'); 
            background-size: cover;
            background-position: center;
            height: 250px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-shadow: 0 2px 4px rgba(0,0,0,0.5);
        }

        .hero h1 {
            font-size: 2.5em;
            background-color: rgba(0,0,0,0.4);
            padding: 10px 20px;
            border-radius: 10px;
        }

        .container {
            max-width: 650px;
            margin: -60px auto 40px;
            background: beige;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            border-left: 10px solid #486569;
        }

        h2 {
            color: #486569;
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 6px;
            font-weight: bold;
            color: #486569;
        }

        select, input[type=datetime-local], input[type=text], button {
            width: 100%;
            padding: 12px;
            margin-bottom: 20px;
            border-radius: 8px;
            border: 1px solid #ccc;
            font-size: 16px;
        }

        button {
            background-color: #8ab2c0;
            color: white;
            border: none;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #486569;
        }

        .message {
            margin-bottom: 15px;
            color: green;
            font-weight: bold;
        }
    </style>
</head>
<body>

<div class="hero">
    <h1>Book a Therapy Session</h1>
</div>

<div class="container">
    <?php if ($message): ?>
        <p class="message"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <form method="post">
        <label for="therapist_id">Select Therapist</label>
        <select name="therapist_id" id="therapist_id" required>
            <option value="">-- Choose a Therapist --</option>
            <?php foreach ($therapists as $t): ?>
                <option value="<?= $t['id'] ?>"><?= htmlspecialchars($t['name'] . " (" . $t['speciality'] . ")") ?></option>
            <?php endforeach; ?>
        </select>

        <label for="session_date">Session Date & Time</label>
        <input type="datetime-local" id="session_date" name="session_date" required min="<?= date('Y-m-d\TH:i') ?>">

        <label for="mode">Session Mode</label>
        <select id="mode" name="mode" required>
            <option value="">-- Select Mode --</option>
            <option value="video">Video</option>
            <option value="audio">Audio</option>
            <option value="chat">Chat</option>
        </select>

        <label for="therapy_type">Therapy Type</label>
        <input type="text" id="therapy_type" name="therapy_type" placeholder="e.g. Cognitive Behavioral Therapy" required>

        <button type="submit">Book Session</button>
    </form>
</div>

</body>
</html>
