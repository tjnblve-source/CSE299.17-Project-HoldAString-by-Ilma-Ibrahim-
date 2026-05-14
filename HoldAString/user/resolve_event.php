<?php
require_once '../config/db.php';
session_start();

$eid = $_GET['id'];
$cid = $_GET['cid'];

$stmt = $conn->prepare("UPDATE events SET isResolved = 1 WHERE eventID = ?");
$stmt->bind_param("i", $eid);

if ($stmt->execute()) {
    $update = $conn->prepare("UPDATE strings SET stringHealth = LEAST(stringHealth + 20.00, 100.00), lastInteraction = NOW() WHERE connectionID = ?");
    $update->bind_param("i", $cid);
    $update->execute();
}

header("Location: view_events.php?id=" . $cid . "&status=resolved");
exit();