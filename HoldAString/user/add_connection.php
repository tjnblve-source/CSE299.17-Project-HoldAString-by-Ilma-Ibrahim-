<?php
require_once '../config/db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$success = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['name'])) {
    $name = trim($_POST['name']);
    $relType = $_POST['relationshipType'];
    $birthday = !empty($_POST['birthday']) ? $_POST['birthday'] : null;
    $bio = trim($_POST['bio']);
    $uid = $_SESSION['user_id'];

    $conn->begin_transaction();

    try {
        $sql1 = "INSERT INTO connections (ownerID, Name, RelationshipType, Birthday, Bio) 
                 VALUES (?, ?, ?, ?, ?)";
        $stmt1 = $conn->prepare($sql1);
        $stmt1->bind_param("sssss", $uid, $name, $relType, $birthday, $bio);
        $stmt1->execute();

        $newID = $conn->insert_id;

        $sql2 = "INSERT INTO strings (connectionID, ownerID, stringHealth, lastInteraction) 
                 VALUES (?, ?, 100.00, NOW())";
        $stmt2 = $conn->prepare($sql2);
        $stmt2->bind_param("is", $newID, $uid);
        $stmt2->execute();

        $conn->commit();
        $success = "String created successfully!";
        header("refresh:2;url=dashboard.php");

    } catch (Exception $e) {
        $conn->rollback();
        $error = "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Connection | HoldAString</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #1e1412; color: #f5ebe0; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; }
        .card { background: #3d2b1f; padding: 2.5rem; border-radius: 15px; width: 400px; border: 1px solid rgba(212, 163, 115, 0.2); }
        h2 { color: #d4a373; margin-top: 0; }
        input, select, textarea { width: 100%; padding: 12px; margin: 10px 0; background: #2d1f18; border: 1px solid #5c4033; color: white; border-radius: 8px; box-sizing: border-box; font-family: inherit; }
        .btn { width: 100%; padding: 12px; background: #d4a373; color: #1e1412; border: none; border-radius: 8px; font-weight: bold; cursor: pointer; margin-top: 10px; }
        label { font-size: 0.8rem; color: #d4a373; display: block; margin-top: 10px; }
    </style>
</head>
<body>
    <div class="card">
        <h2>New Connection</h2>
        <?php if($success) echo "<p style='color: #ccd5ae;'>$success</p>"; ?>
        <form method="POST">
            <input type="text" name="name" placeholder="Full Name" required>
            
            <label>Relationship</label>
            <select name="relationshipType">
                <option value="Friend">Friend</option>
                <option value="Family">Family</option>
                <option value="Partner">Partner</option>
                <option value="Colleague">Colleague</option>
            </select>

            <label>Birthday</label>
            <div style="display: flex; gap: 10px;">
                <select name="birth_day" style="flex: 1;">
                    <option value="">Day</option>
                    <?php for($i=1; $i<=31; $i++) echo "<option value='$i'>$i</option>"; ?>
                </select>
                
                <select name="birth_month" style="flex: 2;">
                    <option value="">Month</option>
                    <?php 
                    $months = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
                    foreach($months as $index => $m) echo "<option value='".($index+1)."'>$m</option>"; 
                    ?>
                </select>

                <select name="birth_year" style="flex: 2;">
                    <option value="">Year</option>
                    <?php for($i=date('Y'); $i>=1950; $i--) echo "<option value='$i'>$i</option>"; ?>
                </select>
            </div>

            <label>Short Bio</label>
            <textarea name="bio" rows="2" maxlength="100" placeholder=""></textarea>

            <button type="submit" class="btn">Save String</button>
        </form>
        <a href="dashboard.php" style="color: #d4a373; text-decoration: none; display: block; text-align: center; margin-top: 20px;">← Back</a>
    </div>
</body>
</html>