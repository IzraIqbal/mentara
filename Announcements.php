<?php
$file = 'announcements.json';
$announcements = file_exists($file) ? json_decode(file_get_contents($file), true) : [];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Announcements | Mentara</title>
    <link href="https://fonts.googleapis.com/css2?family=Winky+Sans&display=swap" rel="stylesheet">
    <style>
        body {   font-family: "Winky Sans", sans-serif;; padding: 40px; background: #f5f5dc; color: #486569; }
        .announcement { background: #fff; padding: 20px; margin-bottom: 20px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); }
        h2 { margin-bottom: 10px; }
        .meta { font-size: 0.9em; color: #666; margin-bottom: 8px; }
    </style>
</head>
<body>
    <h1> Latest Announcements</h1>

    <?php if (!empty($announcements)): ?>
        <?php foreach ($announcements as $a): ?>
            <div class="announcement">
                <h2><?= htmlspecialchars($a['title']) ?></h2>
                <div class="meta">Posted by <?= htmlspecialchars($a['author']) ?> on <?= htmlspecialchars($a['date']) ?></div>
                <p><?= nl2br(htmlspecialchars($a['message'])) ?></p>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No announcements available at the moment.</p>
    <?php endif; ?>
</body>
</html>
