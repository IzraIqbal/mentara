<?php
require 'db.php';
session_start();

// Validate therapist ID
if (!isset($_GET['id'])) {
    die("Access denied.");
}

$therapist_id = $_GET['id'];

// Fetch therapist details (optional, just for display)
$stmt = $conn->prepare("SELECT * FROM therapists WHERE id = ?");
$stmt->execute([$therapist_id]);
$therapist = $stmt->fetch();

if (!$therapist) {
    die("Invalid therapist ID.");
}

// Fetch clients who booked sessions with this therapist
$query = "
    SELECT DISTINCT u.id as user_id, c.name, c.email, c.phone, c.gender, c.age
    FROM sessions s
    JOIN users u ON s.client_id = u.id
    JOIN client c ON c.user_id = u.id
    WHERE s.therapist_id = ?
";

$stmt = $conn->prepare($query);
$stmt->execute([$therapist_id]);
$clients = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Clients List</title>
    <link href="https://fonts.googleapis.com/css2?family=Winky+Sans&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Winky Sans', sans-serif;
            background-color: beige;
            margin: 0;
            padding: 30px;
        }

        .container {
            max-width: 960px;
            margin: auto;
            background: #fdfdfd;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 6px 20px rgba(72, 101, 105, 0.2);
            border: 1px solid #8ab2c0;
        }

        h2 {
            color: #486569;
            border-bottom: 2px solid #8ab2c0;
            padding-bottom: 10px;
            margin-bottom: 30px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background-color: #fff;
            border-radius: 8px;
            overflow: hidden;
        }

        th, td {
            padding: 14px 16px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #8ab2c0;
            color: #fff;
            font-weight: 600;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        .no-data {
            padding: 20px;
            background-color: #fdf6e3;
            color: #555;
            border: 1px solid #ccc;
            border-radius: 8px;
        }

        .back-link {
            display: inline-block;
            margin-top: 25px;
            color: #486569;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.2s ease;
        }

        .back-link:hover {
            color: #2f4f50;
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Clients Who Booked Sessions with Dr. <?= htmlspecialchars($therapist['name']) ?></h2>

        <?php if (count($clients) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Client Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Gender</th>
                        <th>Age</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($clients as $client): ?>
                        <tr>
                            <td><?= htmlspecialchars($client['name']) ?></td>
                            <td><?= htmlspecialchars($client['email']) ?></td>
                            <td><?= htmlspecialchars($client['phone']) ?></td>
                            <td><?= htmlspecialchars($client['gender']) ?></td>
                            <td><?= htmlspecialchars($client['age']) ?></td>
                            <td> <a href="view-journals.php?id=<?= $therapist_id ?>&client_id=<?= $client['user_id'] ?>">View Journals</a>

</td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="no-data">No clients have booked sessions with you yet.</div>
        <?php endif; ?>

        <a class="back-link" href="therapist-dashboard.php?id=<?= $therapist['id'] ?>">← Back to Dashboard</a>
    </div>
</body>
</html>
