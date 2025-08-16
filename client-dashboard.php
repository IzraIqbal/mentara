<?php
require 'db.php';
session_start();

if (!isset($_GET['id'])) {
    die("Access denied.");
}

$id = $_GET['id'];

// Fetch client user data
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ? AND user_type = 'Client'");
$stmt->execute([$id]);
$user = $stmt->fetch();

if (!$user) {
    die("Client not found.");
}

$id = $_GET['id'] ?? null;

if ($id) {
    // Only set session if not already set
    if (!isset($_SESSION['id'])) {
        $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $_SESSION['id'] = $user['id'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['user_type'];
        }
    }
}

// Placeholder stats (replace with actual DB values later)
$journalStreak = 5;
$sessionsCompleted = 3;
$moodEntries = 12;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Client Dashboard | Mentara</title>
    <link href="https://fonts.googleapis.com/css2?family=Winky+Sans&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        

     * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
              font-family: "Winky Sans", sans-serif;
  background-color: beige;

        }

      .sidebar {
    position: fixed;
    left: 0;
    top: 0;
    width: 220px;
    height: 100vh;
    box-shadow: 2px 0 12px rgba(0,0,0,0.1);
    display: flex;
    flex-direction: column;
    align-items: center;
    padding-top: 10px;
    z-index: 1000;
}
   .sidebar i{
    background-color: transparent;
   }
.logo-container img {
    width: 100px;
    height: auto;
    border-radius: 50%;
    margin-bottom: 0px;
      font-family: "Winky Sans", sans-serif;
  background-color: beige;
}

.sidebar-links {
    width: 100%;
    display: flex;
    flex-direction: column;
    gap: 15px;
    padding: 0 20px;
   margin-top: 0;
}

.sidebar-links a {
    display: flex;
    align-items: center;
    gap: 10px;
    text-decoration: none;
    color: #486569;
    font-size: 16px;
    padding: 10px 15px;
    border-radius: 8px;
    transition: background-color 0.3s ease;
}

.sidebar-links a:hover {
    background-color: #8ab2c0;
    color: #486569;
}

.account-icon {
    justify-content: flex-start;
    font-size: 18px;
    margin-top: 20px;
}
.account-icon:hover{
    background-color: #8ab2c0;
}

body {
    margin-left: 220px; 
  background-color: beige;
}

 .dashboard-content {
    max-width: 1000px;
    margin: auto;
    padding: 140px 20px 60px;
    text-align: center;
          
}

        h1, h2 {
            color: #486569;
        }

        .welcome-text {
            font-size: 1em;
            color: #8ab2c0;
            margin-bottom: 40px;
        }

        .illustration {
            max-width: 250px;
            margin: 20px auto;
        }

       

        .account-summary {
            background-color: beige;
            padding: 30px;
            border-radius: 16px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            margin: 40px auto 20px;
            text-align: center;
            max-width: 400px;
        }

        .account-summary h3 {
            color: #486569;
        }
        .account-summary i{
            font-size: 30px;
        }
        .stats-cards {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin: 40px 0;
            flex-wrap: wrap;
            background-color: beige;
        }

        .card {
            background-color: #8ab2c0;
            padding: 20px 30px;
            border-radius: 20px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.06);
            text-align: center;
            width: 250px;
        }

        .card h4 {
            font-size: 24px;
            color: #486569;
            background-color: transparent;
        }

        .card p {
            font-size: 16px;
            color: #486569;
             background-color: transparent;
        }

        .quotes {
            margin-top: 50px;
        }

        .quote-card {
            background:#8ab2c0;
          
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
            text-align: left;
            max-width: 700px;
            margin-left: auto;
            margin-right: auto;
            color: #486569;
        }

        .quote-card p {
            font-size: 18px;
            font-style: italic;
            color: #486569;
            background-color: transparent;
        }
.top-logout {
    position: fixed;
    top: 20px;
    right: 30px;
    z-index: 1100;
}

.top-logout a {
    text-decoration: none;
    color: #486569;
    font-weight: bold;
    font-size: 16px;
    background-color: #f5f5dc;
    padding: 8px 16px;
    border-radius: 8px;
    border: 2px solid #486569;
    transition: background 0.3s ease, color 0.3s ease;
}
.top-logout i{
    background-color: transparent;
}
.top-logout a:hover {
    background-color: #486569;
    color: white;
}
.top-logout i:hover{
    background-color: transparent;
}
/*footer*/
footer {
  position: relative;
  width: 100%;
  background: #8ab2c0;
  min-height: 20%;
  padding: 1px 50px;
  display: flex;
  justify-content: center;
  align-items: center;
  flex-direction: column;
  margin-top: 200px;
}
footer .social-icon,
footer .menu {
  position: relative;
  display: flex;
  justify-content: center;
  align-items: center;
  margin: 10px 0;
  background-color: transparent;
  flex-wrap: wrap;
}

