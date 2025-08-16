<?php
require 'db.php';
session_start();

if (!isset($_GET['client_id'], $_GET['session_date'])) {
    die("Invalid request.");
}

$client_id = intval($_GET['client_id']);
$session_date = $_GET['session_date'];

$stmt = $conn->prepare("SELECT * FROM sessions WHERE client_id = ? AND session_date = ? AND mode = 'chat' AND status = 'approved'");
$stmt->execute([$client_id, $session_date]);
$session = $stmt->fetch();

if (!$session) {
    die("Session not found or not allowed.");
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Attend Chat Session</title>
    <link href="https://fonts.googleapis.com/css2?family=Winky+Sans&display=swap" rel="stylesheet">
    <style>
        body {
             font-family: "Winky Sans", sans-serif;
            background-color: #eef2f5;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 800px;
            margin: 40px auto;
            background: #fff;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        h1 {
            color: #486569;
        }
        .chat-box {
            height: 400px;
            border: 1px solid #ccc;
            padding: 15px;
            overflow-y: auto;
            background-color: #f9f9f9;
            margin-bottom: 15px;
            border-radius: 6px;
        }
        .chat-message {
            margin: 8px 0;
        }
        .chat-message.client {
            text-align: right;
            color: #3a3a3a;
        }
        .chat-message.therapist {
            text-align: left;
            color: #234;
        }
        input[type="text"] {
            width: 85%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }
        button {
            padding: 10px 15px;
            background-color: #486569;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Chat with Therapist</h1>
    <p><strong>Session Date:</strong> <?= htmlspecialchars($session_date) ?></p>

    <div class="chat-box" id="chatBox">
        <!-- Chat messages will be appended here via JS -->
    </div>

    <form id="chatForm">
        <input type="text" id="messageInput" placeholder="Type your message..." required />
        <button type="submit">Send</button>
    </form>
</div>

<script>
const sessionId = <?= $session['session_id'] ?>;
const senderRole = "client"; // or "therapist" depending on who is logged in

function fetchMessages() {
    fetch('fetch_messages.php?session_id=' + sessionId)
        .then(response => response.json())
        .then(data => {
            chatBox.innerHTML = '';
            data.forEach(msg => {
                const div = document.createElement("div");
                div.className = "chat-message " + msg.sender_role;
                div.textContent = msg.message;
                chatBox.appendChild(div);
            });
            chatBox.scrollTop = chatBox.scrollHeight;
        });
}

chatForm.addEventListener("submit", function(e) {
    e.preventDefault();
    const text = messageInput.value.trim();
    if (text === "") return;

    fetch("send_message.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `session_id=${sessionId}&sender_role=${senderRole}&message=${encodeURIComponent(text)}`
    }).then(() => {
        messageInput.value = "";
        fetchMessages();
    });
});

setInterval(fetchMessages, 2000); // Poll every 2 seconds
fetchMessages();
</script>
</body>
</html>
