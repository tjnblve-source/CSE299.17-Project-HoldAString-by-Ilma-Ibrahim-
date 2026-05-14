<?php
function applyHealthDecay($conn, $uid) {
    $sql = "UPDATE strings 
            SET stringHealth = GREATEST(stringHealth - (FLOOR(DATEDIFF(NOW(), lastInteraction) / 7) * 10), 0)
            WHERE ownerID = ? AND lastInteraction < DATE_SUB(NOW(), INTERVAL 7 DAY)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $uid);
    $stmt->execute();
    $stmt->close();
}
?>