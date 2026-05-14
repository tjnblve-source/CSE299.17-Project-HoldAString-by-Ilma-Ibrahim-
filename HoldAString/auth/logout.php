<?php
require_once '../config/db.php';
session_start();

if (isset($_COOKIE['remember_token'])) {
    $token = $_COOKIE['remember_token'];
    
    $stmt = $conn->prepare("DELETE FROM SESSIONS WHERE sessionToken = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $stmt->close();

    setcookie("remember_token", "", time() - 3600, "/", "", false, true);
}

$_SESSION = array();
session_destroy();

header("Location: ../auth/login.php");
exit();