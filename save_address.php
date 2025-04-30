<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

// Include database connection
include('db.php');

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the raw POST data
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);
    
    // Validate input
    if (!isset($data['address']) || empty(trim($data['address']))) {
        echo json_encode(['success' => false, 'message' => 'Address is required']);
        exit();
    }
    
    $address = trim($data['address']);
    $user_id = $_SESSION['user_id'];
    
    try {
        // Insert new address
        $stmt = $conn->prepare("INSERT INTO address (user_id, address) VALUES (?, ?)");
        $stmt->bind_param('is', $user_id, $address);
        $stmt->execute();
        
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>