# WhatsApp Integration Setup Guide

## 📱 Overview

This webhook now sends actual WhatsApp messages using your existing `sendWhatsappMessage` function. The webhook will:

1. **Receive messages** from WhatsApp (or Postman for testing)
2. **Process the conversation** based on the leave management flow
3. **Send WhatsApp responses** to the user's phone number
4. **Return JSON response** with status information

## 🔧 Configuration Steps

### 1. Update WhatsApp Helper File

Edit `whatsapp_helper.php` and update these values:

```php
// Replace with your actual WhatsApp API credentials
$apilink = "https://your-whatsapp-api-endpoint.com/v1/messages";
$apikeyval = "your-api-key-here";
$urlval = "your-url";
$namespace = "your-namespace";
```

### 2. Configure Database (Optional)

If using Joomla database, uncomment these lines in `whatsapp_helper.php`:

```php
$db = &JFactory::getDBO();
$queryapi = "SELECT value FROM base_configtypes WHERE typename = 'whatsapp_credentials'";
$db->setQuery($queryapi);
$apivalues = $db->loadResult();
```

### 3. No Template Required! 🎉

The webhook now sends **simple text messages** without requiring any WhatsApp templates. This makes it much easier to set up and use.

**Benefits:**
- ✅ No template approval needed
- ✅ Instant setup
- ✅ More flexible messaging
- ✅ Supports interactive buttons
- ✅ Works immediately

### 4. Update Phone Number

Change the default phone number in `whatsapp_webhook.php`:

```php
'phone_number' => '+919876543210', // Change to your test number
```

## 🧪 Testing with Postman

### Import the Collection
1. Open Postman
2. Import `WhatsApp_Webhook_Testing.postman_collection.json`
3. Update the `webhook_url` variable

### Test Conversation Flow
Run these requests in sequence:

1. **"Hi"** → Sends welcome message to WhatsApp
2. **"Raise a Leave Request"** → Shows leave types
3. **"Casual Leave (CL)"** → Asks for start date
4. **"23-08-2025"** → Asks for end date
5. **"25-08-2025"** → Asks for reason
6. **"Family vacation"** → Shows confirmation
7. **"Yes"** → Confirms submission
8. **"Main Menu"** → Returns to menu
9. **"View leave history"** → Shows history

## 📱 Expected WhatsApp Messages

### Message 1: Welcome (with buttons)
```
Dear Akshay, please choose any of the options listed below:

1. Raise a Leave Request
2. View leave history
```
*Plus 2 interactive buttons*

### Message 2: Leave Types (with buttons)
```
Pick the relevant leave type to initiate your request.

1. 1 Hour Permission
2. Casual Leave (CL)
3. On Duty (OD)
```
*Plus 3 interactive buttons*

### Message 3: Simple Text
```
Start date of leave (dd/mm/yyyy):
```
*Simple text message*

### Message 4: Confirmation (with buttons)
```
Dear Akshay, your Casual Leave (CL) request from 23-08-2025 to 25-08-2025 has been successfully registered. Please choose one of the options below to proceed with the approval process.

1. Yes
2. No
```
*Plus 2 interactive buttons*

## 🔍 Response Format

Each webhook response will include:

```json
{
  "success": true,
  "whatsapp_sent": true,
  "whatsapp_response": "true~~||~~{API response}~~||~~message_id",
  "bot_response": "Dear Akshay, please choose...",
  "buttons": ["Raise a Leave Request", "View leave history"],
  "current_stage": "main_menu",
  "user_data": {
    "name": "Akshay",
    "phone_number": "+919876543210",
    "leave_type": "",
    "start_date": "",
    "end_date": "",
    "reason": ""
  }
}
```

## 🚨 Troubleshooting

### Common Issues:

1. **WhatsApp not sending**: Check API credentials in `whatsapp_helper.php`
2. **Template not found**: Create the `leave_management_response` template
3. **Phone number invalid**: Ensure number is in international format (+91...)
4. **API errors**: Check error logs for curl command details

### Debug Mode:

Add error logging to see the actual curl command:

```php
error_log("WhatsApp Command: " . $postcommand);
```

### Test API Connection:

```bash
# Test simple text message
curl --location --request POST -k "https://your-api-endpoint.com/v1/messages" \
--header 'D360-API-KEY: your-api-key' \
--header 'Content-Type: application/json' \
--data-raw '{
  "messaging_product": "whatsapp",
  "recipient_type": "individual",
  "to": "+919876543210",
  "type": "text",
  "text": {
    "body": "Hello! This is a test message without template."
  }
}'

# Test interactive message with buttons
curl --location --request POST -k "https://your-api-endpoint.com/v1/messages" \
--header 'D360-API-KEY: your-api-key' \
--header 'Content-Type: application/json' \
--data-raw '{
  "messaging_product": "whatsapp",
  "recipient_type": "individual",
  "to": "+919876543210",
  "type": "interactive",
  "interactive": {
    "type": "button",
    "body": {
      "text": "Please choose an option:"
    },
    "action": {
      "buttons": [
        {
          "type": "reply",
          "reply": {
            "id": "btn_0",
            "title": "Option 1"
          }
        },
        {
          "type": "reply",
          "reply": {
            "id": "btn_1",
            "title": "Option 2"
          }
        }
      ]
    }
  }
}'
```

## 📞 Integration Ready

Once configured, the webhook will:

- ✅ Receive WhatsApp messages
- ✅ Process leave management flow
- ✅ Send WhatsApp responses
- ✅ Handle multiple message formats
- ✅ Track conversation state
- ✅ Provide button interactions

## 🎯 Next Steps

1. **Upload files** to your server
2. **Configure API credentials** in `whatsapp_helper.php`
3. **Create WhatsApp template** in your WhatsApp Business account
4. **Test with Postman** using the provided collection
5. **Connect to actual WhatsApp** webhook

The webhook is now ready to send actual WhatsApp messages for the leave management system!