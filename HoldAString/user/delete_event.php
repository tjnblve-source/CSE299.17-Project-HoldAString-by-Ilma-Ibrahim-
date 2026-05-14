<?php
require_once '../config/db.php';
session_start();

if (isset($_GET['id']) && isset($_SESSION['user_id']) && isset($_GET['cid'])) {
    $eid = $_GET['id'];
    $uid = $_SESSION['user_id'];
    $cid = $_GET['cid'];

    $stmt = $conn->prepare("DELETE FROM events WHERE eventID = ? AND ownerID = ?");
    $stmt->bind_param("is", $eid, $uid);
    
    if ($stmt->execute()) {
        $conn->query("SET @count = 0;");
        $conn->query("UPDATE events SET eventID = (@count := @count + 1);");
        $conn->query("ALTER TABLE events AUTO_INCREMENT = 1;");
    }
    
    header("Location: view_events.php?id=" . $cid);
} else {
    header("Location: dashboard.php");
}
exit();