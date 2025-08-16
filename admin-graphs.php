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

// Get dynamic counts
$totalClients = $conn->query("SELECT COUNT(*) FROM users WHERE user_type = 'Client'")->fetchColumn();
$totalTherapists = $conn->query("SELECT COUNT(*) FROM users WHERE user_type = 'Therapist'")->fetchColumn();

// Dummy monthly inquiries (replace with SQL aggregation if needed)
$inquiriesMonthly = [
  "Jan", "Feb", "Mar", "Apr", "May", "Jun"
];
$inquiriesCounts = [8, 14, 10, 6, 12, 9];

// Dummy therapy type resources
$therapyLabels = ["CBT", "Mindfulness", "ACT", "DBT"];
$therapyData = [5, 3, 4, 2];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Admin Graphs | Mentara</title>
 <link
      href="https://fonts.googleapis.com/css2?family=Winky+Sans:ital,wght@0,300..900;1,300..900&display=swap"
      rel="stylesheet"
    />
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    body {
        font-family: "Winky Sans", sans-serif;
      background-color: #8ab2c0;
      margin: 0;
      padding: 40px 20px;
      color: #405e62;
    }

    .container {
      max-width: 1000px;
      margin: auto;
      background: #fffaf0;
      padding: 40px;
      border-radius: 20px;
      box-shadow: 0 8px 18px rgba(0, 0, 0, 0.1);
    }

    h2 {
      text-align: center;
      margin-bottom: 40px;
      color: #405e62;
    }

    .chart-container {
      margin-bottom: 50px;
    }

    canvas {
      width: 100% !important;
      max-width: 800px;
      height: 360px !important;
      margin: 0 auto;
      display: block;
    }

    .back-link {
      display: block;
      margin-top: 20px;
      text-align: center;
      font-weight: bold;
      text-decoration: none;
      color: #405e62;
    }

    .back-link:hover {
      text-decoration: underline;
    }

    @media (max-width: 600px) {
      body {
        padding: 20px;
      }

      .container {
        padding: 25px;
      }

      h2 {
        font-size: 22px;
      }
    }
  </style>
</head>
<body>
  <div class="container">
    <h2>📊 Admin Graphs Overview</h2>

    <div class="chart-container">
      <h3 style="text-align:center;">User Role Distribution</h3>
      <canvas id="userChart"></canvas>
    </div>

    <div class="chart-container">
      <h3 style="text-align:center;">Monthly Inquiries (Sample)</h3>
      <canvas id="inquiriesChart"></canvas>
    </div>

    <div class="chart-container">
      <h3 style="text-align:center;">Resources by Therapy Type (Sample)</h3>
      <canvas id="resourcesChart"></canvas>
    </div>

    <a class="back-link" href="monitor-reports.php?id=<?= $admin['id'] ?>">&larr; Back to Report</a>
  </div>

  <script>
    // Pie Chart for user roles
    new Chart(document.getElementById('userChart'), {
      type: 'pie',
      data: {
        labels: ['Clients', 'Therapists'],
        datasets: [{
          label: 'User Roles',
          data: [<?= $totalClients ?>, <?= $totalTherapists ?>],
          backgroundColor: ['#8ab2c0', '#405e62']
        }]
      }
    });

    // Bar chart for inquiries
    new Chart(document.getElementById('inquiriesChart'), {
      type: 'bar',
      data: {
        labels: <?= json_encode($inquiriesMonthly) ?>,
        datasets: [{
          label: 'Inquiries per Month',
          data: <?= json_encode($inquiriesCounts) ?>,
          backgroundColor: '#405e62'
        }]
      },
      options: {
        scales: {
          y: { beginAtZero: true }
        }
      }
    });

    // Doughnut chart for resources
    new Chart(document.getElementById('resourcesChart'), {
      type: 'doughnut',
      data: {
        labels: <?= json_encode($therapyLabels) ?>,
        datasets: [{
          label: 'Therapy Resources',
          data: <?= json_encode($therapyData) ?>,
          backgroundColor: ['#8ab2c0', '#405e62', '#6c757d', '#adb5bd']
        }]
      }
    });
  </script>
</body>
</html>
