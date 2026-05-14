<?php
require_once '../config/db.php';
session_start();

if (!isset($_SESSION['user_id']) && isset($_COOKIE['remember_token'])) {
    $token = $_COOKIE['remember_token'];

    $stmt = $conn->prepare("SELECT userID FROM SESSIONS WHERE sessionToken = ? AND expiresAt > NOW()");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {
        $_SESSION['user_id'] = $user['userID'];
        header("Location: ../user/dashboard.php");
        exit();
    }
}

$errorMessage = "";

if (isset($_GET['error'])) {
    if ($_GET['error'] == "invalid") {
        $errorMessage = "Invalid email or password. <span style='font-weight: bold; color: #ff6b6b;'>Please try again.</span>";
    } elseif ($_GET['error'] == "notverified") {
        $uid = $_GET['userid'] ?? '';
        $errorMessage = "Please verify your account first. <a href='verify.php?userid=$uid' style='color: #ff6b6b; text-decoration: underline; font-weight: bold;'>Click here to verify.</a>";
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $pass = $_POST['password'];

    $stmt = $conn->prepare("SELECT userID, Password, isVerified FROM USERS WHERE Email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {
        if ($user['isVerified'] == 0) {
            header("Location: login.php?error=notverified&userid=" . $user['userID']);
            exit();
        }

        if (password_verify($pass, $user['Password'])) {
            $uid = $user['userID'];
            $_SESSION['user_id'] = $uid;

            if (isset($_POST['remember_me'])) {
                $token = bin2hex(random_bytes(32));
                $expiry = date('Y-m-d H:i:s', strtotime('+30 days'));

                $ins = $conn->prepare("INSERT INTO SESSIONS (userID, sessionToken, expiresAt) VALUES (?, ?, ?)");
                $ins->bind_param("sss", $uid, $token, $expiry);
                $ins->execute();

                setcookie("remember_token", $token, time() + (86400 * 30), "/", "", false, true);
            }

            header("Location: ../user/dashboard.php");
            exit();
        } else {
            header("Location: login.php?error=invalid");
            exit();
        }
    } else {
        header("Location: login.php?error=invalid");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | HoldAString</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #1e1412; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; color: #f5ebe0; }
        .card { background: #3d2b1f; padding: 2.5rem; border-radius: 15px; box-shadow: 0 8px 30px rgba(0,0,0,0.5); width: 100%; max-width: 350px; text-align: center; border: 1px solid rgba(255, 255, 255, 0.05); box-sizing: border-box; }
        h2 { color: #d4a373; margin-bottom: 1rem; }
        input[type="email"], input[type="password"] { width: 100%; padding: 12px; background: #2a1d15; border: 1px solid #4e3629; border-radius: 8px; box-sizing: border-box; color: #f5ebe0; margin-bottom: 10px; }
        
        /* Updated Checkbox Style */
        .remember-wrap { display: flex; align-items: center; justify-content: flex-start; gap: 8px; margin: 10px 0 15px 0; color: #d4a373; font-size: 0.85rem; cursor: pointer; }
        .remember-wrap input { width: auto; margin: 0; }

        button { background: #d4a373; color: #1e1412; border: none; padding: 12px; border-radius: 8px; cursor: pointer; width: 100%; font-weight: bold; }
        button:hover { background: #e6be94; }
        
        .error-box { background: rgba(255, 107, 107, 0.2); color: #ff6b6b; padding: 10px; border-radius: 8px; border: 1px solid #ff6b6b; margin-bottom: 15px; font-size: 0.85rem; }
        .success-box { color: #d4a373; margin-bottom: 15px; font-size: 0.85rem; }
        a { color: #d4a373; text-decoration: none; font-size: 0.8rem; }
    </style>
</head>
<body>

<div class="card">
    <h2>Welcome Back</h2>
    
    <?php if ($errorMessage): ?>
        <div class="error-box"><?php echo $errorMessage; ?></div>
    <?php endif; ?>

    <?php if (isset($_GET['msg']) && $_GET['msg'] == 'verified'): ?>
        <div class="success-box">Account verified! You can now login.</div>
    <?php endif; ?>

    <form action="login.php" method="POST">
        <input type="email" name="email" placeholder="Email Address" required>
        <input type="password" name="password" placeholder="Password" required>
        
        <label class="remember-wrap">
            <input type="checkbox" name="remember_me"> Remember Me
        </label>

        <button type="submit" name="login">Login</button>
    </form>
    
    <p style="margin-top: 20px;">Need an account? <a href="register.php"><strong>Sign Up</strong></a></p>
</div>

</body>
</html>