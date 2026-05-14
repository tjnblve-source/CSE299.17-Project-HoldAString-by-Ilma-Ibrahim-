<?php
require_once '../config/db.php';
session_start();

if (!isset($_SESSION['user_id']) || !isset($_GET['id'])) {
    header("Location: dashboard.php");
    exit();
}

$cid = $_GET['id'];
$uid = $_SESSION['user_id'];

$decayQuery = "UPDATE strings 
               SET stringHealth = GREATEST(stringHealth - (FLOOR(DATEDIFF(NOW(), lastInteraction) / 7) * 10), 0)
               WHERE connectionID = ? AND lastInteraction < DATE_SUB(NOW(), INTERVAL 7 DAY)";
$dStmt = $conn->prepare($decayQuery);
$dStmt->bind_param("i", $cid);
$dStmt->execute();

$cStmt = $conn->prepare("SELECT c.Name, s.stringHealth 
                         FROM CONNECTIONS c 
                         JOIN strings s ON c.connectionID = s.connectionID 
                         WHERE c.connectionID = ? AND c.ownerID = ?");
$cStmt->bind_param("is", $cid, $uid);
$cStmt->execute();
$data = $cStmt->get_result()->fetch_assoc();

$cName = $data['Name'] ?? "Connection";
$health = $data['stringHealth'] ?? 0;

$stmt = $conn->prepare("SELECT * FROM EVENTS WHERE connectionID = ? AND ownerID = ? ORDER BY isResolved ASC, eventID DESC");
$stmt->bind_param("is", $cid, $uid);
$stmt->execute();
$events = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Events with <?php echo $cName; ?></title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #1e1412; color: #f5ebe0; padding: 40px; line-height: 1.6; }
        .btn-back { color: #d4a373; text-decoration: none; border: 1px solid #d4a373; padding: 8px 15px; border-radius: 5px; margin-bottom: 20px; display: inline-block; transition: 0.3s; }
        .btn-back:hover { background: #d4a373; color: #1e1412; }
        
        /* AI Nudge Styling */
        .nudge-card { background: #4a3728; border-left: 5px solid #d4a373; padding: 20px; border-radius: 12px; margin-bottom: 30px; }
        .health-indicator { font-size: 0.9rem; font-weight: bold; margin-bottom: 10px; display: block; }
        
        .event-card { background: #3d2b1f; padding: 20px; border-radius: 12px; border-left: 5px solid #d4a373; margin-bottom: 20px; position: relative; transition: 0.2s; }
        .event-card.resolved { border-left-color: #6a994e; opacity: 0.8; }
        
        .actions { margin-top: 15px; display: flex; gap: 15px; align-items: center; }
        .actions a { font-size: 0.8rem; text-decoration: none; font-weight: bold; }
        .btn-edit { color: #d4a373; }
        .btn-delete { color: #ff6b6b; }
        .btn-resolve { background: #d4a373; color: #1e1412; padding: 5px 12px; border-radius: 4px; }
        
        .status-badge { font-size: 0.75rem; padding: 3px 8px; border-radius: 10px; background: rgba(0,0,0,0.3); float: right; }
    </style>
</head>
<body>
    <a href="dashboard.php" class="btn-back">← Back to Dashboard</a>
    
    <div style="display: flex; justify-content: space-between; align-items: flex-end;">
        <h1>Strings with <?php echo htmlspecialchars($cName); ?></h1>
        <span class="health-indicator" style="color: <?php echo ($health < 50) ? '#ff6b6b' : '#d4a373'; ?>">
            Current Health: <?php echo number_format($health, 0); ?>%
        </span>
    </div>

    <?php if ($health < 50): ?>
        <div class="nudge-card">
            <h3 style="margin:0; color:#d4a373">💡 AI Nudge Intervention</h3>
            <p style="margin: 10px 0 0 0;">
                <?php
                $nudgeQuery = $conn->prepare("SELECT FollowUpTopic FROM EVENTS WHERE connectionID = ? AND isResolved = 0 ORDER BY EventDate ASC LIMIT 1");
                $nudgeQuery->bind_param("i", $cid);
                $nudgeQuery->execute();
                $nudge = $nudgeQuery->get_result()->fetch_assoc();

                if ($nudge) {
                    echo "Your string is thinning. Why not check in and ask about <strong>\"" . htmlspecialchars($nudge['FollowUpTopic']) . "\"</strong>?";
                } else {
                    echo "The connection is decaying. It's been a while since your last interaction—reach out to keep the string strong!";
                }
                ?>
            </p>
        </div>
    <?php endif; ?>
    
    <?php if($events->num_rows > 0): ?>
        <?php while($e = $events->fetch_assoc()): ?>
            <div class="event-card <?php echo ($e['isResolved']) ? 'resolved' : ''; ?>">
                <span class="status-badge" style="color: <?php echo ($e['isResolved']) ? '#6a994e' : '#d4a373'; ?>">
                    <?php echo ($e['isResolved']) ? '✔ RESOLVED' : '⏳ PENDING'; ?>
                </span>
                
                <h3 style="margin:0; color:#d4a373"><?php echo htmlspecialchars($e['EventTitle']); ?></h3>
                <p style="font-size:0.9rem; opacity:0.8; margin: 10px 0;"><?php echo htmlspecialchars($e['FollowUpTopic']); ?></p>
                
                <div style="font-size:0.8rem">
                    <span>📅 Date: <?php echo $e['EventDate'] ?? 'Unknown'; ?></span> | 
                    <span>👤 Involved: <?php echo htmlspecialchars($e['Antagonist'] ?? 'N/A'); ?></span>
                </div>

                <div style="background:rgba(0,0,0,0.2); padding:10px; border-radius:5px; margin-top:10px">
                    <strong>Suggested Question:</strong> "<?php echo htmlspecialchars($e['SuggestedQuestions']); ?>"
                </div>

                <div class="actions">
                    <?php if (!$e['isResolved']): ?>
                        <a href="resolve_event.php?id=<?php echo $e['eventID']; ?>&cid=<?php echo $cid; ?>" class="btn-resolve">Mark Resolved</a>
                    <?php endif; ?>
                    
                    <a href="update_event.php?id=<?php echo $e['eventID']; ?>" class="btn-edit">Edit</a>
                    
                    <a href="delete_event.php?id=<?php echo $e['eventID']; ?>&cid=<?php echo $cid; ?>" 
                       class="btn-delete" 
                       onclick="return confirm('Delete this event?')">Delete</a>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p style="opacity:0.5">No events extracted yet. Add a brain dump in the dashboard to start the string.</p>
    <?php endif; ?>
</body>
</html>