<?php
require_once '../config/db.php';
session_start();

$eid = $_GET['id'];
$uid = $_SESSION['user_id'] ?? 'user1';

$stmt = $conn->prepare("SELECT * FROM events WHERE eventID = ? AND ownerID = ?");
$stmt->bind_param("is", $eid, $uid);
$stmt->execute();
$current = $stmt->get_result()->fetch_assoc();

if (!$current) die("Unauthorized.");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title      = $_POST['title'];
    $antagonist = $_POST['antagonist'];
    $topic      = $_POST['topic'];
    $date       = !empty($_POST['date']) ? $_POST['date'] : null;
    $cid        = $current['connectionID'];

    $update = $conn->prepare("UPDATE events SET EventTitle = ?, Antagonist = ?, FollowUpTopic = ?, EventDate = ? WHERE eventID = ? AND ownerID = ?");
    $update->bind_param("ssssis", $title, $antagonist, $topic, $date, $eid, $uid);
    $update->execute();
    
    header("Location: view_events.php?id=" . $cid);
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Update Event</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #1e1412; color: #f5ebe0; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; }
        .card { background: #3d2b1f; padding: 2rem; border-radius: 15px; width: 400px; border: 1px solid #d4a373; }
        label { font-size: 0.8rem; color: #d4a373; display: block; margin-top: 10px; }
        input, textarea { width: 100%; padding: 12px; margin: 5px 0 15px 0; background: #2d1f18; border: 1px solid #5c4033; color: white; border-radius: 8px; box-sizing: border-box; outline: none; }
        input:focus { border-color: #d4a373; }
        .btn { width: 100%; padding: 12px; background: #d4a373; color: #1e1412; border: none; border-radius: 8px; font-weight: bold; cursor: pointer; margin-top: 10px; }
    </style>
</head>
<body>
    <div class="card">
        <h3>Edit Event Detail</h3>
        <form method="POST">
            <label>Event Title</label>
            <input type="text" name="title" value="<?php echo htmlspecialchars($current['EventTitle']); ?>" required>

            <label>Related Person</label>
            <input type="text" name="antagonist" value="<?php echo htmlspecialchars($current['Antagonist']); ?>" required>

            <label>AI Analysis / Topic</label>
            <textarea name="topic" rows="3"><?php echo htmlspecialchars($current['FollowUpTopic']); ?></textarea>
            
            <label>Date</label>
            <input type="date" name="date" value="<?php echo $current['EventDate']; ?>">
            
            <button type="submit" class="btn">Save Changes</button>
        </form>
        <p style="text-align: center; margin-top: 15px;">
            <a href="view_events.php?id=<?php echo $current['connectionID']; ?>" style="color: #d4a373; text-decoration: none; font-size: 0.9rem;">Cancel</a>
        </p>
    </div>
</body>
</html>