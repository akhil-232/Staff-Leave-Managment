<?php
/**
 * Staff Leave Management Chatbot Webhook
 * Simulates WhatsApp-style conversation flow for leave requests
 */

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set content type to JSON
header('Content-Type: application/json');

// Handle CORS for web requests
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Get input data
$input = json_decode(file_get_contents('php://input'), true);

// If no JSON input, try POST data
if (!$input) {
    $input = $_POST;
}

// If still no input, try GET data for testing
if (!$input && $_SERVER['REQUEST_METHOD'] === 'GET') {
    $input = $_GET;
}

// Default response structure
$response = [
    'success' => false,
    'message' => 'Invalid request',
    'bot_response' => '',
    'buttons' => [],
    'current_stage' => '',
    'user_data' => []
];

// Session management (in production, use proper session management)
session_start();

// Initialize user session data if not exists
if (!isset($_SESSION['user_data'])) {
    $_SESSION['user_data'] = [
        'name' => 'Akshay',
        'current_stage' => 'initial',
        'leave_type' => '',
        'start_date' => '',
        'end_date' => '',
        'reason' => '',
        'leave_history' => [
            [
                'start_date' => '23-08-2025',
                'end_date' => '25-08-2025',
                'status' => 'Pending Approval'
            ],
            [
                'start_date' => '17-07-2025',
                'end_date' => '17-07-2025',
                'status' => 'Approved'
            ]
        ]
    ];
}

// Get user message
$user_message = '';
if (isset($input['message'])) {
    $user_message = trim(strtolower($input['message']));
} elseif (isset($input['text'])) {
    $user_message = trim(strtolower($input['text']));
} elseif (isset($input['Body'])) {
    $user_message = trim(strtolower($input['Body']));
}

// If no message provided, return error
if (empty($user_message)) {
    $response['message'] = 'No message provided';
    echo json_encode($response);
    exit();
}

// Process user message based on current stage
$bot_response = '';
$buttons = [];
$current_stage = $_SESSION['user_data']['current_stage'];

