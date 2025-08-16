<?php
require 'db.php';  // Your PDO connection

try {
    $stmt = $conn->prepare("SELECT * FROM therapists WHERE status = 'approved'");
    $stmt->execute();
    $therapists = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Database error: " . htmlspecialchars($e->getMessage());
    exit;
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Available Therapists</title>
 <link
      href="https://fonts.googleapis.com/css2?family=Winky+Sans:ital,wght@0,300..900;1,300..900&display=swap"
      rel="stylesheet"
    />
<style>
  body {
    font-family: "Winky Sans", sans-serif;
    background: beige;
    margin: 0; padding: 20px;
  }
  h1 {
    text-align: center;
    margin-bottom: 30px;
    color:#405e62;
  }
  .therapist-container {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    justify-content: center;
  }
  .therapist-card {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    width: 300px;
    padding: 20px;
    box-sizing: border-box;
    display: flex;
    background:  #8ab2c0;
    flex-direction: column;
    justify-content: space-between;
    /* Temporary border to see cards, remove after confirming */
    /* border: 2px solid red; */
  }
  .therapist-card img {
    width: 100%;
    height: 180px;
    object-fit: cover;
    border-radius: 6px;
    margin-bottom: 15px;
    background: #eee;
  }
  .therapist-info {
    flex-grow: 1;
  }
  .therapist-info h2 {
    margin: 0 0 8px;
    font-size: 1.25em;
    color: beige;
  }
  .therapist-info p {
    margin: 5px 0;
    font-size: 0.9em;
    color: #405e62;
  }
  .btn-request {
    margin-top: 15px;
     background:  #405e62;
    border: none;
    color: white;
    padding: 12px;
    font-size: 1em;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s ease;
    width: 100%;
  }
  .btn-request:hover {
    background-color:rgb(58, 73, 75);
  }
  form {
    margin: 0;
  }
</style>
</head>
<body>

<h1>Available Therapists</h1>

<div class="therapist-container">
  <?php foreach ($therapists as $therapist): ?>
    <div class="therapist-card">
      <?php
// Hardcoded image mapping by therapist ID or name
$imageMap = [
     'Maya Thomas' => 'therapist10.jpg',
    'Dr. Ayesha Malik' => 'therapist2.jpg',
    'youssef Aymn' => 'therapist3.jpg',
    'Dr. Kevin Rodrigo' => 'therapist4.jpg',
    'Dr. Ruqaiyah Saleem' => 'therapist5.jpg',
    'Dr. Thilan Perera' => 'therapist6.jpg',
    'Dr. Hira Zaman' => 'therapist7.jpg',
    'Dr. Kaushalya Weerasinghe' => 'therapist8.jpg',
    
];

// Default image if not found
$imageFile = isset($imageMap[$therapist['name']]) ? $imageMap[$therapist['name']] : 'default.jpg';
?>
<img src="images/<?= htmlspecialchars($imageFile) ?>" alt="Photo of <?= htmlspecialchars($therapist['name']) ?>" />


      <div class="therapist-info">
        <h2><?= htmlspecialchars($therapist['name']) ?></h2>
        <p><strong>Email:</strong> <?= htmlspecialchars($therapist['email']) ?></p>
        <p><strong>Phone:</strong> <?= htmlspecialchars($therapist['phone']) ?></p>
        <p><strong>Gender:</strong> <?= htmlspecialchars($therapist['gender']) ?></p>
        <p><strong>Availability:</strong> <?= htmlspecialchars($therapist['availability']) ?></p>
        <p><strong>Speciality:</strong> <?= htmlspecialchars($therapist['speciality']) ?></p>
      </div>
     
    </div>
  <?php endforeach; ?>
</div>



</body>
</html>
