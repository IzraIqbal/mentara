<?php
require 'db.php';
session_start();

if (!isset($_GET['id'])) {
    die("Access denied.");
}

$therapist_id = $_GET['id'];

// Handle resource upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload_resource'])) {
    $description = $_POST['description'];
    $therapy_type = $_POST['therapy_type'];
    $uploaded_date = date('Y-m-d');

    // Handle file upload
    if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/resources/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $filename = basename($_FILES['file']['name']);
        $targetFilePath = $uploadDir . time() . '_' . $filename;

        if (move_uploaded_file($_FILES['file']['tmp_name'], $targetFilePath)) {
            $stmt = $conn->prepare("INSERT INTO resources (therapist_id, description, uploaded_date, therapy_type, file_url) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$therapist_id, $description, $uploaded_date, $therapy_type, $targetFilePath]);
            $msg = "Resource uploaded successfully.";
        } else {
            $msg = "Failed to upload file.";
        }
    } else {
        $msg = "No file uploaded or error during upload.";
    }
}

// Handle sending reminders
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_reminder'])) {
    $client_id = $_POST['client_id'];
    $message = $_POST['message'];
    $send_date = date('Y-m-d H:i:s');

    $stmt = $conn->prepare("INSERT INTO reminders (therapist_id, client_id, message, send_date) VALUES (?, ?, ?, ?)");
    $stmt->execute([$therapist_id, $client_id, $message, $send_date]);
    $reminder_msg = "Reminder sent.";
}

// Fetch clients to select for reminders
$clients_stmt = $conn->prepare("SELECT id, name FROM users WHERE user_type = 'Client'");
$clients_stmt->execute();
$clients = $clients_stmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Upload Resources & Send Reminders</title>
    <link href="https://fonts.googleapis.com/css2?family=Winky+Sans&display=swap" rel="stylesheet" />
    <style>
        * {
            box-sizing: border-box;
            font-family: 'Winky Sans', sans-serif;
        }

        body {
            background: #e9f0f5;
            margin: 0;
            padding: 30px 15px;
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 100vh;
            color: #2f3e46;
        }

        h2 {
            color: #3a5068;
            margin-bottom: 25px;
            font-size: 1.8rem;
            border-bottom: 3px solid #486569;
            padding-bottom: 8px;
            width: 100%;
            max-width: 520px;
        }

        form {
            background: white;
            max-width: 520px;
            width: 100%;
            padding: 25px 30px;
            border-radius: 12px;
            box-shadow: 0 12px 24px rgba(30, 129, 176, 0.18);
            margin-bottom: 40px;
            transition: box-shadow 0.3s ease;
        }

        form:hover {
            box-shadow: 0 18px 30px rgba(30, 129, 176, 0.28);
        }

        label {
            display: block;
            font-weight: 700;
            margin-bottom: 10px;
            font-size: 1.1rem;
            color: #486569;
        }

        input[type="text"],
        input[type="file"],
        select,
        textarea {
            width: 100%;
            padding: 12px 14px;
            margin-bottom: 20px;
            border: 2px solid #b0c4de;
            border-radius: 10px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
            font-family: 'Winky Sans', sans-serif;
        }

        input[type="text"]:focus,
        input[type="file"]:focus,
        select:focus,
        textarea:focus {
            border-color: #486569;
            outline: none;
            box-shadow: 0 0 8px #486569;
        }

        button {
            background-color: #486569;
            border: none;
            padding: 14px 28px;
            border-radius: 12px;
            color: white;
            font-size: 1.1rem;
            font-weight: 700;
            cursor: pointer;
            width: 100%;
            transition: background-color 0.3s ease;
            letter-spacing: 0.05em;
            box-shadow: 0 6px 14px rgba(30, 129, 176, 0.4);
        }

        button:hover {
            background-color: #486569;
            box-shadow: 0 8px 20px rgba(21, 93, 126, 0.6);
        }

        p.message {
            background-color: #d3f9d8;
            color: #2d6a4f;
            padding: 15px 20px;
            border-radius: 10px;
            max-width: 520px;
            box-shadow: 0 6px 12px rgba(45, 106, 79, 0.15);
            font-weight: 600;
            margin-bottom: 25px;
            text-align: center;
        }

        a.back-link {
            max-width: 520px;
            display: inline-block;
            margin-top: 10px;
            background-color: #486569;
            color: white;
            padding: 12px 20px;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 700;
            transition: background-color 0.3s ease;
            box-shadow: 0 6px 12px rgba(30, 129, 176, 0.3);
            text-align: center;
        }

        a.back-link:hover {
            background-color: #486569;
        }

        /* Reminder specific styling */

        .reminder-form label {
            position: relative;
            padding-left: 38px;
        }

        .reminder-form label::before {
            content: '🔔';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            font-size: 24px;
            color: #1e81b0;
            text-shadow: 0 0 5px #1e81b0aa;
        }

        textarea {
            resize: vertical;
            min-height: 100px;
        }
    </style>
</head>
<body>
    <h2>Upload Resource</h2>
    <?php if (!empty($msg)) : ?>
        <p class="message"><?= htmlspecialchars($msg) ?></p>
    <?php endif; ?>
    <form method="POST" enctype="multipart/form-data" autocomplete="off" novalidate>
        <label for="description">Description:</label>
        <input type="text" id="description" name="description" placeholder="Brief description" required />

        <label for="therapy_type">Therapy Type:</label>
        <input type="text" id="therapy_type" name="therapy_type" placeholder="e.g., CBT, Mindfulness" required />

        <label for="file">File:</label>
        <input type="file" id="file" name="file" required accept=".pdf,.doc,.docx,.ppt,.pptx,.txt,.mp4,.jpg,.png" />

        <button type="submit" name="upload_resource">Upload Resource</button>
    </form>

    <h2>Send Reminder to Client</h2>
    <?php if (!empty($reminder_msg)) : ?>
        <p class="message"><?= htmlspecialchars($reminder_msg) ?></p>
    <?php endif; ?>
    <form method="POST" class="reminder-form" autocomplete="off" novalidate>
        <label for="client_id">Select Client:</label>
        <select id="client_id" name="client_id" required>
            <option value="" disabled selected>-- Select Client --</option>
            <?php foreach ($clients as $client): ?>
                <option value="<?= $client['id'] ?>"><?= htmlspecialchars($client['name']) ?></option>
            <?php endforeach; ?>
        </select>

        <label for="message">Message:</label>
        <textarea id="message" name="message" placeholder="Type your reminder message here..." required></textarea>

        <button type="submit" name="send_reminder">Send Reminder</button>
    </form>

    <a href="therapist-dashboard.php?id=<?= $therapist_id ?>" class="back-link">← Back to Dashboard</a>
</body>
</html>
