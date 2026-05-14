<?php
function triggerPhoneAlerts($conn, $uid) {
    if (!$conn || empty($uid)) return;

    echo "\n<!-- Phone Alert System -->\n";
    echo "<script>";

    $healthSql = "SELECT c.Name, s.stringHealth 
                  FROM strings s
                  JOIN connections c ON s.connectionID = c.connectionID
                  WHERE s.ownerID = ? AND s.stringHealth < 50";
    
    $healthStmt = $conn->prepare($healthSql);
    $healthStmt->bind_param("s", $uid);
    $healthStmt->execute();
    $healthResult = $healthStmt->get_result();
    
    while ($row = $healthResult->fetch_assoc()) {
        $name = addslashes($row['Name']);
        $val = round($row['stringHealth']);
        echo "setTimeout(function() {
                if(window.Android) Android.showNotification('Weakening String', '$name is at $val% health!');
              }, 500);";
    }
    $healthStmt->close();

   $eventSql = "SELECT c.Name, e.EventTitle 
                 FROM events e
                 JOIN connections c ON e.connectionID = c.connectionID
                 WHERE c.ownerID = ? 
                 AND DATE(e.EventDate) = CURDATE()";
    
    $eventStmt = $conn->prepare($eventSql);
    if($eventStmt) {
        $eventStmt->bind_param("s", $uid);
        $eventStmt->execute();
        $eventResult = $eventStmt->get_result();

        while ($event = $eventResult->fetch_assoc()) {
            $conName = addslashes($event['Name']);
            $title = addslashes($event['EventTitle']);
            
            echo "setTimeout(function() {
                    if(window.Android) Android.showNotification('Today\'s Event: $conName', '$title');
                  }, 1000);";
        }
        $eventStmt->close();
    }

    echo "</script>\n";
}