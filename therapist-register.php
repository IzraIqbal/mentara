<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $phone = $_POST['phone'];
    $gender = $_POST['gender'];
    $availability = $_POST['availability'];
    $speciality = $_POST['speciality'];

    $certification = $_FILES['certification']['name'];
    $target = "uploads/" . basename($certification);
    move_uploaded_file($_FILES['certification']['tmp_name'], $target);

    $stmt = $conn->prepare("INSERT INTO therapists (name, email, password, phone, gender, availability, speciality, certification) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$name, $email, $password, $phone, $gender, $availability, $speciality, $certification]);

    echo "Application submitted. Wait for admin approval.";
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Therapist Registration | Mentara</title>
    <link rel="stylesheet" href="therapist.css" />
    <link
      href="https://fonts.googleapis.com/css2?family=Winky+Sans&display=swap"
      rel="stylesheet"
    />
  </head>
  <body class="admin-client-body">
    <div class="register-body">
      <div class="therapist-register-wrapper">
        <div class="therapist-register-container">
          <div class="therapist-logo-container">
            <img
              src="images/Green Minimalist Nature Beauty Care Logo (3).png"
            />
          </div>
          <h2>Therapist Registration</h2>
          <form
            action="therapist-register.php"
            method="POST"
            enctype="multipart/form-data"
          >
            <div class="form-group">
              <label for="name">Full Name</label>
              <input type="text" id="name" name="name" required />
            </div>
            <div class="form-group">
              <label for="email">Email Address</label>
              <input type="email" id="email" name="email" required />
            </div>
            <div class="form-group">
              <label for="password">Create Password</label>
              <input type="password" id="password" name="password" required />
            </div>
            <div class="form-group">
              <label for="phone">Phone Number</label>
              <input type="tel" id="phone" name="phone" required />
            </div>
            <div class="form-group">
              <label for="gender">Gender</label>
              <select id="gender" name="gender" required>
                <option value="">-- Select Gender --</option>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
                <option value="Other">Other</option>
              </select>
            </div>
            <div class="form-group">
              <label for="availability">Availability (Days & Time)</label>
              <input
                type="text"
                id="availability"
                name="availability"
                placeholder="e.g. Mon-Fri, 9am - 5pm"
                required
              />
            </div>
            <div class="form-group">
              <label for="speciality">Speciality</label>
              <input
                type="text"
                id="speciality"
                name="speciality"
                placeholder="e.g. Anxiety, Depression"
                required
              />
            </div>
            <div class="form-group">
              <label for="certification">Upload Certification</label>
              <input
                type="file"
                id="certification"
                name="certification"
                accept=".pdf,.jpg,.png"
                required
              />
            </div>
            <button type="submit">Apply as Therapist</button>
            <p class="login-link">
              Already registered? <a href="therapist-login.php">Login here</a>
            </p>
          </form>
        </div>
      </div>
    </div>
  </body>
</html>