footer .social-icon li,
footer .menu li {
  list-style: none;
  background-color: transparent;
}
footer .social-icon li a,
footer .menu li a {
  font-size: 2rem;
  color: #ffffff;
  margin: 0 10px;
  display: inline-block;
  transition: 0.5x;
  background-color: transparent;
}
footer .social-icon li a:hover {
  transform: translateY(-10px);
}
footer .social-icon i {
  background-color: transparent;
  color: #ffffff;
}

footer .menu li a {
  font-size: 1.2rem;
  color: #ffffff;
  margin: 0 10px;
  display: inline-block;
  text-decoration: none;
  opacity: 0.75;
  background-color: transparent;
}

footer .menu li a:hover {
  opacity: 1;
}

footer p {
  color: #ffffff;
  text-align: center;
  margin-top: 15px;
  margin-bottom: 10px;
  font-size: 1.1rem;
  background-color: transparent;
}

footer .wave {
  position: absolute;
  top: -100px;
  left: 0;
  width: 100%;
  height: 100px;
  background: url(images/wave.png);
  background-size: 1000px 100px;
}

footer .wave#wave1 {
  z-index: 1000;
  opacity: 1;
  bottom: 0;
  animation: animateWave 4s linear infinite;
}
footer .wave#wave2 {
  z-index: 999;
  opacity: 0.5;
  bottom: 10px;
  animation: animateWave_02 4s linear infinite;
}
footer .wave#wave3 {
  z-index: 1000;
  opacity: 0.2;
  bottom: 15px;
  animation: animateWave 3s linear infinite;
}
footer .wave#wave4 {
  z-index: 999;
  opacity: 0.7;
  bottom: 28px;
  animation: animateWave_02 3s linear infinite;
}

@keyframes animateWave {
  0% {
    background-position-x: 1000px;
  }
  100% {
    background-position-x: 0px;
  }
}

@keyframes animateWave_02 {
  0% {
    background-position-x: 0px;
  }
  100% {
    background-position-x: 1000px;
  }
}

        @media screen and (max-width: 768px) {
           .sidebar {
        width: 100%;
        height: auto;
        flex-direction: row;
        flex-wrap: wrap;
        padding: 10px;
        justify-content: center;
        position: relative;
    }

    body {
        margin-left: 0;
        padding-top: 150px;
    }

    .sidebar-links {
        flex-direction: row;
        flex-wrap: wrap;
        justify-content: center;
    }

    .sidebar-links a {
        font-size: 14px;
        padding: 8px;
    }

            .account-icon {
                margin-top: 10px;
            }

            .stats-cards {
                flex-direction: column;
                align-items: center;
            }
        }
    </style>
</head>
<body>
    <div class="top-logout">
    <a href="logout.php" title="Logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
</div>

<div class="sidebar">
    <div class="logo-container">
        <a href="client-dashboard.php?id=<?= $user['id'] ?>">
            <img src="images/Green Minimalist Nature Beauty Care Logo (3).png" />
        </a>
    </div>
    <nav class="sidebar-links">
        <a href="manage-account.php?id=<?= $user['id'] ?>" class="account-icon" title="Account"><i class="fas fa-user"></i>My account</a>
        <a href="book-session.php?id=<?= $user['id'] ?>"><i class="fas fa-calendar-check"></i> Book Sessions</a>
        <a href="mood-journal.php?id=<?= $user['id'] ?>"><i class="fas fa-heart"></i> Mood</a>
        <a href="client-tasks.php?id=<?= $user['id'] ?>"><i class="fas fa-tasks"></i> Resources</a>
        <a href="view-History.php?id=<?= $user['id'] ?>"><i class="fas fa-book"></i> view History</a>
        <a href="quiz.php?id=<?= $user['id'] ?>"><i class="fas fa-question-circle"></i> Quiz</a>
        <a href="therapy-types.php"><i class="fas fa-brain"></i> Therapy</a>
        <a href="available-therapists.php"><i class="fas fa-user-md"></i> Therapists</a>
        <a href="client-submit-inquiry.php"><i class="fas fa-envelope-open-text"></i> Inquiry</a>
        <a href="Announcements.php"><i class="fas fa-blog"></i> Announcements</a>
      
    </nav>
</div>

<!-- 🌟 Hero Landing -->
<div style="background: linear-gradient(to right, #f5f5dc, #f5f5dc); padding: 100px 60px 50px; text-align:center;margin-bottom:-50px">
    <img src="images/clientdashboard_image_login.png" alt="Hero" style="max-width:400px; margin-bottom:20px;" />
    <h1 style="font-size: 2.5em; color: #2c3e50;">Welcome to Your Safe Space, <?= htmlspecialchars($user['name']) ?> </h1>
    <p style="font-size: 1.2em; color: #6c757d; max-width: 700px; margin: 20px auto;">Your journey to mental clarity and emotional resilience starts here. Mentara is more than a platform—it's a path to healing, guided by care and personalized tools.</p>
