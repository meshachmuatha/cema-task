<?php


// Include database connection and error handling functions
require_once 'database.php';
require_once 'error_handling.php';

// Start session for user authentication
session_start();

// Check if user is authenticated
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

// Handle requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle medication order
    if (isset($_POST['medication']) && isset($_POST['quantity'])) {
        $medication = $_POST['medication'];
        $quantity = $_POST['quantity'];
        
        // Save order to legacy database
        $success = saveOrderToLegacyDB($medication, $quantity);
        
        if ($success) {
            // Communicate with pharmacy system
            $response = processOrder($medication, $quantity);
            
            if ($response['success']) {
                echo json_encode(['success' => true, 'message' => 'Order placed successfully']);
            } else {
                logError('Failed to process order with pharmacy system');
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Failed to process order with pharmacy system']);
            }
        } else {
            logError('Failed to save order to legacy database');
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Failed to save order to legacy database']);
        }
    } else {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Missing medication or quantity']);
    }
} else if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Handle statement request
    if (isset($_GET['customerId'])) {
        // Sample code to retrieve order history from legacy database
        $statement = getStatement($_GET['customerId']);
        
        if ($statement !== false) {
            echo json_encode(['success' => true, 'statement' => $statement]);
        } else {
            logError('Failed to retrieve statement from legacy database');
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Failed to retrieve statement']);
        }
    } else {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Missing customer ID']);
    }
}

// Function to save order to legacy database
function saveOrderToLegacyDB($medication, $quantity) {
    // Implement logic to save order to legacy database
    // Return true if successful, false otherwise
    return true;
}

// Function to process order with pharmacy system
function processOrder($medication, $quantity) {
    // Implement logic to communicate with pharmacy system
    // Return response from pharmacy system
    $data = ['medication' => $medication, 'quantity' => $quantity];
    $options = [
        'http' => [
            'method' => 'POST',
            'header' => 'Content-Type: application/json',
            'content' => json_encode($data)
        ]
    ];
    $context = stream_context_create($options);
    $response = file_get_contents('https://your-pharmacy-system/api/process_order', false, $context);
    return json_decode($response, true);
}

// Function to retrieve statement from legacy database
function getStatement($customerId) {
    // Implement logic to retrieve statement from legacy database
    // Return statement array if successful, false otherwise
    return ['order1', 'order2', 'order3'];
}

?>
