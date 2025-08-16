<?php
require 'db.php';
session_start();

if (!isset($_GET['id'])) {
    die("Access denied.");
}

$admin_id = $_GET['id'];

// Handle approval
if (isset($_GET['approve'])) {
    $therapist_id = $_GET['approve'];
    $stmt = $conn->prepare("UPDATE therapists SET status = 'Approved' WHERE id = ?");
    $stmt->execute([$therapist_id]);
    header("Location: approve-therapist.php?id=$admin_id");
    exit;
}

// Fetch pending therapists
$stmt = $conn->prepare("SELECT * FROM therapists WHERE status = 'Pending'");
$stmt->execute();
$pendingTherapists = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Approve Therapists</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f4f9fb;
            margin: 0;
            padding: 20px;
            color: #333;
        }

        h1 {
            color: #405e62;
            text-align: center;
            margin-bottom: 30px;
        }

        table {
            width: 90%;
            margin: 0 auto;
            border-collapse: collapse;
            background-color: #ffffff;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        th, td {
            padding: 14px 18px;
            border-bottom: 1px solid #ddd;
            text-align: center;
        }

        th {
            background-color: #405e62;
            color: white;
        }

        tr:hover {
            background-color: #f1f7fa;
        }

        a {
            color: #405e62;
            text-decoration: none;
            font-weight: bold;
        }

        a:hover {
            color: #8ab2c0;
        }

        .back-link {
            display: block;
            width: fit-content;
            margin: 30px auto 0;
            padding: 10px 20px;
            background-color: #8ab2c0;
            color: white;
            border-radius: 6px;
            text-align: center;
        }

        .back-link:hover {
            background-color: #729aa9;
        }

        .view-link {
            background-color: #8ab2c0;
            color: white;
            padding: 6px 12px;
            border-radius: 4px;
            display: inline-block;
        }

        .approve-link {
            background-color: #405e62;
            color: white;
            padding: 6px 12px;
            border-radius: 4px;
            display: inline-block;
        }

        .view-link:hover,
        .approve-link:hover {
            opacity: 0.85;
        }
        /* Responsive Design */
        @media (max-width: 768px) {
            h1 {
                font-size: 24px;
                padding: 10px;
            }

            table {
                width: 100%;
                font-size: 14px;
            }

            th, td {
                padding: 12px 10px;
                font-size: 14px;
            }

            .back-link {
                font-size: 14px;
                padding: 8px 16px;
            }
        }

        @media (max-width: 480px) {
            h1 {
                font-size: 20px;
            }

            table, th, td {
                font-size: 12px;
                padding: 10px 8px;
            }

            .view-link,
            .approve-link {
                font-size: 12px;
                padding: 5px 10px;
            }

            .back-link {
                font-size: 13px;
                padding: 8px 12px;
                margin-top: 20px;
            }
        }
    </style>
</head>
<body>
    <h1>Pending Therapist Applications</h1>

    <?php if (count($pendingTherapists) > 0): ?>
        <table>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Speciality</th>
                <th>Certification</th>
                <th>Action</th>
            </tr>
            <?php foreach ($pendingTherapists as $therapist): ?>
                <tr>
                    <td><?= htmlspecialchars($therapist['name']) ?></td>
                    <td><?= htmlspecialchars($therapist['email']) ?></td>
                    <td><?= htmlspecialchars($therapist['speciality']) ?></td>
                    <td><a class="view-link" href="uploads/<?= urlencode($therapist['certification']) ?>" target="_blank">View</a></td>
                    <td><a class="approve-link" href="?id=<?= $admin_id ?>&approve=<?= $therapist['id'] ?>" onclick="return confirm('Approve this therapist?')">Approve</a></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p style="text-align:center; font-size: 18px;">No pending applications.</p>
    <?php endif; ?>

    <a class="back-link" href="admin-dashboard.php?id=<?= $admin_id ?>">← Back to Dashboard</a>
</body>
</html>
