<?php
require_once '../config/db.php';
session_start();

$cid = $_GET['id'];
$uid = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT * FROM CONNECTIONS WHERE connectionID = ? AND ownerID = ?");
$stmt->bind_param("is", $cid, $uid);
$stmt->execute();
$current = $stmt->get_result()->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $newName = $_POST['name'];
    $newType = $_POST['relationshipType'];
    $newBday = !empty($_POST['birthday']) ? $_POST['birthday'] : null;
    $newBio = $_POST['bio'];
    
    $update = $conn->prepare("UPDATE CONNECTIONS SET Name = ?, RelationshipType = ?, Birthday = ?, Bio = ? WHERE connectionID = ? AND ownerID = ?");
    $update->bind_param("ssssis", $newName, $newType, $newBday, $newBio, $cid, $uid);
    $update->execute();
    header("Location: view_connections.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Connection</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #1e1412; color: #f5ebe0; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; }
        .card { background: #3d2b1f; padding: 2rem; border-radius: 15px; width: 350px; border: 1px solid #d4a373; }
        input, select, textarea { width: 100%; padding: 10px; margin: 8px 0; background: #2d1f18; border: 1px solid #5c4033; color: white; border-radius: 8px; box-sizing: border-box; }
        .btn { width: 100%; padding: 12px; background: #d4a373; color: #1e1412; border: none; border-radius: 8px; font-weight: bold; cursor: pointer; }
    </style>
</head>
<body>
    <div class="card">
        <h3>Edit Details</h3>
        <form method="POST">
            <input type="text" name="name" value="<?php echo htmlspecialchars($current['Name']); ?>">
            <select name="relationshipType">
                <option value="Friend" <?php if($current['RelationshipType']=='Friend') echo 'selected'; ?>>Friend</option>
                <option value="Family" <?php if($current['RelationshipType']=='Family') echo 'selected'; ?>>Family</option>
                <option value="Partner" <?php if($current['RelationshipType']=='Partner') echo 'selected'; ?>>Partner</option>
                <option value="Colleague" <?php if($current['RelationshipType']=='Colleague') echo 'selected'; ?>>Colleague</option>
            </select>
            <input type="date" name="birthday" value="<?php echo $current['Birthday']; ?>">
            <textarea name="bio" rows="2"><?php echo htmlspecialchars($current['Bio']); ?></textarea>
            <button type="submit" class="btn">Update Connection</button>
        </form>
    </div>
</body>
</html>