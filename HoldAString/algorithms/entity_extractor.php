<?php
class EntityExtractor {
    public static function extract($userInput) {
        $pythonPath = "C:/Users/User/AppData/Local/Python/pythoncore-3.14-64/python.exe";
        $scriptPath = __DIR__ . "/nlp_engine.py";
        
        $command = $pythonPath . " " . escapeshellarg($scriptPath) . " " . escapeshellarg($userInput);
        $output = shell_exec($command);
        
        $nlpData = json_decode($output, true);

        return [
            'Antagonist'   => $nlpData['person'] ?? 'Someone', 
            'EventDate'    => $nlpData['date'] ?? date('Y-m-d'), 
            'OriginalText' => $userInput
        ];
    }
}
