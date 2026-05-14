<?php
require_once '../config/db.php';
session_start();

if (isset($_POST['cid']) && isset($_POST['type']) && isset($_SESSION['user_id'])) {
    $cid = $_POST['cid'];
    $type = $_POST['type'];

    switch ($type) {
        case 'text':
            $boost = 2.00;
            break;
        case 'call':
            $boost = 5.00;
            break;
        case 'hangout':
            $boost = 10.00;
            break;
        default:
            $boost = 1.00;
    }

    $stmt = $conn->prepare("UPDATE strings SET stringHealth = LEAST(stringHealth + ?, 100.00) WHERE connectionID = ?");
    $stmt->bind_param("di", $boost, $cid);
    
    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "new_boost" => $boost]);
    } else {
        echo json_encode(["status" => "error"]);
    }
}
exit();