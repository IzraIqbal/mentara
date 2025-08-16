<?php
require 'db.php';
session_start();

if (!isset($_GET['id'])) {
    die("Access denied.");
}

$admin_id = $_GET['id'];
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ? AND user_type = 'Admin'");
$stmt->execute([$admin_id]);
$admin = $stmt->fetch();

if (!$admin) {
    die("Admin not found.");
}

// Get stats (except bookings, since table doesn't exist)
$totalUsers = $conn->query("SELECT COUNT(*) FROM users")->fetchColumn();
$totalClients = $conn->query("SELECT COUNT(*) FROM client")->fetchColumn();
$totalTherapists = $conn->query("SELECT COUNT(*) FROM therapists")->fetchColumn();
$totalInquiries = $conn->query("SELECT COUNT(*) FROM inquiries")->fetchColumn();
$totalResources = $conn->query("SELECT COUNT(*) FROM resources")->fetchColumn();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Monitor Reports | Admin Panel</title>
  <link
      href="https://fonts.googleapis.com/css2?family=Winky+Sans:ital,wght@0,300..900;1,300..900&display=swap"
      rel="stylesheet"
    />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <style>
    body {
       font-family: "Winky Sans", sans-serif;
      background-color: #8ab2c0;
      color: #405e62;
      padding: 50px 20px;
    }

    .container {
      max-width: 900px;
      margin: auto;
      background: #fffaf0;
      padding: 40px;
      border-radius: 20px;
      box-shadow: 0 6px 18px rgba(0, 0, 0, 0.1);
    }

    h2 {
      text-align: center;
      font-size: 28px;
      color: #405e62;
      margin-bottom: 30px;
    }

    .report-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
      gap: 20px;
    }

    .report-card {
      background-color: #405e62;
      color: #ffffff;
      border-radius: 12px;
      padding: 20px;
      text-align: center;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }

    .report-card i {
      font-size: 28px;
      margin-bottom: 10px;
      color: #8ab2c0;
    }

    .report-card h3 {
      margin: 10px 0;
      font-size: 20px;
    }

    .report-card p {
      font-size: 16px;
    }

    .back-link {
      display: block;
      margin-top: 30px;
      text-align: center;
      font-weight: bold;
      text-decoration: none;
      color: #405e62;
    }

    .back-link:hover {
      text-decoration: underline;
    }

    @media (max-width: 480px) {
      h2 {
        font-size: 22px;
      }

      .report-card h3 {
        font-size: 18px;
      }

      .report-card p {
        font-size: 14px;
      }
    }
  </style>
</head>
<body>
  <div class="container">
    <h2>📊 System Monitoring Report</h2>

    <div class="report-grid">
      <div class="report-card">
        <i class="fas fa-users"></i>
        <h3><?= $totalUsers ?></h3>
        <p>Total Registered Users</p>
      </div>

      <div class="report-card">
        <i class="fas fa-user"></i>
        <h3><?= $totalClients ?></h3>
        <p>Clients</p>
      </div>

      <div class="report-card">
        <i class="fas fa-user-md"></i>
        <h3><?= $totalTherapists ?></h3>
        <p>Therapists</p>
      </div>

      <div class="report-card">
        <i class="fas fa-envelope"></i>
        <h3><?= $totalInquiries ?></h3>
        <p>Inquiries Received</p>
      </div>

      <div class="report-card">
        <i class="fas fa-file-alt"></i>
        <h3><?= $totalResources ?></h3>
        <p>Resources Uploaded</p>
      </div> 

      <div class="report-card">
        <a href="admin-graphs.php?id=<?= $admin['id'] ?>" style="color: white; text-decoration: none;">
        <i class="fas fa-chart-pie"></i>
        <h3>View</h3>
        <p>Interactive Graphs</p>
        </a>
       </div>
      

      
    </div>

    <a class="back-link" href="admin-dashboard.php?id=<?= $admin['id'] ?>"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
  </div>
</body>
</html>
