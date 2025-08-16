<?php
require 'db.php';
session_start();

// Check if therapist ID is provided
if (!isset($_GET['id'])) {
    die("Access denied.");
}

$therapist_id = $_GET['id'];
$success = "";
$error = "";

// Fetch therapist info
$stmtTherapist = $conn->prepare("SELECT email FROM therapists WHERE id = ?");
$stmtTherapist->execute([$therapist_id]);
$therapist = $stmtTherapist->fetch(PDO::FETCH_ASSOC);

if (!$therapist) {
    die("Therapist not found.");
}

$email = $therapist['email'];
$role = "Therapist";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message = trim($_POST['message']);

    if ($message) {
        $stmt = $conn->prepare("INSERT INTO inquiries (email, message, role, submitted_at) VALUES (?, ?, ?, NOW())");
        if ($stmt->execute([$email, $message, $role])) {
            $success = "Inquiry submitted successfully.";
        } else {
            $error = "Something went wrong. Please try again.";
        }
    } else {
        $error = "Please fill in the message field.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Submit Inquiry</title>
     <link
      href="https://fonts.googleapis.com/css2?family=Winky+Sans:ital,wght@0,300..900;1,300..900&display=swap"
      rel="stylesheet"
    />
    <style>
        body {
            font-family: "Winky Sans", sans-serif;
            background: beige;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 700px;
            margin: 60px auto;
            background: white;
            padding: 30px 40px;
            border-radius: 10px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
            background: beige;
        }

        h2 {
            color: #486569;
            text-align: center;
            font-size: 30px;
        }

        label {
            display: block;
            margin: 15px 0 5px;
            font-weight: bold;
            color:#486569;
        }

        input[type="email"], textarea {
            width: 100%;
            padding: 12px;
            border-radius: 6px;
            border: 1px solid #ccc;
            box-sizing: border-box;
        }

        textarea {
            resize: vertical;
            height: 150px;
        }

        button {
            margin-top: 20px;
            padding: 12px 20px;
           background:linear-gradient(90deg, #405e62, #8ab2c0);
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
        }

        button:hover {
            background-color:#405e62;
        }

        .message {
            margin-top: 15px;
            padding: 10px;
            border-radius: 6px;
        }

        .success {
            background-color: #c6f6d5;
            color: #22543d;
        }

        .error {
            background-color: #fed7d7;
            color: #742a2a;
        }

        .back {
            margin-top: 20px;
            display: inline-block;
            text-decoration: none;
            color: #405e62;
        }

        .back:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Submit an Inquiry</h2>

        <?php if ($success): ?>
            <div class="message success"><?= $success ?></div>
        <?php elseif ($error): ?>
            <div class="message error"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST">
            <label for="email">Email:</label>
            <input type="email" value="<?= htmlspecialchars($email) ?>" readonly>

            <label for="message">Message:</label>
            <textarea name="message" id="message" required></textarea>

            <button type="submit">Send Inquiry</button>
        </form>

        <a class="back" href="therapist-dashboard.php?id=<?= $therapist_id ?>">← Back to Dashboard</a>
    </div>
</body>
</html>
