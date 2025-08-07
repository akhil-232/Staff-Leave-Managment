<?php
/**
 * Test script for the Leave Management Webhook
 * Simulates the complete conversation flow
 */

// Test the webhook with a series of messages
function testWebhook($baseUrl, $messages) {
    echo "🧪 Testing Leave Management Webhook\n";
    echo "=====================================\n\n";
    
    $sessionId = uniqid();
    
    foreach ($messages as $index => $message) {
        echo "Step " . ($index + 1) . ": Sending '$message'\n";
        
        // Prepare the request
        $data = json_encode(['message' => $message]);
        
        // Create context for the request
        $context = stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => [
                    'Content-Type: application/json',
                    'Content-Length: ' . strlen($data)
                ],
                'content' => $data
            ]
        ]);
        
        // Make the request
        $response = file_get_contents($baseUrl, false, $context);
        
        if ($response === false) {
            echo "❌ Error: Could not connect to webhook\n\n";
            return false;
        }
        
        // Parse the response
        $result = json_decode($response, true);
        
        if ($result) {
            echo "✅ Response: " . $result['bot_response'] . "\n";
            
            if (!empty($result['buttons'])) {
                echo "📋 Buttons: " . implode(', ', $result['buttons']) . "\n";
            }
            
            echo "📍 Stage: " . $result['current_stage'] . "\n";
        } else {
            echo "❌ Error: Invalid JSON response\n";
            echo "Raw response: " . $response . "\n";
        }
        
        echo "\n" . str_repeat("-", 50) . "\n\n";
        
        // Small delay between requests
        usleep(500000); // 0.5 seconds
    }
    
    echo "✅ Test completed!\n";
    return true;
}

// Define the test conversation
$testMessages = [
    'Hi',
    'Raise a Leave Request',
    'Casual Leave (CL)',
    '23-08-2025',
    '25-08-2025',
    'Family vacation',
    'Yes',
    'Main Menu',
    'View leave history',
    'Main Menu'
];

// Test with local webhook
$webhookUrl = 'http://localhost/leave_management_webhook.php';

echo "Starting webhook test...\n";
echo "Webhook URL: $webhookUrl\n\n";

// Check if webhook is accessible
$headers = get_headers($webhookUrl);
if ($headers === false) {
    echo "❌ Error: Cannot access webhook at $webhookUrl\n";
    echo "Please make sure:\n";
    echo "1. The webhook file is in the correct location\n";
    echo "2. Your web server is running\n";
    echo "3. PHP is properly configured\n";
    exit(1);
}

// Run the test
testWebhook($webhookUrl, $testMessages);

echo "\n🎉 Test script completed!\n";
echo "You can also test the webhook using:\n";
echo "1. The web interface: test_interface.html\n";
echo "2. Direct API calls with curl or Postman\n";
echo "3. Integration with WhatsApp Business API\n";
?>