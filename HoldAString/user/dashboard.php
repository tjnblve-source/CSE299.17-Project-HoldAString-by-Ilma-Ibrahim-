<?php
require_once '../config/db.php';
require_once '../algorithms/decay_logic.php';
require_once '../algorithms/phone_alert.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$uid = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT DisplayName FROM USERS WHERE userID = ?");
$stmt->bind_param("s", $uid);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$name = !empty($user['DisplayName']) ? $user['DisplayName'] : "User";

applyHealthDecay($conn, $uid);
triggerPhoneAlerts($conn, $uid);

$status_msg = "";
if (isset($_GET['success'])) {
    if ($_GET['success'] == 'quick_boost') $status_msg = "⚡ Quick Boost Applied!";
    if ($_GET['success'] == '1') $status_msg = "✅ Event Analyzed & Saved!";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | HoldAString</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #1e1412; color: #f5ebe0; margin: 0; display: flex; flex-direction: column; align-items: center; }
        .nav-bar { width: 100%; background: #2d1f18; padding: 15px 20px; display: flex; justify-content: space-between; align-items: center; box-sizing: border-box; border-bottom: 1px solid rgba(212, 163, 115, 0.2); }
        .logo { color: #d4a373; font-weight: bold; font-size: 1.5rem; text-decoration: none; }
        .container { width: 90%; max-width: 600px; margin-top: 30px; padding-bottom: 50px; }
        
        .status-banner { background: rgba(212, 163, 115, 0.1); color: #d4a373; padding: 10px; border-radius: 8px; text-align: center; margin-bottom: 20px; border: 1px solid #d4a373; font-size: 0.9rem; }
        
        .action-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin: 25px 0; }
        
        .btn { padding: 15px; border-radius: 10px; border: none; font-weight: bold; cursor: pointer; text-decoration: none; text-align: center; transition: 0.3s; }
        .btn-primary { background: #d4a373; color: #1e1412; }
        .btn-secondary { background: transparent; border: 2px solid #d4a373; color: #d4a373; }

        .input-card { background: #2a1d15; padding: 25px; border-radius: 20px; border: 1px solid #5c4033; box-sizing: border-box; }
        select, textarea { width: 100%; background: #1e1412; border: 1px solid #5c4033; border-radius: 8px; padding: 12px; color: #f5ebe0; margin-top: 8px; margin-bottom: 20px; box-sizing: border-box; outline: none; }
        
        .interaction-center { margin-bottom: 20px; text-align: center; }
        .btn-group { display: flex; flex-wrap: wrap; gap: 8px; justify-content: center; }
        .quick-btn { background: #3d2b1f; border: 1px solid #d4a373; color: #f5ebe0; padding: 10px 12px; border-radius: 8px; cursor: pointer; font-size: 0.85rem; }
        
        .logout-link { color: #ff6b6b; text-decoration: none; font-size: 0.9rem; }

        .header-wrapper {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            width: 100%;
            margin-bottom: 25px;
            box-sizing: border-box;
        }

        .header-text { flex: 1; }

        .game-box {
            background: #2a1d15;
            border: 1px solid #d4a373;
            border-radius: 12px;
            padding: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
            margin-left: 10px;
            min-width: 170px;
            box-sizing: border-box;
        }

        .game-btn {
            background: #d4a373;
            color: #1e1412 !important;
            text-decoration: none;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 0.75rem;
            font-weight: bold;
            display: block;
            margin-top: 5px;
            text-align: center;
        }

        @media (max-width: 480px) {
            .header-wrapper { flex-direction: column; align-items: flex-start; }
            .game-box { margin-left: 0; margin-top: 15px; width: 100%; }
        }
    </style>
</head>

<nav class="nav-bar">
    <a href="dashboard.php" class="logo">HoldAString</a>
    <a href="../auth/logout.php" class="logout-link">Logout</a>
</nav>

<div class="container">
    <?php if ($status_msg): ?>
        <div class="status-banner"><?php echo $status_msg; ?></div>
    <?php endif; ?>

    <div class="header-wrapper">
        <div class="header-text">
            <h1 style="color: #d4a373; margin: 0; font-size: 2rem;">Hello, <?php echo htmlspecialchars($name); ?>!</h1>
            <p style="margin: 5px 0 0 0; opacity: 0.8;">Keep your strings alive.</p>
        </div>

        <!-- Cleaned up Game Card -->
        <div class="game-box">
            <span style="font-size: 1.8rem;">🎮</span>
            <a href="tictactoe.php" class="game-btn">Play Tic-Tac-Toe</a>
        </div>
    </div>

    <div class="action-grid">
        <a href="add_connection.php" class="btn btn-primary">+ New String</a>
        <a href="view_connections.php" class="btn btn-secondary">All Strings</a>
    </div>

    <div class="input-card">
        <form action="event_processor.php" method="POST" onsubmit="return validateSubmission()">
            
            <label style="color: #d4a373; font-size: 0.85rem;">1. Target Connection:</label>
            <div style="display: flex; gap: 10px; margin-top: 8px; margin-bottom: 20px;">
                <select name="target_connection_id" id="connectionSelector" required style="flex: 2; margin-bottom: 0;">
                    <option value="">-- Select Connection --</option>
                    <?php
                    $query = "SELECT connectionID, Name FROM CONNECTIONS WHERE ownerID = '$uid' ORDER BY Name ASC";
                    $result = $conn->query($query);
                    while($row = $result->fetch_assoc()) {
                        echo "<option value='{$row['connectionID']}'>" . htmlspecialchars($row['Name']) . "</option>";
                    }
                    ?>
                </select>

                <button type="button" onclick="viewTimeline()" class="btn" style="flex: 1; height: 50px; padding: 0; background: #5c4033; color: #d4a373; border: 1px solid #d4a373; font-size: 0.8rem;">
                    View Events
                </button>
            </div>

            <label style="color: #d4a373; font-size: 0.85rem;">2. Interaction Type:</label>
            <div class="interaction-center">
                <input type="hidden" name="interaction_type" id="selectedType" value="">
                <div class="btn-group">
                    <button type="button" class="quick-btn" id="btn-text" onclick="setQuickType('text')">💬 Text</button>
                    <button type="button" class="quick-btn" id="btn-call" onclick="setQuickType('call')">📞 Call</button>
                    <button type="button" class="quick-btn" id="btn-hangout" onclick="setQuickType('hangout')">🍕 Hangout</button>
                </div>
            </div>

            <label style="color: #d4a373; font-size: 0.85rem;">3. Description for AI Analysis (Optional):</label>
            <textarea name="user_input" id="interactionDesc" rows="4" placeholder="Type here for AI analysis..."></textarea>
            
            <button type="submit" class="btn btn-primary" style="width: 100%;">Save Interaction</button>
        </form>
    </div>
</div>

<script>
    function viewTimeline() {
        const cid = document.getElementById('connectionSelector').value;
        if (cid) {
            window.location.href = 'view_events.php?id=' + cid;
        } else {
            alert("Please select a connection first to view its events!");
        }
    }

    function setQuickType(type) {
        document.getElementById('selectedType').value = type;
        document.querySelectorAll('.quick-btn').forEach(btn => {
            btn.style.background = '#3d2b1f';
            btn.style.color = '#f5ebe0';
        });
        const activeBtn = document.getElementById('btn-' + type);
        activeBtn.style.background = '#d4a373';
        activeBtn.style.color = '#1e1412';
    }

    function validateSubmission() {
        const cid = document.getElementById('connectionSelector').value;
        const type = document.getElementById('selectedType').value;
        if (!cid || !type) {
            alert("Please select a connection and an interaction type!");
            return false;
        }
        return true;
    }

    function sendMobileNotification(title, message) {
        if (typeof Android !== "undefined" && Android.showNotification) {
            Android.showNotification(title, message);
        } else {
            console.log("Not in app: " + title + " - " + message);
        }
    }
</script>

</body>
</html>