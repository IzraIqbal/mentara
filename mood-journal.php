<?php
require 'db.php';
session_start();

if (!isset($_GET['id'])) {
    die("Access denied.");
}

$client_id = $_GET['id'];

// Verify client exists
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ? AND user_type = 'Client'");
$stmt->execute([$client_id]);
$client = $stmt->fetch();
if (!$client) die("Client not found.");

// Handle form submission for mood
if (isset($_POST['submit_mood'])) {
    $mood_level = intval($_POST['mood_level']);
    $mood_description = $_POST['mood_description'] ?? null;
    $log_date = $_POST['log_date'] ?? date('Y-m-d');

    $stmt = $conn->prepare("INSERT INTO mood_logs (client_id, mood_level, mood_description, log_date) VALUES (?, ?, ?, ?)");
    $stmt->execute([$client_id, $mood_level, $mood_description, $log_date]);
    header("Location: mood-journal.php?id=$client_id");
    exit;
}

// Handle form submission for journal
if (isset($_POST['submit_journal'])) {
    $entry_text = $_POST['entry_text'];
    $entry_date = $_POST['entry_date'] ?? date('Y-m-d');

    $stmt = $conn->prepare("INSERT INTO journal_entries (client_id, entry_text, entry_date) VALUES (?, ?, ?)");
    $stmt->execute([$client_id, $entry_text, $entry_date]);
    header("Location: mood-journal.php?id=$client_id");
    exit;
}

// Fetch data
$moods = $conn->prepare("SELECT * FROM mood_logs WHERE client_id = ? ORDER BY log_date DESC");
$moods->execute([$client_id]);
$moods = $moods->fetchAll(PDO::FETCH_ASSOC);

$journals = $conn->prepare("SELECT * FROM journal_entries WHERE client_id = ? ORDER BY entry_date DESC");
$journals->execute([$client_id]);
$journals = $journals->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Mood & Journal - Client Dashboard</title>
  
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&family=Winky+Sans&display=swap" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    * {
      box-sizing: border-box;
    }

    body {
        font-family: "Winky Sans", sans-serif;
      margin: 0;
      padding: 30px;
      background: linear-gradient(to right, #e6f0ff, #fff8f0);
      color: #333;
    }

    h1 {
      text-align: center;
      color: #2c3e50;
      margin-bottom: 30px;
    }

    .flex-container {
      display: flex;
      flex-wrap: wrap;
      gap: 30px;
      justify-content: center;
    }

    .card {
      background: #fff;
      padding: 20px;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
      flex: 1 1 400px;
      max-width: 500px;
    }

    label {
      display: block;
      margin: 12px 0 6px;
      font-weight: 500;
    }

    select, textarea, input[type="date"] {
      width: 100%;
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 8px;
      font-size: 1em;
      background: #fdfdfd;
    }

    textarea {
      height: 100px;
      resize: vertical;
    }

    button {
      margin-top: 15px;
      padding: 10px 20px;
      font-size: 1em;
      border: none;
      border-radius: 8px;
      background-color: #6c9ef8;
      color: white;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }

    button:hover {
      background-color: #4f84f0;
    }

    h2 {
      margin-top: 40px;
      border-bottom: 2px solid #6c9ef8;
      padding-bottom: 5px;
      color: #34495e;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 15px;
      font-size: 0.95em;
    }

    th, td {
      padding: 12px;
      border-bottom: 1px solid #e0e0e0;
      text-align: left;
    }

    th {
      background-color: #6c9ef8;
      color: white;
    }

    canvas {
      display: block;
      max-width: 100%;
      margin: 20px auto 0;
    }

    @media (max-width: 768px) {
      body {
        padding: 20px;
      }

      .flex-container {
        flex-direction: column;
        align-items: stretch;
      }
    }
  </style>
</head>
<body>

<h1>Hello, <?= htmlspecialchars($client['name']) ?> – Track Mood & Journals</h1>

<div class="flex-container">
  <div class="card">
    <h2>Log Your Mood</h2>
    <form method="POST">
      <label for="mood_level">Mood Level (1-5):</label>
      <select name="mood_level" id="mood_level" required>
        <option value="1">1 - Very Low</option>
        <option value="2">2 - Low</option>
        <option value="3" selected>3 - Neutral</option>
        <option value="4">4 - Good</option>
        <option value="5">5 - Very Good</option>
      </select>

      <label for="mood_description">Mood Description:</label>
      <textarea name="mood_description" id="mood_description"></textarea>

      <label for="log_date">Date:</label>
      <input type="date" name="log_date" id="log_date" value="<?= date('Y-m-d') ?>" required>

      <button type="submit" name="submit_mood">Submit Mood</button>
    </form>
  </div>

  <div class="card">
    <h2>Write a Journal Entry</h2>
    <form method="POST">
      <label for="entry_text">Journal Entry:</label>
      <textarea name="entry_text" id="entry_text" required></textarea>

      <label for="entry_date">Date:</label>
      <input type="date" name="entry_date" id="entry_date" value="<?= date('Y-m-d') ?>" required>

      <button type="submit" name="submit_journal">Submit Journal</button>
    </form>
  </div>
</div>

<h2>Your Mood History</h2>
<canvas id="moodChart" height="250"></canvas>
<table>
  <thead>
    <tr><th>Date</th><th>Mood Level</th><th>Description</th></tr>
  </thead>
  <tbody>
    <?php foreach ($moods as $m): ?>
      <tr>
        <td><?= htmlspecialchars($m['log_date']) ?></td>
        <td><?= htmlspecialchars($m['mood_level']) ?></td>
        <td><?= htmlspecialchars($m['mood_description']) ?></td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<h2>Your Journal Entries</h2>
<table>
  <thead>
    <tr><th>Date</th><th>Entry</th></tr>
  </thead>
  <tbody>
    <?php foreach ($journals as $j): ?>
      <tr>
        <td><?= htmlspecialchars($j['entry_date']) ?></td>
        <td><?= nl2br(htmlspecialchars($j['entry_text'])) ?></td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<script>
  const moodData = <?= json_encode(array_reverse(array_map(fn($m) => ['date' => $m['log_date'], 'level' => (int)$m['mood_level']], $moods))) ?>;
  const labels = moodData.map(e => e.date);
  const data = moodData.map(e => e.level);

  const ctx = document.getElementById('moodChart').getContext('2d');
  new Chart(ctx, {
    type: 'line',
    data: {
      labels,
      datasets: [{
        label: 'Mood Level',
        data,
        borderColor: 'rgba(108, 158, 248, 1)',
        backgroundColor: 'rgba(108, 158, 248, 0.2)',
        fill: true,
        tension: 0.3,
        pointRadius: 5,
        pointHoverRadius: 7,
      }]
    },
    options: {
      scales: {
        y: {
          min: 1,
          max: 5,
          ticks: { stepSize: 1 }
        }
      }
    }
  });
</script>

</body>
</html>
