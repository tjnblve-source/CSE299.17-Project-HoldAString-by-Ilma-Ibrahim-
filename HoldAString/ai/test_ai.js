import http from 'k6/http';
import { check, sleep } from 'k6';

export let options = {
    vus: 1, 
    duration: '5s', 
};

export default function () {
    const url = 'http://localhost/holdastring/ai/ai_prm.php';
    
    const payload = JSON.stringify({
        connection_name: 'Mary',
        message: 'I talked to Mary. Her cat Luna has surgery next Wednesday. Also, her parents anniversary is April 1st and she needs a gift.'
    });

    const params = {
        headers: {
            'Content-Type': 'application/json',
        },
    };

    const res = http.post(url, payload, params);

    if (res.status === 200) {
        const data = JSON.parse(res.body);
        console.log("--- AI EXTRACTION REPORT ---");
        
        data.forEach((event, index) => {
            console.log(`Event #${index + 1}: ${event.EventTitle}`);
            console.log(`  - Antagonist: ${event.Antagonist}`);
            console.log(`  - Date: ${event.EventDate}`);
            console.log(`  - Follow-up: ${event.FollowUpTopic}`);
            console.log(`  - Suggested Question: "${event.SuggestedQuestions}"`); 
            console.log("----------------------------");
        });
    }

    const data = JSON.parse(res.body);

    check(res, {
        '1. HTTP 200': (r) => r.status === 200,
        '2. Is Valid Array': (r) => Array.isArray(data),
        '3. At least one event found': (r) => data.length >= 1,
        '4. First event has a Title': (r) => data[0].EventTitle !== undefined,
    });

    sleep(1);
}