<?php
/**
 * WhatsApp Helper Functions
 * Contains the sendWhatsappMessage function for the leave management webhook
 */

// Include Joomla framework if needed
// require_once 'path/to/joomla/framework.php';

function sendWhatsappMessage($phonenumber, $templatename, $filepath, $filename, $parameters, $headertext='', $buttonsArray=array()) {
    $db = null; // Initialize database connection
    
    // If using Joomla, uncomment these lines:
    // $db = &JFactory::getDBO();
    
    // For testing without Joomla, use hardcoded values
    $apilink = "https://your-whatsapp-api-endpoint.com/v1/messages";
    $apikeyval = "your-api-key-here";
    $urlval = "your-url";
    $oncloud = 1;
    $namespace = "your-namespace";
    
    // If using database, uncomment this:
    /*
    $queryapi = "SELECT value FROM base_configtypes WHERE typename = 'whatsapp_credentials'";
    $db->setQuery($queryapi);
    $apivalues = $db->loadResult();
    
    if ($apivalues) {
        $apiexp = explode('~||~', $apivalues);
        $apilink = $apiexp[1] . "messages";
        $apikeyval = $apiexp[2];
        $urlval = $apiexp[3];
        $oncloud = $apiexp[4];
        $namespace = $apiexp[5]; // Add namespace to your database
    }
    */
    
    if ($oncloud == 1) {
        $metacloud = '"messaging_product": "whatsapp",';
    }

    $postcommand = "curl --location --request POST -k " . $apilink . " \
    --header 'D360-API-KEY: " . $apikeyval . "' \
    --header 'Content-Type: application/json' \
    --data-raw '{ " . $metacloud . "
        \"recipient-type\": \"individual\",
        \"to\": \"$phonenumber\",
        \"type\": \"template\",
        \"template\":{
            \"namespace\": \"$namespace\",
            \"name\": \"$templatename\",
            \"language\":{
                \"code\": \"en\",
                \"policy\": \"deterministic\"
            },
            \"components\": [";
    
    if($filepath != '' && $filename != '') {
        //this is for file type document we have document url and document name
        $postcommand .= "{
            \"type\": \"header\",
            \"parameters\":[
                {
                    \"type\": \"document\",
                    \"document\":{
                        \"link\": \"" . $filepath . "\",
                        \"filename\":\"" . $filename . "\"
                    }
                }
            ]
        },";
    } else if($filepath != '' && $filename == '') {
        //this is for type images we have only file url but not name
        $postcommand .= "{
            \"type\": \"header\",
            \"parameters\":[
                {
                    \"type\": \"image\",
                    \"image\":{
                        \"link\": \"" . $filepath . "\"
                    }
                }
            ]
        },";
    } else if($headertext != '') {
        $postcommand .= "{
            \"type\": \"header\",
            \"parameters\":[
                {
                    \"type\": \"text\",
                    \"text\": \"" . $headertext . "\"
                }
            ]
        },";
    }
    
    $temp = "";
    for($i = 0; $i < count($parameters); $i++) {
        $temp .= "{
            \"type\": \"text\",
            \"text\": \"" . $parameters[$i] . "\"
        },";
    }
    $temp = trim($temp, ',');

    $postcommand .= "{
        \"type\":\"body\",
        \"parameters\":[" . $temp . "]
    }";

    if (!empty($buttonsArray)) {
        $postcommand .= ",";

        foreach ($buttonsArray as $key => $button) {
            $buttonType = $button['type'];
            $payload = $button['payload'];
            $label = $button['label'];

            if ($buttonType == 'quick_reply') {
                // Quick reply button
                $postcommand .= "{
                    \"type\": \"button\",
                    \"sub_type\": \"quick_reply\",
                    \"index\": \"$key\",
                    \"parameters\": [
                        {
                            \"type\": \"payload\",
                            \"payload\": \"$payload\"
                        }
                    ]
                },";
            } elseif ($buttonType == 'url') {
                // URL button
                $postcommand .= "{
                    \"type\": \"button\",
                    \"sub_type\": \"url\",
                    \"index\": \"$key\",
                    \"parameters\": [
                        {
                            \"type\": \"payload\",
                            \"payload\": \"$payload\"
                        }
                    ]
                },";
            } elseif ($buttonType == 'phone_number') {
                // Phone number button
                $postcommand .= "{
                    \"type\": \"button\",
                    \"sub_type\": \"phone_number\",
                    \"index\": \"$key\",
                    \"parameters\": [
                        {
                            \"type\": \"payload\",
                            \"payload\": \"$payload\"
                        }
                    ]
                },";
            } elseif ($buttonType == 'copy_code') {
                // Copy code button
                $postcommand .= "{
                    \"type\": \"button\",
                    \"sub_type\": \"copy_code\",
                    \"index\": \"$key\",
                    \"parameters\": [
                        {
                            \"type\": \"payload\",
                            \"payload\": \"$payload\"
                        }
                    ]
                },";
            }
        }
        $postcommand = rtrim($postcommand, ',');
    }

    $postcommand .= "]
        }
    }'";

    // For debugging, you can log the command
    error_log("WhatsApp Command: " . $postcommand);
    
    $output = shell_exec($postcommand);
    $outputjson = json_decode($output);
    
    if($outputjson && isset($outputjson->messages[0]->id)) {
        return 'true~~||~~' . $output . '~~||~~' . $outputjson->messages[0]->id;
    } else {
        return $outputjson;
    }
}

// Alternative function for simple text messages (not templates)
function sendSimpleWhatsAppMessage($phonenumber, $message, $buttons = array()) {
    $apilink = "https://your-whatsapp-api-endpoint.com/v1/messages";
    $apikeyval = "your-api-key-here";
    
    $data = [
        "messaging_product" => "whatsapp",
        "recipient_type" => "individual",
        "to" => $phonenumber,
        "type" => "text",
        "text" => [
            "body" => $message
        ]
    ];
    
    // Add interactive buttons if provided
    if (!empty($buttons)) {
        $data["type"] = "interactive";
        $data["interactive"] = [
            "type" => "button",
            "body" => [
                "text" => $message
            ],
            "action" => [
                "buttons" => []
            ]
        ];
        
        foreach ($buttons as $index => $button) {
            $data["interactive"]["action"]["buttons"][] = [
                "type" => "reply",
                "reply" => [
                    "id" => "btn_" . $index,
                    "title" => $button
                ]
            ];
        }
    }
    
    $postcommand = "curl --location --request POST -k " . $apilink . " \
    --header 'D360-API-KEY: " . $apikeyval . "' \
    --header 'Content-Type: application/json' \
    --data-raw '" . json_encode($data) . "'";
    
    $output = shell_exec($postcommand);
    $outputjson = json_decode($output);
    
    if($outputjson && isset($outputjson->messages[0]->id)) {
        return 'true~~||~~' . $output . '~~||~~' . $outputjson->messages[0]->id;
    } else {
        return $outputjson;
    }
}
?>