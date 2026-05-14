<?php
session_start();
require_once '../config/db.php';
require_once '../config/ai_config.php'; 
require_once '../algorithms/entity_extractor.php';
require_once '../ai/ai_prm.php';

global $apiKey; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userInput    = trim($_POST['user_input'] ?? '');
    $connectionID = $_POST['target_connection_id'] ?? null;
    $type         = $_POST['interaction_type'] ?? 'general';
    $uid          = strtolower($_SESSION['user_id'] ?? 'user1');

    if (!empty($userInput)) {
        $aiEvents = AI_PRM::getStructuredEvents($userInput, $apiKey);

        if (!empty($aiEvents)) {
            $sql = "INSERT INTO events (ownerID, connectionID, Antagonist, EventTitle, EventDate, FollowUpTopic, SuggestedQuestions) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $successCount = 0;

            foreach ($aiEvents as $event) {
                $nlp = EntityExtractor::extract($event['Antagonist'] . " " . $event['EventTitle']);
                
                $antagonist = ($nlp['Antagonist'] !== 'Someone') ? $nlp['Antagonist'] : $event['Antagonist'];
                $eventDate  = $event['EventDate'] ?? date('Y-m-d');
                $title      = substr($event['EventTitle'], 0, 95);
                $followUp   = substr($event['FollowUpTopic'], 0, 195);
                $questions  = is_array($event['Questions']) ? implode('; ', $event['Questions']) : $event['Questions'];

                $stmt->bind_param("sisssss", $uid, $connectionID, $antagonist, $title, $eventDate, $followUp, $questions);
                if ($stmt->execute()) { $successCount++; }
            }

            if ($successCount > 0) {
                $updateSql = "UPDATE strings SET stringHealth = LEAST(stringHealth + 15.00, 100.00), lastInteraction = NOW() WHERE connectionID = ?";
                $upStmt = $conn->prepare($updateSql);
                $upStmt->bind_param("i", $connectionID);
                $upStmt->execute();
                
                header("Location: dashboard.php?status=success&count=$successCount");
                exit();
            }
        }
    } 
    
    else {
        $boostMap = ['text' => 2.00, 'call' => 5.00, 'hangout' => 10.00];
        $healthBoost = $boostMap[$type] ?? 1.00;

        $updateSql = "UPDATE strings SET stringHealth = LEAST(stringHealth + ?, 100.00), lastInteraction = NOW() WHERE connectionID = ?";
        $upStmt = $conn->prepare($updateSql);
        $upStmt->bind_param("di", $healthBoost, $connectionID);
        
        if ($upStmt->execute()) {
            header("Location: dashboard.php?status=quick_success");
            exit();
        }
    }

    header("Location: dashboard.php?status=error");
    exit();
}