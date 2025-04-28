<?php
session_start();
include('db.php');

if (!isset($_SESSION['user_id']) || empty($_POST)) {
    header('Location: checkout.php');
    exit();
}

try {
    $conn->begin_transaction();

    // Get form data
    $user_id = $_SESSION['user_id'];
    $service_type = $_POST['service_type'];
    $payment_method = $_POST['payment_method'];
    $total_amount = $_POST['total_amount'];
    
    // Create the order
    $stmt = $conn->prepare("INSERT INTO orders (user_id, service_type, payment_method, total_amount) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("issd", $user_id, $service_type, $payment_method, $total_amount);
    $stmt->execute();
    
    $order_id = $conn->insert_id;

    // Save order items
    if (isset($_POST['cart']) && is_array($_POST['cart'])) {
        foreach ($_POST['cart'] as $item) {
            $menu_id = $item['menu_id'];
            $quantity = $item['quantity'];
            $unit_price = $item['price'];
            $subtotal = $quantity * $unit_price;
            
            $notes = isset($item['notes']) ? $item['notes'] : '';
            $stmt = $conn->prepare("INSERT INTO order_items (order_id, menu_id, quantity, unit_price, subtotal, notes) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("iiisss", $order_id, $menu_id, $quantity, $unit_price, $subtotal, $notes);
            $stmt->execute();
        }
    }

    $conn->commit();
    unset($_SESSION['cart']);
    
    header('Location: thank_you.php?order_id=' . $order_id);
    exit();

} catch (Exception $e) {
    $conn->rollback();
    $_SESSION['error'] = "Error processing order: " . $e->getMessage();
    header('Location: checkout.php');
    exit();
}
?>