</div>

<!--  Welcome -->


<!--  Why Mentara Section -->
<div style="background-color:#f5f5dc; padding:60px 20px;" class="whychoose">
    <h2 style="text-align:center; color:#486569; margin-bottom:40px;">Why Choose Mentara?</h2>
    <div style="display: flex; flex-wrap: wrap; justify-content: center; gap: 30px;">
        <div style="width:250px; text-align:center;color:#486569">
            <img src="images/Managing Anxiety.jpg" alt="Support" style="width:200px;border-radius: 20px;height:150px">
            <h4>1:1 Therapy</h4>
            <p>Work with qualified therapists in confidential one-on-one sessions.</p>
        </div>
        <div style="width:250px; text-align:center;color:#486569">
            <img src="images/hero-image.jpeg" alt="Journal" style="width:200px;border-radius: 20px;height:150px">
            <h4>Smart Journals</h4>
            <p>Track emotions, moods, and breakthroughs easily with guided journaling.</p>
        </div>
        <div style="width:250px; text-align:center;color:#486569">
            <img src="images/Building Resilience.jpg" alt="Mood Tracker" style="width:200px;border-radius: 20px;height:150px">
            <h4>Mood Tracker</h4>
            <p>Visualize your emotional journey and identify your patterns over time.</p>
        </div>
    </div>
</div>



<!--  Account Info -->
<div style="display: flex; flex-wrap: wrap; justify-content: center; gap: 30px; margin: 60px 0;">

    <!--  Account Info -->
    <div class="account-summary" style="flex: 1; min-width: 280px;color:#486569; background: transparent; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
        <i class="fa-solid fa-gear"></i>
    <h3>My Account Info</h3>
        <p><strong>Name:</strong> <?= htmlspecialchars($user['name']) ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
        <p><strong>User ID:</strong> <?= $user['id'] ?></p>
        <p><a href="manage-account.php?id=<?= $user['id'] ?>" style="color:#486569; font-weight:bold;">Edit My Account</a></p>
    </div>

    <!-- Assigned Therapist Info -->
    <div class="account-summary" style="flex: 1; min-width: 280px; color:#486569;background:transparent; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
        <i class="fa-solid fa-user-doctor"></i>
    <h3>Assigned Therapist</h3>
        <p><strong>Name:</strong> Dr. Ayesha Fernando</p>
        <p><strong>Email:</strong> ayesha.therapist@mentara.com</p>
        <p><strong>Specialty:</strong> Anxiety & Stress Management</p>
        <p><a href="available-therapists.php" style="color:#486569; font-weight:bold;">View All Therapists</a></p>
    </div>

    <!-- Next Session Info -->
    <div class="account-summary" style="flex: 1; min-width: 280px;color:#486569; background: transparent; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
        
    <i class="fa-solid fa-desktop"></i>
    <h3>Next Session</h3>
        <p><strong>Date:</strong> June 2, 2025</p>
        <p><strong>Time:</strong> 4:00 PM - 5:00 PM</p>
        <p><strong>Mode:</strong> Online (Zoom)</p>
        <p><a href="client-sessions.php?id=<?= $user['id'] ?>" style="color:#486569; font-weight:bold;">View Upcoming Session </a></p>
    </div>

</div>

<!--  Quotes -->
<div class="quotes">
    <div class="quote-card">
        <p>"You don’t have to control your thoughts. You just have to stop letting them control you." – Dan Millman</p>
    </div>
    <div class="quote-card">
        <p>"Sometimes the most productive thing you can do is relax." – Mark Black</p>
    </div>
    <div class="quote-card">
        <p>"Healing is not linear. Give yourself permission to rest." – Unknown</p>
    </div>
</div>
<!--footer-->
    <footer>
      <div class="waves">
        <div class="wave" id="wave1"></div>
        <div class="wave" id="wave2"></div>
        <div class="wave" id="wave3"></div>
        <div class="wave" id="wave4"></div>
      </div>
      <ul class="social-icon">
        <li>
          <a href="#"><i class="fa-solid fa-envelope"></i></a>
        </li>
        <li>
          <a href="#"><i class="fa-brands fa-facebook"></i></a>
        </li>
        <li>
          <a href="#"><i class="fa-brands fa-whatsapp"></i></a>
        </li>
        <li>
          <a href="#"><i class="fa-brands fa-instagram"></i></a>
        </li>
      </ul>
      <ul class="menu">
        <li><a href="#careers">Careers</a></li>
        
        <li><a href="#FAQs">FAQ</a></li>
        <li><a href="">Terms&Conditions</a></li>
        <li><a href="contact.html">Contact Us</a></li>
      </ul>
      <p>© 2025 Mentara. All rights reserved.</p>
    </footer>


</body>

</html>
