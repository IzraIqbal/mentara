<?php
require 'db.php';
session_start();

if (!isset($_GET['id']) || !isset($_GET['client_id'])) {
    die("Access denied.");
    exit;
}

$therapist_id = $_GET['id'];
$client_id = $_GET['client_id'];

// Verify therapist (approved)
$stmt = $conn->prepare("SELECT * FROM therapists WHERE id = ? AND status = 'approved'");
$stmt->execute([$therapist_id]);
$therapist = $stmt->fetch();
if (!$therapist) {
    die("Therapist not found or not approved.");
    exit;
}

// Verify client
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ? AND user_type = 'Client'");
$stmt->execute([$client_id]);
$client = $stmt->fetch();
if (!$client) {
    die("Client not found.");
    exit;
}

// Dates (optional filtering)
$start_date = !empty($_GET['start_date']) ? $_GET['start_date'] : null;
$end_date = !empty($_GET['end_date']) ? $_GET['end_date'] : null;

// Prepare queries
$paramsMood = [$client_id];
$paramsJournal = [$client_id];
$whereMood = "";
$whereJournal = "";

if ($start_date) {
    $whereMood .= " AND log_date >= ?";
    $whereJournal .= " AND entry_date >= ?";
    $paramsMood[] = $start_date;
    $paramsJournal[] = $start_date;
}

if ($end_date) {
    $whereMood .= " AND log_date <= ?";
    $whereJournal .= " AND entry_date <= ?";
    $paramsMood[] = $end_date;
    $paramsJournal[] = $end_date;
}

// Fetch data
$stmt = $conn->prepare("SELECT * FROM mood_logs WHERE client_id = ? $whereMood ORDER BY log_date DESC");
$stmt->execute($paramsMood);
$moods = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $conn->prepare("SELECT * FROM journal_entries WHERE client_id = ? $whereJournal ORDER BY entry_date DESC");
$stmt->execute($paramsJournal);
$journals = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Client Journals & Mood Logs</title>
     <link
      href="https://fonts.googleapis.com/css2?family=Winky+Sans:ital,wght@0,300..900;1,300..900&display=swap"
      rel="stylesheet"
    />
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: "Winky Sans", sans-serif;
            background: linear-gradient(to right, #f5f7fa, #e0eafc);
            margin: 0;
            padding: 40px;
            color: #333;
        }

        .container {
            max-width: 960px;
            margin: auto;
            background-color: #ffffff;
            border-radius: 12px;
            padding: 40px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }

        h1 {
            text-align: center;
            color: #2c3e50;
            margin-bottom: 30px;
        }

        form {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 20px;
            margin-bottom: 30px;
            background: #f0f4ff;
            padding: 20px;
            border-radius: 10px;
        }

        label {
            font-weight: 500;
        }

        input[type="date"] {
            padding: 8px 12px;
            border-radius: 6px;
            border: 1px solid #ccc;
            background: #fff;
        }

        button {
            padding: 10px 18px;
            background-color: #6c9ef8;
            border: none;
            color: white;
            border-radius: 6px;
            font-size: 1em;
            cursor: pointer;
        }

        button:hover {
            background-color: #4f84f0;
        }

        h2 {
            margin-top: 40px;
            color: #34495e;
            border-bottom: 2px solid #6c9ef8;
            padding-bottom: 6px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            font-size: 0.95em;
        }

        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #6c9ef8;
            color: white;
        }

        tr:hover {
            background-color: #f9f9f9;
        }

        .no-data {
            padding: 15px;
            color: #777;
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Journals & Mood Logs – <?= htmlspecialchars($client['name']) ?></h1>

        <form method="GET">
            <input type="hidden" name="id" value="<?= htmlspecialchars($therapist_id) ?>">
            <input type="hidden" name="client_id" value="<?= htmlspecialchars($client_id) ?>">

            <label for="start_date">Start Date:</label>
            <input type="date" name="start_date" value="<?= htmlspecialchars($start_date) ?>">

            <label for="end_date">End Date:</label>
            <input type="date" name="end_date" value="<?= htmlspecialchars($end_date) ?>">

            <button type="submit">Filter</button>
        </form>

        <h2>Mood Logs</h2>
        <table>
            <thead>
                <tr><th>Date</th><th>Mood Level</th><th>Description</th></tr>
            </thead>
            <tbody>
                <?php if (!empty($moods)): ?>
                    <?php foreach ($moods as $m): ?>
                        <tr>
                            <td><?= htmlspecialchars($m['log_date']) ?></td>
                            <td><?= htmlspecialchars($m['mood_level']) ?></td>
                            <td><?= htmlspecialchars($m['mood_description']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="3" class="no-data">No mood logs found for selected dates.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>

        <h2>Journal Entries</h2>
        <table>
            <thead>
                <tr><th>Date</th><th>Entry</th></tr>
            </thead>
            <tbody>
                <?php if (!empty($journals)): ?>
                    <?php foreach ($journals as $j): ?>
                        <tr>
                            <td><?= htmlspecialchars($j['entry_date']) ?></td>
                            <td><?= nl2br(htmlspecialchars($j['entry_text'])) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="2" class="no-data">No journal entries found for selected dates.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
