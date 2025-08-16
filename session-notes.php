<?php
require 'db.php';
session_start();

if (!isset($_GET['id'])) {
    die("Therapist ID missing.");
}
$therapist_id = $_GET['id'];

// Fetch sessions assigned to this therapist
$stmt = $conn->prepare("SELECT s.*, u.name AS client_name FROM sessions s
    JOIN users u ON s.client_id = u.id
    WHERE s.therapist_id = ?");
$stmt->execute([$therapist_id]);
$sessions = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $session_id = $_POST['session_id'];
    $notes = $_POST['notes'];

    $update = $conn->prepare("UPDATE sessions SET status = ? WHERE session_id = ?");
    $update->execute([$notes, $session_id]);

    echo "<script>alert('Notes saved successfully!'); window.location.href='session-notes.php?id=$therapist_id';</script>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Write Session Notes | Mentara</title>
    <link href="https://fonts.googleapis.com/css2?family=Winky+Sans&family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Winky Sans', sans-serif;
            background: #f3f7f9;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 1000px;
            margin: 50px auto;
            padding: 30px;
            background-color: #ffffff;
            border-radius: 16px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.08);
        }

        h2 {
            text-align: center;
            color: #405e62;
            font-size: 2rem;
            margin-bottom: 30px;
        }

        .session-form {
            border: 1px solid #e2e8f0;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 30px;
            background-color: #f9fdfb;
        }

        .session-form p {
            margin: 8px 0;
            color: #333;
        }

        .session-form label {
            font-weight: 600;
            display: block;
            margin-top: 10px;
            margin-bottom: 5px;
        }

        .session-form textarea {
            width: 100%;
            padding: 12px;
            font-size: 14px;
            border-radius: 8px;
            border: 1px solid #cbd5e0;
            resize: vertical;
            font-family: 'Inter', sans-serif;
        }

        .session-form button {
            background-color: #8ab2c0;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            margin-top: 12px;
            font-size: 15px;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .session-form button:hover {
            background-color: #36474dff;
        }

        .client-header {
            font-weight: 600;
            font-size: 16px;
            color: #2d3748;
            margin-bottom: 10px;
        }

        @media screen and (max-width: 768px) {
            .container {
                margin: 20px;
                padding: 20px;
            }

            .session-form textarea {
                font-size: 13px;
            }

            .session-form button {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Write Session Notes</h2>

        <?php if (count($sessions) > 0): ?>
            <?php foreach ($sessions as $session): ?>
                <form method="POST" class="session-form">
                    <div class="client-header">
                         Client: <?= htmlspecialchars($session['client_name']) ?>
                    </div>
                    <p><strong>Date:</strong> <?= $session['session_date'] ?></p>
                    <p><strong>Type:</strong> <?= $session['therapy_type'] ?> | <strong>Mode:</strong> <?= $session['mode'] ?></p>
                    
                    <label for="notes">Session Notes / Feedback</label>
                    <textarea name="notes" rows="4" placeholder="Write your notes or progress summary..."><?= htmlspecialchars($session['status']) ?></textarea>

                    <input type="hidden" name="session_id" value="<?= $session['session_id'] ?>">
                    <button type="submit"> Save Notes</button>
                </form>
            <?php endforeach; ?>
        <?php else: ?>
            <p style="text-align:center; color:#718096;">No sessions assigned yet.</p>
        <?php endif; ?>
    </div>
</body>
</html>
