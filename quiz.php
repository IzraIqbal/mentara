<?php
require 'db.php';
session_start();

if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'Client') {
    die("Unauthorized access.");
}

$client_id = $_SESSION['id'];
$result = '';
$recommended_therapist = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $score = array_sum($_POST['answers']);

    if ($score <= 7) {
        $result = "You seem to be managing well. Keep maintaining a healthy balance.";
        $mental_issue = "Balanced";
    } elseif ($score <= 12) {
        $result = "You may be experiencing signs of stress. Consider relaxation or time management therapy.";
        $mental_issue = "Stress Management";
    } elseif ($score <= 17) {
        $result = "You may have symptoms of anxiety. Therapy focused on anxiety relief may help.";
        $mental_issue = "Anxiety";
    } else {
        $result = "Your answers suggest possible signs of depression. We recommend speaking to a professional.";
        $mental_issue = "Depression";
    }

    // Find a therapist with matching specialty
    $stmt = $conn->prepare("SELECT * FROM therapists WHERE speciality = ? AND status = 'approved'");

    $stmt->execute(["%$mental_issue%"]);
    $recommended_therapist = $stmt->fetch();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Mentara Mental Health Quiz</title>
     <link href="https://fonts.googleapis.com/css2?family=Winky+Sans&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: "Winky Sans", sans-serif;
            background-color: #f5f5dc;
            padding: 40px;
            color: #486569;
        }

        h1, h2 {
            text-align: center;
        }

        form {
            max-width: 700px;
            margin: auto;
            background-color: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }

        .question {
            margin-bottom: 20px;
        }

        .question p {
            font-weight: bold;
            margin-bottom: 10px;
        }

        label {
            display: block;
            margin: 4px 0;
        }

        .btn {
            padding: 10px 20px;
            background-color: #486569;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            margin-top: 20px;
        }

        .result-box {
            background-color: #e3f2f4;
            padding: 25px;
            margin-top: 30px;
            border-radius: 10px;
            text-align: center;
        }

        .therapist-box {
            background-color: #fff9ec;
            padding: 20px;
            margin-top: 20px;
            border-radius: 10px;
            border: 1px solid #e3e3e3;
        }
    </style>
</head>
<body>

<h1> Mental Wellness Quiz</h1>
<p style="text-align:center;">Answer honestly to receive guidance and therapist suggestions.</p>

<?php if (!$result): ?>
<form method="POST">
    <?php
    $questions = [
        "1. I often feel overwhelmed or under pressure.",
        "2. I have trouble sleeping or staying asleep.",
        "3. I find it hard to concentrate on tasks.",
        "4. I feel anxious or worried about everyday things.",
        "5. I lack motivation or feel hopeless frequently."
    ];

    foreach ($questions as $i => $q):
    ?>
        <div class="question">
            <p><?= $q ?></p>
            <label><input type="radio" name="answers[<?= $i ?>]" value="0" required> Never</label>
            <label><input type="radio" name="answers[<?= $i ?>]" value="1"> Rarely</label>
            <label><input type="radio" name="answers[<?= $i ?>]" value="2"> Sometimes</label>
            <label><input type="radio" name="answers[<?= $i ?>]" value="3"> Often</label>
            <label><input type="radio" name="answers[<?= $i ?>]" value="4"> Always</label>
        </div>
    <?php endforeach; ?>

    <button class="btn" type="submit">Submit Quiz</button>
</form>

<?php else: ?>
    <div class="result-box">
        <h2>📝 Your Result</h2>
        <p><?= htmlspecialchars($result) ?></p>

        <?php if ($recommended_therapist): ?>
            <div class="therapist-box">
                <h3>🎯 Recommended Therapist</h3>
                <p><strong>Name:</strong> <?= htmlspecialchars($recommended_therapist['name']) ?></p>
                <p><strong>Email:</strong> <?= htmlspecialchars($recommended_therapist['email']) ?></p>
                <p><strong>Specialty:</strong> <?= htmlspecialchars($recommended_therapist['speciality']) ?></p>
                <a href="available-therapists.php" style="color: #486569; font-weight: bold;">View All Therapists</a>
            </div>
        <?php else: ?>
            <p><em>No specialist available for this category right now.</em></p>
        <?php endif; ?>
    </div>
<?php endif; ?>

</body>
</html>
