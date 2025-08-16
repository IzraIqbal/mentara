<?php
require 'db.php';
session_start();

if (!isset($_SESSION['user_id'], $_SESSION['user_role'])) {
    die("Unauthorized.");
}

$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['user_role'];

if (!isset($_GET['client_id'], $_GET['session_date'])) {
    die("Invalid request.");
}

$client_id = intval($_GET['client_id']);
$session_date = $_GET['session_date'];

$stmt = $conn->prepare("SELECT * FROM sessions WHERE client_id = ? AND session_date = ? AND mode = 'audio' AND status = 'approved'");
$stmt->execute([$client_id, $session_date]);
$session = $stmt->fetch();

if (!$session) {
    die("Session not found or not allowed.");
}

$session_id = $session['session_id'];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Client Audio Session</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f0f4f8;
            text-align: center;
            padding: 40px;
            color: #333;
        }

        h2 {
            color: #486569;
            margin-bottom: 10px;
        }

        .session-info {
            margin-bottom: 20px;
        }

        .call-panel {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            max-width: 800px;
            margin: auto;
        }

        .users {
            display: flex;
            justify-content: space-around;
            align-items: center;
            margin-bottom: 30px;
        }

        .user {
            text-align: center;
        }

        .user img {
            width: 100px;
            height: 100px;
            background: #ccc;
            border-radius: 50%;
            margin-bottom: 10px;
        }

        .status {
            font-weight: bold;
            padding: 6px 12px;
            border-radius: 6px;
            display: inline-block;
        }

        .connected { background: #d1e7dd; color: #155724; }
        .disconnected { background: #f8d7da; color: #721c24; }

        button {
            padding: 10px 18px;
            font-size: 16px;
            margin: 5px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }

        .join-btn { background: #486569; color: white; }
        .leave-btn { background: #a94442; color: white; }
        .talk-btn { background: #8ab2c0; color: white; }

        #audioMessages audio {
            margin-top: 10px;
            width: 90%;
        }

        .timeline {
            font-size: 14px;
            color: #666;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>

    <h2>Client - Audio Session</h2>

    <div class="session-info">
        <p><strong>Session Date:</strong> <?= htmlspecialchars($session_date) ?></p>
        <p><strong>Client ID:</strong> <?= $client_id ?> | <strong>Therapist ID:</strong> <?= $session['therapist_id'] ?></p>
        <p class="timeline" id="sessionTimer">Session Time: 0s</p>
    </div>

    <div class="call-panel">
        <div class="users">
            <div class="user">
                <img src="images/client_audio.png" alt="Client Icon">
                <div id="clientStatus" class="status disconnected">You: Not Connected</div>
            </div>
            <div class="user">
                <img src="images/therapist_audio.png" alt="Therapist Icon">
                <div id="therapistStatus" class="status disconnected">Therapist: Waiting...</div>
            </div>
        </div>

        <div class="controls">
            <button id="joinBtn" class="join-btn">Join Call</button>
            <button id="leaveBtn" class="leave-btn">End Call</button><br><br>
            <button id="recordBtn" class="talk-btn">🎤 Press & Hold to Talk</button>
        </div>

        <div id="audioMessages"></div>
    </div>

    <script>
        const sessionId = <?= $session_id ?>;
        const userId = <?= $user_id ?>;
        const role = "<?= $user_role ?>";
        const otherRole = role === "client" ? "therapist" : "client";

        const joinBtn = document.getElementById("joinBtn");
        const leaveBtn = document.getElementById("leaveBtn");
        const clientStatus = document.getElementById("clientStatus");
        const therapistStatus = document.getElementById("therapistStatus");
        const sessionTimer = document.getElementById("sessionTimer");
        const audioContainer = document.getElementById("audioMessages");
        const recordBtn = document.getElementById("recordBtn");

        let timer = 0;
        setInterval(() => {
            sessionTimer.textContent = "Session Time: " + (++timer) + "s";
        }, 1000);

        joinBtn.onclick = () => {
            fetch("audio_join.php", {
                method: "POST",
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: `session_id=${sessionId}&user_id=${userId}&role=${role}`
            }).then(() => {
                clientStatus.textContent = "You: Connected";
                clientStatus.className = "status connected";
            });
        };

        leaveBtn.onclick = () => {
            fetch("audio_leave.php", {
                method: "POST",
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: `session_id=${sessionId}&user_id=${userId}`
            }).then(() => {
                clientStatus.textContent = "You: Disconnected";
                clientStatus.className = "status disconnected";
            });
        };

        // Poll both statuses
        function pollStatus() {
            fetch(`audio_status.php?session_id=${sessionId}&role=client`)
                .then(res => res.json())
                .then(data => {
                    clientStatus.textContent = "You: " + (data.status === 'connected' ? "Connected" : "Waiting...");
                    clientStatus.className = data.status === 'connected' ? "status connected" : "status disconnected";
                });

            fetch(`audio_status.php?session_id=${sessionId}&role=therapist`)
                .then(res => res.json())
                .then(data => {
                    therapistStatus.textContent = "Therapist: " + (data.status === 'connected' ? "Connected" : "Waiting...");
                    therapistStatus.className = data.status === 'connected' ? "status connected" : "status disconnected";
                });
        }

        let mediaRecorder;
        let audioChunks = [];

        recordBtn.addEventListener('mousedown', async () => {
            const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
            mediaRecorder = new MediaRecorder(stream);
            audioChunks = [];

            mediaRecorder.ondataavailable = e => audioChunks.push(e.data);
            mediaRecorder.onstop = async () => {
                const blob = new Blob(audioChunks, { type: 'audio/webm' });
                const formData = new FormData();
                formData.append('session_id', sessionId);
                formData.append('role', role);
                formData.append('audio', blob);

                await fetch('send_voice_clip.php', { method: 'POST', body: formData });
            };

            mediaRecorder.start();
        });

        recordBtn.addEventListener('mouseup', () => {
            if (mediaRecorder && mediaRecorder.state !== "inactive") {
                mediaRecorder.stop();
            }
        });

        function pollVoice() {
            fetch(`receive_voice_clip.php?session_id=${sessionId}&role=${otherRole}`)
                .then(res => {
                    if (res.status === 200) return res.blob();
                })
                .then(blob => {
                    if (blob && blob.size > 0) {
                        const audio = document.createElement('audio');
                        audio.controls = true;
                        audio.src = URL.createObjectURL(blob);
                        audioContainer.appendChild(audio);
                    }
                });
        }

        setInterval(pollStatus, 3000);
        setInterval(pollVoice, 2000);
    </script>

</body>
</html>
