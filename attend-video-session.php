<?php
require 'db.php';
session_start();


$session_id = isset($_GET['session_id']) ? $_GET['session_id'] : null;
$sender_role = 'therapist';

$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['user_role'];

if (!isset($_GET['client_id'], $_GET['session_date'])) {
    die("Invalid request.");
}

$client_id = intval($_GET['client_id']);
$session_date = $_GET['session_date'];

// Check session
$stmt = $conn->prepare("SELECT * FROM sessions WHERE client_id = ? AND session_date = ? AND mode = 'video' AND status = 'approved'");
$stmt->execute([$client_id, $session_date]);
$session = $stmt->fetch();

if (!$session) {
    die("Session not found.");
}

$session_id = $session['session_id'];
$other_role = $user_role === 'client' ? 'therapist' : 'client';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Video Session</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f0f4f8;
            text-align: center;
            padding: 30px;
            color: #333;
        }
        h2 {
            color: #486569;
        }
        .video-container {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin: 30px auto;
        }
        video {
            width: 45%;
            height: auto;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        button {
            padding: 10px 18px;
            font-size: 16px;
            margin: 10px;
            border: none;
            border-radius: 6px;
            background-color: #486569;
            color: white;
            cursor: pointer;
        }
        .leave-btn {
            background-color: #a94442;
        }
    </style>
</head>
<body>
<h2>Video Session</h2>
<p><strong>Session Date:</strong> <?= htmlspecialchars($session_date) ?></p>

<div class="video-container">
    <video id="localVideo" autoplay muted></video>
    <video id="remoteVideo" autoplay></video>
</div>

<button onclick="startCall()">Start Call</button>
<button onclick="leaveCall()" class="leave-btn">Leave</button>

<script>
const sessionId = <?= $session_id ?>;
const userId = <?= $user_id ?>;
const role = "<?= $user_role ?>";
const otherRole = "<?= $other_role ?>";

let pc = new RTCPeerConnection({
    iceServers: [{ urls: "stun:stun.l.google.com:19302" }]
});
let localStream;

async function startCall() {
    localStream = await navigator.mediaDevices.getUserMedia({ video: true, audio: true });
    document.getElementById("localVideo").srcObject = localStream;

    localStream.getTracks().forEach(track => pc.addTrack(track, localStream));

    pc.onicecandidate = e => {
        if (e.candidate) {
            fetch('video_ice.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({
                    session_id: sessionId,
                    role,
                    candidate: e.candidate
                })
            });
        }
    };

    pc.ontrack = e => {
        document.getElementById("remoteVideo").srcObject = e.streams[0];
    };

    if (role === "client") {
        const offer = await pc.createOffer();
        await pc.setLocalDescription(offer);

        fetch('video_offer.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({
                session_id: sessionId,
                role,
                offer
            })
        });
    }
}


async function pollOfferAndAnswer() {
    if (role !== "therapist") return;

    const res = await fetch(`video_offer.php?session_id=${sessionId}&role=client`);
    const data = await res.json();

    if (data.offer && !pc.remoteDescription) {
        await pc.setRemoteDescription(new RTCSessionDescription(data.offer));
        const answer = await pc.createAnswer();
        await pc.setLocalDescription(answer);

        await fetch('video_answer.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({
                session_id: sessionId,
                role,
                answer
            })
        });
    }
}

async function pollIce() {
    const res = await fetch(`video_ice.php?session_id=${sessionId}&role=${otherRole}`);
    const data = await res.json();
    data.forEach(async c => {
        try {
            await pc.addIceCandidate(new RTCIceCandidate(c));
        } catch (e) {}
    });
}

setInterval(pollAnswer, 2000);
setInterval(pollIce, 2000);
setInterval(pollOfferAndAnswer, 2000);
function leaveCall() {
    if (localStream) localStream.getTracks().forEach(t => t.stop());
    pc.close();
    alert("You left the session.");
}
</script>
</body>
</html>
