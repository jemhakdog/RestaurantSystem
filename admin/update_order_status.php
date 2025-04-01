<?php
session_start();
include('../db.php');


// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

// Validate input
if (!isset($_POST['order_id']) || !isset($_POST['status'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
    exit();
}

$order_id = intval($_POST['order_id']);
$status = $conn->real_escape_string($_POST['status']);

// Update order status
$sql = "UPDATE orders SET 
        order_status = ?, 
        updated_at = NOW() 
        WHERE order_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param('si', $status, $order_id);

if ($stmt->execute()) {
    // Get customer user_id
    $customer_sql = "SELECT user_id FROM orders WHERE order_id = ?";
    $customer_stmt = $conn->prepare($customer_sql);
    $customer_stmt->bind_param('i', $order_id);
    $customer_stmt->execute();
    $customer_result = $customer_stmt->get_result();
    $customer_data = $customer_result->fetch_assoc();
    
    if (!$customer_data) {
        echo json_encode(['success' => false, 'message' => 'Order not found']);
        exit();
    }

    // Insert into order history
    $history_sql = "INSERT INTO order_history (order_id, status, created_by) 
                   VALUES (?, ?, ?)";
    $history_stmt = $conn->prepare($history_sql);
    $history_stmt->bind_param('isi', 
        $order_id, 
        $status,
        $_SESSION['user_id']
    );
    
    if (!$history_stmt->execute()) {
        echo json_encode(['success' => false, 'message' => 'Failed to update order history']);
        exit();
    }

    // Fetch updated order details
    $order_sql = "SELECT o.*, u.first_name, u.last_name, u.phone, 
                 CASE 
                     WHEN o.service_type = 'delivery' THEN a.address 
                     ELSE NULL 
                 END AS address
                 FROM orders o 
                 LEFT JOIN users u ON o.user_id = u.user_id 
                 LEFT JOIN address a ON o.user_id = a.user_id 
                 WHERE o.order_id = ?";
    $order_stmt = $conn->prepare($order_sql);
    $order_stmt->bind_param('i', $order_id);
    $order_stmt->execute();
    $order_result = $order_stmt->get_result();
    $order_details = $order_result->fetch_assoc();

    // Fetch order items
    $items_sql = "SELECT oi.*, m.name as item_name, m.price 
                  FROM order_items oi 
                  LEFT JOIN menu m ON oi.menu_id = m.menu_id 
                  WHERE oi.order_id = ?";
    $items_stmt = $conn->prepare($items_sql);
    $items_stmt->bind_param('i', $order_id);
    $items_stmt->execute();
    $items_result = $items_stmt->get_result();
    
    $order_items = [];
    while ($item = $items_result->fetch_assoc()) {
        $order_items[] = [
            'name' => $item['item_name'],
            'quantity' => $item['quantity'],
            'price' => $item['price'],
            'subtotal' => $item['quantity'] * $item['price']
        ];
    }

    // Prepare response data
    $response = [
        'success' => true,
        'order' => [
            'order_id' => $order_details['order_id'],
            'customer_name' => $order_details['first_name'] . ' ' . $order_details['last_name'],
            'phone' => $order_details['phone'],
            'address' => $order_details['address'],
            'status' => $order_details['order_status'],
            'total_amount' => $order_details['total_amount'],
            'items' => $order_items
        ]
    ];

    echo json_encode($response);
} else {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
}

$stmt->close();
$conn->close();