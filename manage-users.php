<?php
require 'db.php';
session_start();

if (!isset($_GET['id'])) {
    die("Access denied.");
}

$admin_id = $_GET['id'];

// Fetch all users (clients + therapists)
$stmt = $conn->query("SELECT id, name, email, user_type FROM users ORDER BY user_type");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Users | Admin Panel</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&family=Winky+Sans&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

  <style>
    body {
      background-color: #f5f5dc;
      font-family:  'Winky Sans', sans-serif;
      color: #486569;
      padding: 40px 20px;
    }

    .container {
      max-width: 1000px;
      margin: auto;
      background: #fff;
      padding: 30px 40px;
      border-radius: 16px;
      box-shadow: 0 6px 18px rgba(0, 0, 0, 0.1);
    }

    h2 {
      text-align: center;
      color: #405e62;
      margin-bottom: 30px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
    }

    th, td {
      text-align: left;
      padding: 12px 15px;
      border-bottom: 1px solid #ccc;
    }

    th {
      background-color: #8ab2c0;
      color: white;
    }

    tr:hover {
      background-color: #f2f2f2;
    }

    .action-btn {
      text-decoration: none;
      color: #fff;
      background-color: #405e62;
      padding: 8px 14px;
      border-radius: 8px;
      font-size: 14px;
      transition: background-color 0.3s;
    }

    .action-btn:hover {
      background-color: #202b2cff;
    }

    .back-link {
      display: inline-block;
      margin-top: 20px;
      color: #486569;
      font-weight: bold;
      text-decoration: none;
    }

    .back-link:hover {
      text-decoration: underline;
    }
    /* ---------- Responsive Design ---------- */
    @media (max-width: 1024px) {
      h2 {
        font-size: 24px;
      }

      .container {
        padding: 25px 20px;
      }

      table, th, td {
        font-size: 14px;
      }
    }

    @media (max-width: 768px) {
      h2 {
        font-size: 22px;
      }

      .container {
        padding: 20px 15px;
      }

      th, td {
        padding: 10px;
        font-size: 13px;
      }

      .action-btn {
        font-size: 13px;
        padding: 6px 10px;
      }
    }

    @media (max-width: 480px) {
      h2 {
        font-size: 20px;
      }

      .container {
        padding: 15px 10px;
      }

      table {
        min-width: 100%;
      }

      th, td {
        padding: 8px;
        font-size: 12px;
      }

      .action-btn {
        font-size: 12px;
        padding: 5px 8px;
      }

      .back-link {
        font-size: 13px;
      }
    }
  </style>
</head>
<body>
  <div class="container">
    <h2>Manage Users</h2>

    <?php if (count($users) > 0): ?>
      <table>
        <thead>
          <tr>
            <th>User ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Role</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($users as $user): ?>
            <tr>
              <td><?= htmlspecialchars($user['id']) ?></td>
              <td><?= htmlspecialchars($user['name']) ?></td>
              <td><?= htmlspecialchars($user['email']) ?></td>
              <td><?= htmlspecialchars($user['user_type']) ?></td>
              <td>
                <a class="action-btn" href="edit-user.php?id=<?= $user['id'] ?>&admin=<?= $admin_id ?>"><i class="fas fa-edit"></i> Edit</a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php else: ?>
      <p>No users found.</p>
    <?php endif; ?>

    <a class="back-link" href="admin-dashboard.php?id=<?= $admin_id ?>"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
  </div>
</body>
</html>