switch ($current_stage) {
    case 'initial':
        if (in_array($user_message, ['hi', 'hello', 'apply for a leave'])) {
            $bot_response = "Dear " . $_SESSION['user_data']['name'] . ", please choose any of the options listed below:";
            $buttons = ['Raise a Leave Request', 'View leave history'];
            $_SESSION['user_data']['current_stage'] = 'main_menu';
        } else {
            $bot_response = "Hello! Please say 'Hi' or 'Apply for a leave' to start.";
        }
        break;

    case 'main_menu':
        if ($user_message === 'raise a leave request' || $user_message === 'apply for a leave') {
            $bot_response = "Pick the relevant leave type to initiate your request.";
            $buttons = ['1 Hour Permission', 'Casual Leave (CL)', 'On Duty (OD)', 'Main Menu'];
            $_SESSION['user_data']['current_stage'] = 'leave_type_selection';
        } elseif ($user_message === 'view leave history' || $user_message === 'check leave history') {
            $bot_response = "Dear " . $_SESSION['user_data']['name'] . ", your recent leave history:\n";
            foreach ($_SESSION['user_data']['leave_history'] as $leave) {
                $bot_response .= "- " . $leave['start_date'] . " to " . $leave['end_date'] . " – *" . $leave['status'] . "*\n";
            }
            $buttons = ['Main Menu'];
            $_SESSION['user_data']['current_stage'] = 'main_menu';
        } else {
            $bot_response = "Please choose a valid option.";
            $buttons = ['Raise a Leave Request', 'View leave history'];
        }
        break;

    case 'leave_type_selection':
        if (in_array($user_message, ['casual leave (cl)', 'casual leave', 'cl'])) {
            $_SESSION['user_data']['leave_type'] = 'Casual Leave (CL)';
            $bot_response = "Start date of leave (dd/mm/yyyy):";
            $_SESSION['user_data']['current_stage'] = 'start_date';
        } elseif (in_array($user_message, ['1 hour permission', '1 hour', 'hour permission'])) {
            $_SESSION['user_data']['leave_type'] = '1 Hour Permission';
            $bot_response = "Start date of leave (dd/mm/yyyy):";
            $_SESSION['user_data']['current_stage'] = 'start_date';
        } elseif (in_array($user_message, ['on duty (od)', 'on duty', 'od'])) {
            $_SESSION['user_data']['leave_type'] = 'On Duty (OD)';
            $bot_response = "Start date of leave (dd/mm/yyyy):";
            $_SESSION['user_data']['current_stage'] = 'start_date';
        } elseif ($user_message === 'main menu') {
            $bot_response = "Dear " . $_SESSION['user_data']['name'] . ", please choose any of the options listed below:";
            $buttons = ['Raise a Leave Request', 'View leave history'];
            $_SESSION['user_data']['current_stage'] = 'main_menu';
        } else {
            $bot_response = "Please select a valid leave type.";
            $buttons = ['1 Hour Permission', 'Casual Leave (CL)', 'On Duty (OD)', 'Main Menu'];
        }
        break;

    case 'start_date':
        // Simple date validation (dd-mm-yyyy format)
        if (preg_match('/^\d{2}-\d{2}-\d{4}$/', $user_message)) {
            $_SESSION['user_data']['start_date'] = $user_message;
            $bot_response = "End date of leave (dd/mm/yyyy):";
            $_SESSION['user_data']['current_stage'] = 'end_date';
        } else {
            $bot_response = "Please enter the start date in dd-mm-yyyy format (e.g., 23-08-2025):";
        }
        break;

    case 'end_date':
        // Simple date validation (dd-mm-yyyy format)
        if (preg_match('/^\d{2}-\d{2}-\d{4}$/', $user_message)) {
            $_SESSION['user_data']['end_date'] = $user_message;
            $bot_response = "State the reason for availing leave.";
            $_SESSION['user_data']['current_stage'] = 'reason';
        } else {
            $bot_response = "Please enter the end date in dd-mm-yyyy format (e.g., 25-08-2025):";
        }
        break;

    case 'reason':
        if (!empty($user_message) && $user_message !== 'main menu') {
            $_SESSION['user_data']['reason'] = $user_message;
            $bot_response = "Dear " . $_SESSION['user_data']['name'] . ", your " . $_SESSION['user_data']['leave_type'] . 
                          " request from " . $_SESSION['user_data']['start_date'] . " to " . $_SESSION['user_data']['end_date'] . 
                          " has been successfully registered. Please choose one of the options below to proceed with the approval process.";
            $buttons = ['Yes', 'No'];
            $_SESSION['user_data']['current_stage'] = 'approval_confirmation';
        } else {
            $bot_response = "Please provide a reason for your leave request.";
        }
        break;

    case 'approval_confirmation':
        if ($user_message === 'yes') {
            $bot_response = "Leave application submitted successfully.";
            $buttons = ['Main Menu'];
            $_SESSION['user_data']['current_stage'] = 'main_menu';
            
            // Add to leave history
            $_SESSION['user_data']['leave_history'][] = [
                'start_date' => $_SESSION['user_data']['start_date'],
                'end_date' => $_SESSION['user_data']['end_date'],
                'status' => 'Pending Approval'
            ];
            
            // Reset leave request data
            $_SESSION['user_data']['leave_type'] = '';
            $_SESSION['user_data']['start_date'] = '';
            $_SESSION['user_data']['end_date'] = '';
            $_SESSION['user_data']['reason'] = '';
        } elseif ($user_message === 'no') {
            $bot_response = "Leave request cancelled. Returning to main menu.";
            $buttons = ['Main Menu'];
            $_SESSION['user_data']['current_stage'] = 'main_menu';
            
            // Reset leave request data
            $_SESSION['user_data']['leave_type'] = '';
            $_SESSION['user_data']['start_date'] = '';
            $_SESSION['user_data']['end_date'] = '';
            $_SESSION['user_data']['reason'] = '';
        } else {
            $bot_response = "Please select Yes or No to proceed.";
            $buttons = ['Yes', 'No'];
        }
        break;

    default:
        $bot_response = "Hello! Please say 'Hi' or 'Apply for a leave' to start.";
        $_SESSION['user_data']['current_stage'] = 'initial';
        break;
}

// Prepare response
$response = [
    'success' => true,
    'message' => 'Success',
    'bot_response' => $bot_response,
    'buttons' => $buttons,
    'current_stage' => $_SESSION['user_data']['current_stage'],
    'user_data' => [
        'name' => $_SESSION['user_data']['name'],
        'leave_type' => $_SESSION['user_data']['leave_type'],
        'start_date' => $_SESSION['user_data']['start_date'],
        'end_date' => $_SESSION['user_data']['end_date'],
        'reason' => $_SESSION['user_data']['reason']
    ]
];

// Return JSON response
echo json_encode($response, JSON_PRETTY_PRINT);
?>