<?php
require_once '../config/db.php';
session_start();
$error = "";
$uid = $_GET['userid'] ?? ($_POST['userid'] ?? '');

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['verify'])) {
    $entered_otp = trim($_POST['otp']);
    $stmt = $conn->prepare("SELECT verificationCode, codeExpiryTime FROM USERS WHERE userID = ?");
    $stmt->bind_param("s", $uid);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($user = $result->fetch_assoc()) {
        if ($entered_otp === $user['verificationCode'] && date("Y-m-d H:i:s") <= $user['codeExpiryTime']) {
            $update = $conn->prepare("UPDATE USERS SET isVerified = 1, verificationCode = NULL, codeExpiryTime = NULL WHERE userID = ?");
            $update->bind_param("s", $uid);
            $update->execute();
            header("Location: login.php?msg=verified");
            exit();
        } else { $error = "Invalid or expired code."; }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Verify | HoldAString</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #1e1412; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; color: #f5ebe0; }
        .card { background: #3d2b1f; padding: 2rem; border-radius: 15px; box-shadow: 0 8px 30px rgba(0,0,0,0.5); width: 350px; text-align: center; border: 1px solid rgba(255, 255, 255, 0.05); }
        h2 { color: #d4a373; }
        input { width: 100%; padding: 12px; margin: 15px 0; background: #2a1d15; border: 1px solid #4e3629; border-radius: 8px; box-sizing: border-box; color: #f5ebe0; text-align: center; font-size: 1.5rem; letter-spacing: 5px; }
        button { background: #d4a373; color: #1e1412; border: none; padding: 12px; border-radius: 8px; cursor: pointer; width: 100%; font-weight: bold; }
        .error { color: #ff6b6b; font-size: 0.8rem; }
    </style>
</head>
<body>
<div class="card">
    <h2>Verify User</h2>
    <?php if($error) echo "<div class='error'>$error</div>"; ?>
    <form method="POST">
        <input type="hidden" name="userid" value="<?php echo htmlspecialchars($uid); ?>">
        <input type="text" name="otp" placeholder="000000" maxlength="6" required>
        <button type="submit" name="verify">Sign Up Now</button>
    </form>
</div>
</body>
</html>