# Staff Leave Management Chatbot Webhook

A PHP-based webhook that simulates a WhatsApp-style chatbot for staff leave management workflow. This demo follows the exact conversation flow shown in the provided screenshots.

## Features

- **State-based conversation flow** - Tracks user progress through different stages
- **Multiple input formats** - Accepts JSON, POST, and GET requests
- **Button simulation** - Provides clickable options at each step
- **Leave history tracking** - Maintains user's leave request history
- **Personalization** - Uses dummy user data (Akshay)
- **CORS support** - Works with web-based interfaces
- **Session management** - Maintains conversation state

## Conversation Flow

The chatbot follows this exact flow:

1. **Initial Greeting**
   - User: "Hi" or "Apply for a leave"
   - Bot: Shows main menu options

2. **Leave Request Process**
   - User selects leave type (Casual Leave, 1 Hour Permission, On Duty)
   - Bot asks for start date (dd-mm-yyyy format)
   - Bot asks for end date (dd-mm-yyyy format)
   - Bot asks for reason
   - Bot shows confirmation with approval options

3. **Approval Process**
   - User confirms with "Yes" or "No"
   - Bot submits application and returns to main menu

4. **Leave History**
   - User can view their leave history
   - Shows pending and approved requests

## Files

- `leave_management_webhook.php` - Main webhook script
- `test_interface.html` - Web-based test interface
- `README.md` - This documentation

## Setup Instructions

### 1. Server Requirements

- PHP 7.0 or higher
- Web server (Apache, Nginx, or built-in PHP server)
- Session support enabled

### 2. Installation

1. Upload the files to your web server
2. Ensure the web server can execute PHP files
3. Make sure sessions are enabled in PHP

### 3. Testing

#### Option A: Web Interface (Recommended)
1. Open `test_interface.html` in your browser
2. The interface will automatically connect to the webhook
3. Use the "🧪 Run Demo" button to test the full conversation flow
4. Or manually type messages to test individual responses

#### Option B: Direct API Testing
```bash
# Test with curl
curl -X POST http://your-domain.com/leave_management_webhook.php \
  -H "Content-Type: application/json" \
  -d '{"message": "Hi"}'
```

#### Option C: GET Request (for testing)
```
http://your-domain.com/leave_management_webhook.php?message=Hi
```

## API Reference

### Request Format

The webhook accepts multiple input formats:

**JSON POST:**
```json
{
  "message": "Hi"
}
```

**Form POST:**
```
message=Hi
```

**GET Parameter:**
```
?message=Hi
```

### Response Format

```json
{
  "success": true,
  "message": "Success",
  "bot_response": "Dear Akshay, please choose any of the options listed below:",
  "buttons": ["Raise a Leave Request", "View leave history"],
  "current_stage": "main_menu",
  "user_data": {
    "name": "Akshay",
    "leave_type": "",
    "start_date": "",
    "end_date": "",
    "reason": ""
  }
}
```

### Supported Messages

#### Initial Stage
- `"Hi"`, `"Hello"`, `"Apply for a leave"`

#### Main Menu
- `"Raise a Leave Request"`, `"Apply for a leave"`
- `"View leave history"`, `"Check leave history"`

#### Leave Type Selection
- `"Casual Leave (CL)"`, `"Casual Leave"`, `"CL"`
- `"1 Hour Permission"`, `"1 Hour"`, `"Hour Permission"`
- `"On Duty (OD)"`, `"On Duty"`, `"OD"`
- `"Main Menu"`

#### Date Input
- Format: `dd-mm-yyyy` (e.g., `23-08-2025`)

#### Approval
- `"Yes"`, `"No"`

## Integration with WhatsApp

To integrate with WhatsApp Business API:

1. **Twilio WhatsApp Sandbox:**
   ```php
   // In your webhook, handle Twilio's incoming webhook format
   $user_message = $_POST['Body'] ?? '';
   ```

