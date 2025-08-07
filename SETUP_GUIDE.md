# Setup Guide: Leave Management Webhook

## 📁 Files to Upload

Upload these files to your web server:
- `whatsapp_webhook.php` - Main webhook script
- `Leave_Management_Webhook.postman_collection.json` - Postman collection

## 🚀 Quick Setup

### 1. Upload Webhook
1. Upload `whatsapp_webhook.php` to your web server
2. Note the URL: `http://your-domain.com/whatsapp_webhook.php`

### 2. Import Postman Collection
1. Open Postman
2. Click "Import" button
3. Select the `Leave_Management_Webhook.postman_collection.json` file
4. Update the `webhook_url` variable with your actual URL

### 3. Test the Webhook

#### Option A: Use Postman Collection
1. Open the imported collection
2. Run requests 1-9 in sequence to test the full conversation
3. Each request simulates a WhatsApp message

#### Option B: Manual Testing
```bash
# Test with curl
curl -X POST http://your-domain.com/whatsapp_webhook.php \
  -H "Content-Type: application/json" \
  -d '{"message": "Hi"}'
```

## 📱 Supported Message Formats

The webhook handles multiple formats:

### 1. Simple JSON (for Postman testing)
```json
{
  "message": "Hi"
}
```

### 2. WhatsApp Business API Format
```json
{
  "entry": [
    {
      "changes": [
        {
          "value": {
            "messages": [
              {
                "text": {
                  "body": "Hi"
                }
              }
            ]
          }
        }
      ]
    }
  ]
}
```

### 3. Twilio WhatsApp Format
```
Body=Hi
```

### 4. GET Request (for testing)
```
http://your-domain.com/whatsapp_webhook.php?message=Hi
```

## 🧪 Test Conversation Flow

Follow this sequence in Postman:

1. **"Hi"** → Bot shows main menu
2. **"Raise a Leave Request"** → Bot shows leave types
3. **"Casual Leave (CL)"** → Bot asks for start date
4. **"23-08-2025"** → Bot asks for end date
5. **"25-08-2025"** → Bot asks for reason
6. **"Family vacation"** → Bot shows confirmation
7. **"Yes"** → Bot confirms submission
8. **"Main Menu"** → Bot returns to main menu
9. **"View leave history"** → Bot shows leave history

## 🔧 Expected Responses

Each response will include:
```json
{
  "success": true,
  "bot_response": "Dear Akshay, please choose...",
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

## 🎯 Demo Features

- ✅ State-based conversation flow
- ✅ Button simulation
- ✅ Leave history tracking
- ✅ Personalization (Akshay)
- ✅ Multiple input formats
- ✅ Session management
- ✅ Error handling

## 🚨 Troubleshooting

### Common Issues:

1. **404 Error**: Check if the file is uploaded correctly
2. **500 Error**: Check PHP error logs
3. **Session not working**: Ensure sessions are enabled
4. **CORS errors**: The webhook includes CORS headers

### Debug Mode:
Add `?debug=1` to see detailed information:
```
http://your-domain.com/whatsapp_webhook.php?message=Hi&debug=1
```

## 📞 Integration Ready

Once tested with Postman, the webhook is ready for:
- WhatsApp Business API
- Twilio WhatsApp
- Any webhook-based messaging platform

The webhook will automatically detect the message format and respond appropriately!