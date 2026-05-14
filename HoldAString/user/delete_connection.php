<?php
require_once '../config/db.php';
session_start();

if (isset($_GET['id']) && isset($_SESSION['user_id'])) {
    $cid = $_GET['id'];
    $uid = $_SESSION['user_id'];

    $stmt = $conn->prepare("DELETE FROM CONNECTIONS WHERE connectionID = ? AND ownerID = ?");
    $stmt->bind_param("is", $cid, $uid);
    $stmt->execute();
}

header("Location: view_connections.php");
exit();