2. **WhatsApp Business API:**
   ```php
   // Handle WhatsApp's webhook format
   $user_message = $input['entry'][0]['changes'][0]['value']['messages'][0]['text']['body'] ?? '';
   ```

3. **Send responses back to WhatsApp:**
   ```php
   // Use Twilio's API to send messages back
   $twilio = new Client($account_sid, $auth_token);
   $message = $twilio->messages->create(
       $from_number,
       [
           'from' => 'whatsapp:' . $twilio_number,
           'body' => $bot_response
       ]
   );
   ```

## Customization

### Change User Name
Edit line 47 in `leave_management_webhook.php`:
```php
'name' => 'Your Name Here',
```

### Add More Leave Types
Add new cases in the `leave_type_selection` stage:
```php
elseif (in_array($user_message, ['sick leave', 'sl'])) {
    $_SESSION['user_data']['leave_type'] = 'Sick Leave (SL)';
    // ... rest of the code
}
```

### Modify Leave History
Edit the `leave_history` array in the session initialization:
```php
'leave_history' => [
    [
        'start_date' => '23-08-2025',
        'end_date' => '25-08-2025',
        'status' => 'Pending Approval'
    ],
    // Add more entries...
]
```

## Error Handling

The webhook includes basic error handling:
- Invalid message format
- Missing message parameter
- Invalid date format
- Invalid stage transitions

## Security Considerations

For production use:
1. Implement proper session management
2. Add authentication/authorization
3. Validate and sanitize all inputs
4. Use HTTPS for all communications
5. Implement rate limiting
6. Add logging for debugging

## Demo Conversation

Here's a complete demo conversation:

```
User: Hi
Bot: Dear Akshay, please choose any of the options listed below:
     [Raise a Leave Request] [View leave history]

User: Raise a Leave Request
Bot: Pick the relevant leave type to initiate your request.
     [1 Hour Permission] [Casual Leave (CL)] [On Duty (OD)] [Main Menu]

User: Casual Leave (CL)
Bot: Start date of leave (dd/mm/yyyy):

User: 23-08-2025
Bot: End date of leave (dd/mm/yyyy):

User: 25-08-2025
Bot: State the reason for availing leave.

User: Family vacation
Bot: Dear Akshay, your Casual Leave (CL) request from 23-08-2025 to 25-08-2025 has been successfully registered. Please choose one of the options below to proceed with the approval process.
     [Yes] [No]

User: Yes
Bot: Leave application submitted successfully.
     [Main Menu]

User: Main Menu
Bot: Dear Akshay, please choose any of the options listed below:
     [Raise a Leave Request] [View leave history]

User: View leave history
Bot: Dear Akshay, your recent leave history:
     - 23-08-2025 to 25-08-2025 – *Pending Approval*
     - 17-07-2025 to 17-07-2025 – *Approved*
     [Main Menu]
```

## Troubleshooting

### Common Issues

1. **Session not working**: Ensure PHP sessions are enabled
2. **CORS errors**: The webhook includes CORS headers, but you may need to configure your server
3. **Date format errors**: Use dd-mm-yyyy format (e.g., 23-08-2025)
4. **Button not working**: Make sure to click the exact button text

### Debug Mode

Enable debug mode by checking the response structure:
```json
{
  "success": true,
  "current_stage": "main_menu",
  "user_data": { ... }
}
```

## Future Enhancements

1. **Database Integration**: Store leave requests in a database
2. **Email Notifications**: Send approval notifications
3. **Leave Balance Check**: Verify available leave balance
4. **Manager Approval**: Implement approval workflow
5. **Calendar Integration**: Sync with calendar systems
6. **Multi-language Support**: Add support for multiple languages
7. **Advanced Validation**: More robust date and input validation
8. **Analytics**: Track usage patterns and metrics

## License

This is a demo project. Feel free to modify and use as needed for your leave management system.