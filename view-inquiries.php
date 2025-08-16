<?php
require 'db.php';
session_start(); // Optional if using session for admin access

$stmt = $conn->query("SELECT * FROM inquiries ORDER BY submitted_at DESC");
$inquiries = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>All Inquiries</title>
    <link
      href="https://fonts.googleapis.com/css2?family=Winky+Sans:ital,wght@0,300..900;1,300..900&display=swap"
      rel="stylesheet"
    />
    <style>
        body {
            font-family: "Winky Sans", sans-serif;
            background-color: #f0f4f5;
            margin: 0;
            padding: 0;
        }

        h2 {
            text-align: center;
            padding: 30px 0 10px;
            color: #405e62;
            font-size: 28px;
        }

        .table-container {
            width: 90%;
            margin: 0 auto 40px;
            overflow-x: auto;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            background-color: white;
            border-radius: 12px;
            padding: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th, td {
            padding: 14px 16px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #405e62;
            color: #ffffff;
            font-weight: bold;
        }

        td {
            color: #333;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #e0f0f5;
        }

       /* Media Queries for Responsiveness */
        @media (max-width: 1024px) {
            h2 {
                font-size: 24px;
            }

            .table-container {
                width: 95%;
                padding: 15px;
            }

            table, th, td {
                font-size: 14px;
            }
        }

        @media (max-width: 768px) {
            h2 {
                font-size: 22px;
            }

            .table-container {
                padding: 10px;
            }

            th, td {
                padding: 10px;
                font-size: 13px;
            }
        }

        @media (max-width: 480px) {
            h2 {
                font-size: 20px;
            }

            .table-container {
                padding: 8px;
            }

            table {
                min-width: 100%;
            }

            th, td {
                padding: 8px;
                font-size: 12px;
            }

            td:nth-child(3), td:nth-child(4) {
                word-break: break-word;
            }
        }
    </style>
</head>
<body>
    <h2>All Inquiries</h2>
    <div class="table-container">
        <table>
            <tr>
                <th>ID</th>
                <th>Email</th>
                <th>Role</th>
                <th>Message</th>
                <th>Date</th>
            </tr>
            <?php foreach ($inquiries as $inq): ?>
            <tr>
                <td><?= $inq['inquiry_id'] ?></td>
                <td><?= htmlspecialchars($inq['email']) ?></td>
                <td><?= $inq['role'] ?></td>
                <td><?= nl2br(htmlspecialchars($inq['message'])) ?></td>
                <td><?= $inq['submitted_at'] ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
</body>
</html>
