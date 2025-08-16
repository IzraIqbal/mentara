<?php
require 'db.php';
session_start();

if (!isset($_SESSION['user_id'], $_SESSION['user_role']) || $_SESSION['user_role'] !== 'client') {
    die("Unauthorized access.");
}

$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['user_role'];

if (!isset($_GET['client_id'], $_GET['session_date'])) {
    die("Invalid request.");
}

$client_id = intval($_GET['client_id']);
$session_date = $_GET['session_date'];

// Verify session exists
$stmt = $conn->prepare("SELECT * FROM sessions WHERE client_id = ? AND session_date = ? AND mode = 'video' AND status = 'approved'");
$stmt->execute([$client_id, $session_date]);
$session = $stmt->fetch();

if (!$session) {
    die("Session not found or not allowed.");
}

$session_id = $session['session_id'];
$therapist_id = $session['therapist_id'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Client Video Session</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f4f8fc;
            margin: 0;
            padding: 20px;
            text-align: center;
        }
        h1 {
            color: #486569;
        }
           .video-wrapper {
        position: relative;
        width: 45%;
        aspect-ratio: 16/9;
        background-color: #dbe9ee;
        border-radius: 10px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .video-wrapper i {
        font-size: 40px;
        color: #8ab2c0;
        position: absolute;
        z-index: 1;
    }

    .video-wrapper video {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: none;
    }

    .video-wrapper video.active {
        display: block;
    }
        .video-box {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 30px;
        }
        video {
            width: 45%;
            border-radius: 10px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        .controls {
            margin-top: 30px;
        }
        button {
            padding: 12px 20px;
            font-size: 16px;
            border: none;
            border-radius: 6px;
            background-color: #486569;
            color: white;
            cursor: pointer;
            margin: 10px;
        }
        button.leave {
            background-color: #a94442;
        }
    </style>
</head>
<body>

<h1>Video Session with Therapist <?= htmlspecialchars($therapist_id) ?></h1>
<p><strong>Session Date:</strong> <?= htmlspecialchars($session_date) ?></p>

<div class="video-box">
     <div class="video-wrapper">
        <i class="fas fa-video" id="localPlaceholder"></i>
        <video id="localVideo" autoplay muted></video>
    </div>

    <div class="video-wrapper">
        <i class="fas fa-video" id="remotePlaceholder"></i>
        <video id="remoteVideo" autoplay></video>
    </div>
  
</div>

<div class="controls">
    <button onclick="startCall()">Start Call</button>
    <button onclick="leaveCall()" class="leave">Leave</button>
</div>

<script>
const sessionId = <?= $session_id ?>;
const userId = <?= $user_id ?>;
const role = "client";
const otherRole = "therapist";

let pc = new RTCPeerConnection({
    iceServers: [{ urls: "stun:stun.l.google.com:19302" }]
});
let localStream;

// Video setup
async function startCall() {
    localStream = await navigator.mediaDevices.getUserMedia({ video: true, audio: true });
    document.getElementById("localVideo").srcObject = localStream;

    localStream.getTracks().forEach(track => pc.addTrack(track, localStream));

    pc.onicecandidate = e => {
        if (e.candidate) {
            fetch("video_ice.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
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

    const offer = await pc.createOffer();
    await pc.setLocalDescription(offer);

    await fetch("video_offer.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
            session_id: sessionId,
            role,
            offer
        })
    });
}

// Poll for answer
async function pollAnswer() {
    const res = await fetch(`video_answer.php?session_id=${sessionId}&role=${otherRole}`);
    const data = await res.json();
    if (data.answer && !pc.remoteDescription) {
        await pc.setRemoteDescription(new RTCSessionDescription(data.answer));
    }
}

// Poll for ICE
async function pollIce() {
    const res = await fetch(`video_ice.php?session_id=${sessionId}&role=${otherRole}`);
    const candidates = await res.json();
    candidates.forEach(async c => {
        try {
            await pc.addIceCandidate(new RTCIceCandidate(c));
        } catch (err) {}
    });
}

setInterval(pollAnswer, 2000);
setInterval(pollIce, 2000);

// Leave
function leaveCall() {
    if (localStream) localStream.getTracks().forEach(t => t.stop());
    pc.close();
    alert("You have left the session.");
}
// When local stream starts
document.getElementById("localVideo").classList.add("active");
document.getElementById("localPlaceholder").style.display = "block";

// When remote stream is received
pc.ontrack = e => {
    document.getElementById("remoteVideo").srcObject = e.streams[0];
    document.getElementById("remoteVideo").classList.add("active");
    document.getElementById("remotePlaceholder").style.display = "none";
};
</script>

</body>
</html>
