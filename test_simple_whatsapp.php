<?php
/**
 * Test script for simple WhatsApp messages (no templates)
 * Demonstrates sending text messages and interactive buttons
 */

// Include the helper file
require_once 'whatsapp_helper.php';

// Test phone number (change this to your test number)
$test_phone = '+919876543210';

echo "🧪 Testing Simple WhatsApp Messages (No Templates)\n";
echo "==================================================\n\n";

// Test 1: Simple text message
echo "1. Testing simple text message...\n";
$result1 = sendSimpleWhatsAppMessage($test_phone, "Hello! This is a simple text message without any template.");
echo "Result: " . $result1 . "\n\n";

// Test 2: Message with buttons
echo "2. Testing message with buttons...\n";
$buttons = array('Raise a Leave Request', 'View leave history');
$result2 = sendSimpleWhatsAppMessage($test_phone, "Dear Akshay, please choose any of the options listed below:", $buttons);
echo "Result: " . $result2 . "\n\n";

// Test 3: Leave type selection
echo "3. Testing leave type selection...\n";
$leave_buttons = array('1 Hour Permission', 'Casual Leave (CL)', 'On Duty (OD)');
$result3 = sendSimpleWhatsAppMessage($test_phone, "Pick the relevant leave type to initiate your request.", $leave_buttons);
echo "Result: " . $result3 . "\n\n";

// Test 4: Confirmation buttons
echo "4. Testing confirmation buttons...\n";
$confirm_buttons = array('Yes', 'No');
$result4 = sendSimpleWhatsAppMessage($test_phone, "Please confirm your leave request:", $confirm_buttons);
echo "Result: " . $result4 . "\n\n";

echo "✅ All tests completed!\n";
echo "\n📱 Expected WhatsApp Messages:\n";
echo "1. Simple text: 'Hello! This is a simple text message...'\n";
echo "2. With buttons: 'Dear Akshay, please choose...' + 2 buttons\n";
echo "3. Leave types: 'Pick the relevant leave type...' + 3 buttons\n";
echo "4. Confirmation: 'Please confirm your leave request:' + 2 buttons\n";

echo "\n🔧 To test with your webhook:\n";
echo "curl -X POST http://your-domain.com/whatsapp_webhook.php \\\n";
echo "  -H 'Content-Type: application/json' \\\n";
echo "  -d '{\"message\": \"Hi\", \"phone_number\": \"$test_phone\"}'\n";
?>