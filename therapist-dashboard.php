<?php
require 'db.php';
session_start();

// Check if therapist ID is provided
if (!isset($_GET['id'])) {
    die("Access denied.");
}

$id = $_GET['id'];

// Fetch therapist data
$stmt = $conn->prepare("SELECT * FROM therapists WHERE id = ? AND status = 'approved'");
$stmt->execute([$id]);
$therapist = $stmt->fetch();

if (!$therapist) {
    die("Therapist not found or not approved yet.");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Therapist Dashboard</title>
     <link
      href="https://fonts.googleapis.com/css2?family=Winky+Sans:ital,wght@0,300..900;1,300..900&display=swap"
      rel="stylesheet"
    />
    <style>
        body {
            font-family: "Winky Sans", sans-serif;
            background: #f3f7f9;
            margin: 0;
            padding: 0;
        }

        .dashboard-container {
            max-width: 900px;
            margin: 50px auto;
            background: white;
            padding: 30px 40px;
            border-radius: 12px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.1);
        }

        h1 {
            color: #405e62;
        }

        .info {
            background: #e6fffa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 6px solid #8ab2c0;
        }

        ul {
            list-style-type: none;
            padding: 0;
        }

        ul li {
            padding: 5px 0;
        }

        .dashboard-links a {
            display: block;
            text-decoration: none;
            background-color: #8ab2c0;
            color: white;
            padding: 12px 18px;
            margin: 10px 0;
            border-radius: 8px;
            text-align: center;
            transition: background 0.3s;
        }

        .dashboard-links a:hover {
            background-color: #272e30ff;
        }

        .logout {
            margin-top: 20px;
            display: inline-block;
            color: #e53e3e;
            text-decoration: none;
        }

        .logout:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <h1>Welcome, Dr. <?= htmlspecialchars($therapist['name']) ?></h1>

        <div class="info">
            <ul>
                <li><strong>Email:</strong> <?= htmlspecialchars($therapist['email']) ?></li>
                <li><strong>Speciality:</strong> <?= htmlspecialchars($therapist['speciality']) ?></li>
                <li><strong>Availability:</strong> <?= htmlspecialchars($therapist['availability']) ?></li>
            </ul>
        </div>

        <div class="dashboard-links">
            <a href="clients-list.php?id=<?= $therapist['id'] ?>">Clients List</a>
            <a href="manage-availability.php?id=<?= $therapist['id'] ?>"> Manage Availability & Bookings</a>
            <a href="session-notes.php?id=<?= $therapist['id'] ?>"> Write Session Notes with Tags</a>
           
            <a href="upload-resources.php?id=<?= $therapist['id'] ?>">Upload Resources & Send Reminders</a>
            <a href="submit-inquiry.php?id=<?= $therapist['id'] ?>"> Submit Inquiry</a>
        </div>

        <a class="logout" href="logout.php"> Logout</a>
    </div>
</body>
</html>
