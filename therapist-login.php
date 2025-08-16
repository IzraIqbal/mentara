<?php
require 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Only fetch therapists who are approved
    $stmt = $conn->prepare("SELECT * FROM therapists WHERE email = ? AND status = 'Approved'");
    $stmt->execute([$email]);
    $therapist = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($therapist && password_verify($password, $therapist['password'])) {
        header("Location: therapist-dashboard.php?id=" . $therapist['id']);
        exit();
    } else {
        echo "❌ Invalid credentials or your application is still pending approval.";
    }
}
?>


<!DOCTYPE html>
    <html lang="en">
      <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Therapist Login | Mentara</title>
        <link rel="stylesheet" href="therapist.css" />
            <link
      href="https://fonts.googleapis.com/css2?family=Winky+Sans:ital,wght@0,300..900;1,300..900&display=swap"
      rel="stylesheet"
    />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
    />
      </head>
      <body>
        <div class="therapist-login-wrapper">
    <div class="therapist-image-section">
      <img src="images/therapist_image_login.png" alt="Therapist Illustration" />
    </div>
        <div class="therapist-login-container">
          <h2>Therapist Login</h2>
          <form action="therapist-login.php" method="POST">
            <div class="therapist-form-group">
              <label for="email">Email Address</label>
              <input type="email" id="email" name="email" required />
            </div>
            <div class="therapist-form-group">
              <label for="password">Password</label>
              <input type="password" id="password" name="password" required />
            </div>
            <button type="submit">Login</button>
            <div class="therapist-extra-links">
              <a href="#">Forgot Password?</a>
              <span>|</span>
              <a href="therapist-register.php">New Therapist? Register</a>
            </div>
          </form>
        </div>
      </body>
    </html>
  </body>
</html>
