<?php
session_start();
require 'db.php';

$session_id = isset($_GET['session_id']) ? $_GET['session_id'] : null;
$sender_role = 'therapist';

if (!$session_id) {
    echo "Error: session_id not provided in URL.";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Therapist Chat Session</title>
    <link href="https://fonts.googleapis.com/css2?family=Winky+Sans&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;

        }

        body {
            margin: 0;
             font-family: "Winky Sans", sans-serif;
            background: #f0f4f8;
            color: #333;
        }

        .header {
            background-color: #486569;
            color: white;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header h2 {
            margin: 0;
        }

        .back-btn {
            background-color: #8ab2c0;
            color: #486569;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
        }

        .back-btn:hover {
            background-color: #8ab2c0;
        }

        #chatBox {
            width: 90%;
            max-width: 1000px;
            height: 500px;
            margin: 30px auto 10px;
            padding: 15px;
            background: #ffffff;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            overflow-y: auto;
        }

        .chat-message {
            margin: 8px 0;
            padding: 10px 15px;
            border-radius: 20px;
            max-width: 70%;
            word-wrap: break-word;
            clear: both;
        }

        .therapist {
            background-color: #e0f7fa;
            float: right;
            text-align: right;
        }

        .client {
            background-color: #8ab2c0;
            float: left;
            text-align: left;
        }

        #chatForm {
            display: flex;
            justify-content: center;
            margin: 20px auto;
            width: 90%;
            max-width: 1000px;
        }

        #messageInput {
            flex: 1;
            padding: 12px 15px;
            border: 1px solid #ccc;
            border-radius: 20px;
            font-size: 16px;
            outline: none;
        }

        #sendBtn {
            margin-left: 10px;
            padding: 12px 20px;
            border: none;
            background-color:  #486569;
            color: white;
            font-weight: bold;
            border-radius: 20px;
            cursor: pointer;
        }

        #sendBtn:hover {
            background-color:rgb(57, 78, 80);
        }
    </style>
</head>
<body>

<div class="header">
    <h2>Chat Session with Client</h2>
    <a href="home.html" class="back-btn">← Back to Dashboard</a>
</div>

<div id="chatBox"></div>

<form id="chatForm">
    <input type="text" id="messageInput" placeholder="Type your message..." autocomplete="off" />
    <button type="submit" id="sendBtn">Send</button>
</form>

<script>
const sessionId = <?= json_encode($session_id) ?>;
const senderRole = "therapist";
const chatBox = document.getElementById("chatBox");
const chatForm = document.getElementById("chatForm");
const messageInput = document.getElementById("messageInput");

function fetchMessages() {
    fetch(`fetch_messages.php?session_id=${sessionId}`)
        .then(res => res.json())
        .then(data => {
            chatBox.innerHTML = "";
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
    const message = messageInput.value.trim();
    if (message === "") return;

    fetch("send_message.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `session_id=${sessionId}&sender_role=${senderRole}&message=${encodeURIComponent(message)}`
    }).then(() => {
        messageInput.value = "";
        fetchMessages();
    });
});

setInterval(fetchMessages, 2000);
fetchMessages();
</script>

</body>
</html>
