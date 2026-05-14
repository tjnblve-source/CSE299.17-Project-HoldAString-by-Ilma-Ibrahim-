<?php
require_once '../config/db.php';
session_start();
$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['register'])) {
    $uid = trim($_POST['userid']);
    $name = trim($_POST['display_name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $check = $conn->prepare("SELECT userID FROM USERS WHERE userID = ? OR Email = ?");
    $check->bind_param("ss", $uid, $email);
    $check->execute();
    if ($check->get_result()->num_rows > 0) {
        $error = "UserID or Email already exists!";
    } else {
        $hashed_pass = password_hash($password, PASSWORD_DEFAULT);
        $otp = rand(100000, 999999);
        $expiry = date("Y-m-d H:i:s", strtotime("+10 minutes"));
        $isVerified = 0;

        $stmt = $conn->prepare("INSERT INTO USERS (userID, DisplayName, Email, Password, verificationCode, codeExpiryTime, isVerified) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssi", $uid, $name, $email, $hashed_pass, $otp, $expiry, $isVerified);

        if ($stmt->execute()) {
            header("Location: verify.php?userid=" . urlencode($uid));
            exit();
        } else { $error = "Error: " . $stmt->error; }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Register | HoldAString</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #1e1412; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; color: #f5ebe0; }
        .card { background: #3d2b1f; padding: 2rem; border-radius: 15px; box-shadow: 0 8px 30px rgba(0,0,0,0.5); width: 350px; text-align: center; border: 1px solid rgba(255, 255, 255, 0.05); }
        h2 { color: #d4a373; }
        input { width: 100%; padding: 10px; margin: 8px 0; background: #2a1d15; border: 1px solid #4e3629; border-radius: 8px; box-sizing: border-box; color: #f5ebe0; }
        button { background: #d4a373; color: #1e1412; border: none; padding: 12px; border-radius: 8px; cursor: pointer; width: 100%; font-weight: bold; margin-top: 10px; }
        .error { color: #ff6b6b; font-size: 0.8rem; margin-bottom: 10px; }
        a { color: #d4a373; text-decoration: none; font-size: 0.8rem; }
    </style>
</head>
<body>
<div class="card">
    <h2>Join HoldAString</h2>
    <?php if($error) echo "<div class='error'>$error</div>"; ?>
    <form method="POST">
        <input type="text" name="userid" placeholder="Username" required>
        <input type="text" name="display_name" placeholder="Display Name" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit" name="register">Sign Up</button>
    </form>
    <p>Already have an account? <a href="login.php"><strong>Login</strong></a></p>
</div>
</body>
</html>