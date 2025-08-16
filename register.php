<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $passwordPlain = $_POST['password'];
    $passwordHashed = password_hash($passwordPlain, PASSWORD_DEFAULT);
    $user_type = $_POST['user_type'];

       $checkStmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $checkStmt->execute([$email]);

    if ($checkStmt->rowCount() > 0) {
        echo "<script>alert('⚠️ Email already registered. Please use a different one.'); window.history.back();</script>";
        exit();
    }

    // Insert into users table
    $stmt = $conn->prepare("INSERT INTO users (name, email, password, user_type) VALUES (?, ?, ?, ?)");
    $stmt->execute([$name, $email, $passwordHashed, $user_type]);
    $user_id = $conn->lastInsertId();

    if ($user_type === 'Client') {
        $phone = $_POST['phone'] ?? null;
        $gender = $_POST['gender'] ?? null;
        $age = $_POST['age'] ?? null;

        $stmt = $conn->prepare("INSERT INTO client (user_id, name, email, password, phone, gender, age) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$user_id, $name, $email, $passwordHashed, $phone, $gender, $age]);
    }

    if ($user_type === 'Admin') {
        $stmt = $conn->prepare("INSERT INTO admin (user_id, name, email, password) VALUES (?, ?, ?, ?)");
        $stmt->execute([$user_id, $name, $email, $passwordHashed]);
    }

    header("Location: login.php?id=" . $user_id);
}
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Register | Mentara</title>
    <link rel="stylesheet" href="styles.css" />
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
    <div class="admin-client-body">
      <div class="admin-client-form-wrapper">
        <h2>Join Mentara</h2>
        <form action="register.php" method="POST">
          <div class="admin-client-form-group">
            <label for="name">Full Name</label>
            <input type="text" id="name" name="name" required />
          </div>
          <div class="admin-client-form-group">
            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" required />
          </div>
          <div class="admin-client-form-group">
            <label for="password">Create Password</label>
            <input type="password" id="password" name="password" required />
          </div>
          <div id="client-extra-fields" style="display: none;">
    <div class="admin-client-form-group">
        <label for="phone">Phone Number</label>
        <input type="text" name="phone" id="phone" />
    </div>
    <div class="admin-client-form-group">
        <label for="gender">Gender</label>
        <select name="gender" id="gender">
            <option value="">-- Select Gender --</option>
            <option value="Male">Male</option>
            <option value="Female">Female</option>
            <option value="Other">Other</option>
        </select>
    </div>
    <div class="admin-client-form-group">
        <label for="age">Age</label>
        <input type="number" name="age" id="age" />
    </div>
</div>

          <div class="admin-client-form-group">
            <label for="user_type">Register As</label>
            <select name="user_type" id="user_type" required>
              <option value="">-- Select Role --</option>
              <option value="Client">Client</option>
              <option value="Admin">Admin</option>
            </select>
          </div>
          <button type="submit">Register</button>
          <p class="admin-client-switch-link">
            Already a member? <a href="login.php">Login here</a>
          </p>
        </form>
      </div>
    </div>
    <script>
document.getElementById("user_type").addEventListener("change", function () {
    const clientFields = document.getElementById("client-extra-fields");
    clientFields.style.display = this.value === "Client" ? "block" : "none";
});
</script>

  </body>
</html>
