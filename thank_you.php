<?php
session_start();
include('db.php');
include('includes/Mailer.php'); // Add Mailer class

if (!isset($_GET['order_id'])) {
    header('Location: dashboard.php');
    exit();
}

$order_id = $_GET['order_id'];

// Fetch order details
$stmt = $conn->prepare("SELECT o.*, u.first_name, u.last_name, u.email 
                       FROM orders o 
                       JOIN users u ON o.user_id = u.user_id 
                       WHERE o.order_id = ?");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

// Fetch order items
$stmt = $conn->prepare("SELECT oi.*, m.name, m.price 
                       FROM order_items oi 
                       JOIN menu m ON oi.menu_id = m.menu_id 
                       WHERE oi.order_id = ?");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$order_items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Format items for email
$items_for_email = array_map(function($item) {
    return [
        'name' => $item['name'],
        'quantity' => $item['quantity'],
        'price' => $item['subtotal']
    ];
}, $order_items);

// Send confirmation email
$mailer = new Mailer();
$customer_name = $order['first_name'] . ' ' . $order['last_name'];
$delivery_address = ($order['service_type'] === 'delivery' && isset($order['delivery_address'])) ? $order['delivery_address'] : null;
$mailer->sendOrderConfirmation(
    $customer_name,
    $order['email'],
    $order_id,
    $items_for_email,
    $order['total_amount'],
    $order['service_type'],
    $delivery_address
);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation - The Golden Spoon</title>
    <style>
        .container {
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .order-details {
            margin: 20px 0;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 5px;
        }
        .order-items {
            margin: 20px 0;
        }
        .item {
            padding: 10px;
            border-bottom: 1px solid #eee;
        }
        .success-message {
            color: #28a745;
            text-align: center;
            font-size: 24px;
            margin-bottom: 30px;
        }
        .back-button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #f39c12;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="success-message">
            <h1>Thank You for Your Order!</h1>
            <p>Order #<?php echo $order_id; ?> has been successfully placed.</p>
        </div>

        <div class="order-details">
            <h2>Order Details</h2>
            <p>Order Date: <?php echo date('F j, Y, g:i a', strtotime($order['created_at'])); ?></p>
            <p>Service Type: <?php echo ucfirst($order['service_type']); ?></p>
            <p>Payment Method: <?php echo ucfirst($order['payment_method']); ?></p>
            <p>Total Amount: $<?php echo number_format($order['total_amount'], 2); ?></p>
        </div>

        <div class="order-items">
            <h2>Order Items</h2>
            <?php foreach ($order_items as $item): ?>
                <div class="item">
                    <p>
                        <?php echo htmlspecialchars($item['name']); ?> x 
                        <?php echo $item['quantity']; ?> - 
                        $<?php echo number_format($item['subtotal'], 2); ?>
                    </p>
                </div>
            <?php endforeach; ?>
        </div>

        <a href="dashboard.php" class="back-button">Return to Dashboard</a>
    </div>
</body>
</html>
