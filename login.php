
<?php
require 'db.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $user_type = $_POST['user_type'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? AND user_type = ?");
    $stmt->execute([$email, $user_type]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_role'] = strtolower($user_type);
        if ($user_type === 'Client') {
            header("Location: client-dashboard.php?id=" . $user['id']);
        } else {
            header("Location: admin-dashboard.php?id=" . $user['id']);
        }
    } else {
        echo "Invalid credentials.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login | Mentara</title>
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
      <div class="admin-client-image-section">
        <img
          src="images/admin-client_image_login.png"
          alt="Admin-Client Illustration"
        />
      </div>
      <div class="admin-client-form-wrapper">
        <h2>Login to Mentara</h2>
        <form action="login.php" method="POST">
          <div class="admin-client-form-group">
            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" required />
          </div>
          <div class="admin-client-form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required />
          </div>
          <div class="admin-client-form-group">
            <label for="user_type">Login As</label>
            <select name="user_type" id="user_type" required>
              <option value="">-- Select Role --</option>
              <option value="Client">Client</option>
              <option value="Admin">Admin</option>
            </select>
          </div>
          <button type="submit">Login</button>
          <p class="admin-client-switch-link">
            New to Mentara?
            <a href="register.php">Register here</a>
          </p>
        </form>
      </div>
    </div>
  </body>
</html>
