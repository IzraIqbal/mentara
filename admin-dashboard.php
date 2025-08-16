<?php
require 'db.php';
session_start();

if (!isset($_GET['id'])) {
    die("Access denied.");
}

$id = $_GET['id'];
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ? AND user_type = 'Admin'");
$stmt->execute([$id]);
$admin = $stmt->fetch();

if (!$admin) {
    die("Admin not found.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Admin Dashboard | Mentara</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&family=Winky+Sans&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family:  'Winky Sans', sans-serif;
        }

        body {
            background-color: #8ab2c0;
            color: #405e62;
            padding: 60px 20px;
        }

        .dashboard-container {
            max-width: 900px;
            margin: auto;
            background: beige;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.1);
        }

        .dashboard-image {
            text-align: center;
            margin-bottom: 30px;
        }

        .dashboard-image img {
            max-width: 200px;
            height: auto;
            border-radius: 20px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        h1 {
            font-size: 2em;
            color: #405e62;
            text-align: center;
            margin-bottom: 10px;
        }

        p.subheading {
            text-align: center;
            margin-bottom: 30px;
            color: #6c757d;
        }

        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }

        .dashboard-grid a {
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            background-color: #405e62;
            color: #ffffff;
            padding: 18px;
            border-radius: 12px;
            font-weight: 600;
            text-align: center;
            transition: background 0.3s ease;
        }

        .dashboard-grid a i {
            margin-right: 10px;
        }

        .dashboard-grid a:hover {
            background-color: #34484aff;
        }

        .logout-link {
            display: block;
            margin-top: 40px;
            text-align: center;
            color: #8ab2c0;
            font-weight: bold;
            text-decoration: none;
        }

        .logout-link:hover {
            text-decoration: underline;
        }
        @media (max-width: 1024px) {
            body {
                padding: 40px 16px;
            }

            .dashboard-container {
                padding: 30px;
            }

            h1 {
                font-size: 1.8em;
            }

            .dashboard-grid a {
                font-size: 15px;
                padding: 16px;
            }
        }

        @media (max-width: 768px) {
            .dashboard-container {
                padding: 25px;
            }

            .dashboard-grid {
                grid-template-columns: 1fr;
            }

            h1 {
                font-size: 1.6em;
            }

            .dashboard-grid a {
                font-size: 14px;
                padding: 14px;
            }
        }

        @media (max-width: 480px) {
            body {
                padding: 20px 10px;
            }

            .dashboard-container {
                padding: 20px;
            }

            h1 {
                font-size: 1.4em;
            }

            .dashboard-grid a {
                font-size: 13px;
                padding: 12px;
            }

            .dashboard-image img {
                max-width: 150px;
            }
        }
    </style>
</head>
<body>

    <div class="dashboard-container">
        <!-- 🌟 Admin Banner Image -->
        <div class="dashboard-image">
            <img src="images/therapist_image_login.png" alt="Admin Dashboard">
        </div>

        <h1>Welcome, <?= htmlspecialchars($admin['name']) ?></h1>
        <p class="subheading">(Admin Panel)</p>

        <div class="dashboard-grid">
            <a href="approve-therapist.php?id=<?= $admin['id'] ?>"><i class="fas fa-user-check"></i> Approve Therapists</a>
            <a href="monitor-reports.php?id=<?= $admin['id'] ?>"><i class="fas fa-chart-line"></i> Monitor Reports</a>
            <a href="view-inquiries.php?id=<?= $admin['id'] ?>"><i class="fas fa-envelope-open-text"></i> View Inquiries</a>
            <a href="admin-post-announcement.php?id=<?= $admin['id'] ?>"><i class="fas fa-bullhorn"></i> Post Announcements</a>
              <a href="manage-users.php?id=<?= $admin['id'] ?>"><i class="fas fa-users-cog"></i> Manage Users</a>
               <a href="admin-graphs.php?id=<?=$admin['id']?>"><i class="fas fa-users-cog"></i> View Graphs</a>
        </div>

        <a class="logout-link" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>

</body>
</html>
