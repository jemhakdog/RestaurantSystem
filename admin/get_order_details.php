<?php
session_start();
include('../db.php');


// Check if order_id is provided
if (!isset($_GET['order_id'])) {

    header('Content-Type: application/json');
    echo json_encode(['error' => 'Order ID is required']);
    exit;
}

// Sanitize input
$order_id = filter_var($_GET['order_id'], FILTER_SANITIZE_NUMBER_INT);

// Get order details with customer information
$sql = "SELECT o.*, u.first_name, u.last_name, u.phone, 
        CASE 
            WHEN o.service_type = 'delivery' THEN a.address 
            ELSE NULL 
        END AS delivery_address
        FROM orders o 
        LEFT JOIN users u ON o.user_id = u.user_id 
        LEFT JOIN address a ON o.user_id = a.user_id 
        WHERE o.order_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $order_id);
$stmt->execute();
// Add error handling for the database query
if (!$stmt->execute()) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Database error']);
    exit;
}
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Order not found']);
    exit;
}

$order = $result->fetch_assoc();

// Get order items
$items = [];
$items_sql = "SELECT oi.*, m.name 
              FROM order_items oi 
              LEFT JOIN menu m ON oi.menu_id = m.menu_id 
              WHERE oi.order_id = ?";

$stmt = $conn->prepare($items_sql);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$items_result = $stmt->get_result();

while ($item = $items_result->fetch_assoc()) {
    $items[] = [
        'name' => htmlspecialchars($item['name'] ?? ''),
        'quantity' => intval($item['quantity']),
        'price' => floatval($item['unit_price'] ?? $item['price'] ?? 0) // Use unit_price with fallback to price
    ];
}

// Get order timeline
$timeline = [];
// Check if order_timeline table exists
$table_exists = $conn->query("SHOW TABLES LIKE 'order_timeline'");
if ($table_exists->num_rows > 0) {
    $timeline_sql = "SELECT * FROM order_timeline WHERE order_id = ? ORDER BY created_at DESC";
    $stmt = $conn->prepare($timeline_sql);
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $timeline_result = $stmt->get_result();

    while ($event = $timeline_result->fetch_assoc()) {
        $timeline[] = [
            'status' => htmlspecialchars($event['status'] ?? ''),
            'description' => htmlspecialchars($event['description'] ?? ''),
            'timestamp' => date('M j, Y g:i A', strtotime($event['created_at']))
        ];
    }
}

// Prepare response
$response = [
    'order_id' => intval($order['order_id']),
    'first_name' => htmlspecialchars($order['first_name'] ?? ''),
    'last_name' => htmlspecialchars($order['last_name'] ?? ''),
    'phone' => htmlspecialchars($order['phone'] ?? ''),
    'delivery_address' => htmlspecialchars($order['delivery_address'] ?? 'N/A'),
    'service_type' => htmlspecialchars($order['service_type'] ?? 'N/A'),
    'payment_method' => htmlspecialchars($order['payment_method'] ?? 'N/A'),
    'payment_status' => htmlspecialchars($order['payment_status'] ?? 'N/A'),
    'total_amount' => floatval($order['total_amount'] ?? 0),
    'order_status' => htmlspecialchars($order['order_status'] ?? ''),
    'created_at' => isset($order['created_at']) ? date('M j, Y g:i A', strtotime($order['created_at'])) : '',
    'items' => $items,
    'timeline' => $timeline
];

// Set proper JSON header and ensure no output before this
ob_clean(); // Clear any previous output
header('Content-Type: application/json');
echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
exit;
?>