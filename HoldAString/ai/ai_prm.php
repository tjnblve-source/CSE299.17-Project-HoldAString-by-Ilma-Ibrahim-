<?php
require_once '../config/ai_config.php';

class AI_PRM {
    public static function getStructuredEvents($userInput, $apiKey) {
        $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-flash-latest:generateContent?key=" . $apiKey;

        $promptText = "
        Task: Break this user interaction log into a JSON array of distinct events.
        Questions are related to FollowUpTopic and should be directed toward the Connection.
        Questions can also be topics the user can bring up to show that they care or want to know further.
        Moreover, the user can also ask the connection out on a hangout depending on the Event.
        Input: '{$userInput}'
        
        Return ONLY a JSON array:
        [{
          \"Antagonist\": \"Name or Role\",
          \"EventTitle\": \"Event title\",
          \"EventDate\": \"YYYY-MM-DD\",
          \"FollowUpTopic\": \"1 line summary\",
          \"Questions\": [\"Q1\"]
        }]";

        $data = [
            "contents" => [
                ["parts" => [["text" => $promptText]]]
            ]
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);

        $result = json_decode($response, true);
        $rawOutput = $result['candidates'][0]['content']['parts'][0]['text'] ?? '';

        $startPos = strpos($rawOutput, '[');
        $endPos = strrpos($rawOutput, ']');

        if ($startPos !== false && $endPos !== false) {
            $cleanJSON = substr($rawOutput, $startPos, ($endPos - $startPos) + 1);
            return json_decode($cleanJSON, true) ?? [];
        }
        return [];
    }
}