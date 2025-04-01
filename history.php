<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}
// Include database connection
include('db.php');

// Fetch order history for the logged-in user
$sql = "SELECT order_id, receipt_date, receipt_number, subtotal, tax_amount, total_amount, payment_method, service_type, customer_name, delivery_address FROM receipts ORDER BY receipt_date DESC";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order History - The Golden Spoon</title>
    <style>
        .history-container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            border-radius: 8px;
        }

        .history-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .history-table th,
        .history-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .history-table th {
            background-color: #f5f5f5;
            font-weight: bold;
        }

        .history-table tr:hover {
            background-color: #f9f9f9;
        }

        .no-orders {
            text-align: center;
            padding: 20px;
            color: #666;
        }

        .amount {
            text-align: right;
        }

        .navbar {
            background-color: #333; /* Dark background */
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .navbar h1 {
            color: #f1c40f; /* Golden yellow text */
            font-size: 2em;
            font-family: 'Georgia', serif;
            margin: 0;
        }

        .navbar a {
            color: #fff;
            text-decoration: none;
            padding: 12px 20px;
            font-size: 1.1em;
            transition: background-color 0.3s, transform 0.3s ease-in-out;
        }

        .navbar a:hover {
            background-color: #f39c12;
            border-radius: 5px;
            transform: scale(1.05); /* Slight scale effect on hover */
        }

        .navbar a.active {
            background-color: #f39c12;
        }

        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #e8e8e8; /* Light gray background */
            color: #444; /* Darker text for better readability */
        }
    </style>
</head>
<body>
   
<div class="navbar">
        <h1>The Golden Spoon</h1>
        <div>
            <a href="dashboard.php">Home</a>
            <a href="menu.php">Menu</a>
            <a href="cart.php">Cart</a>
            <a href="reservation.php">Reservation</a>
            <a href="profile.php">Profile</a>
            <a href="history.php" class="active">History</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>
    <div class="history-container">
        <h2>Order History</h2>

        <?php if ($result->num_rows > 0): ?>
            <table class="history-table">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Date</th>
                        <th>Receipt Number</th>
                        <th>Subtotal</th>
                        <th>Tax</th>
                        <th>Total</th>
                        <th>Payment Method</th>
                        <th>Service Type</th>
                        <th>Customer Name</th>
                        <th>Delivery Address</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['order_id']); ?></td>
                            <td><?php echo date('Y-m-d H:i', strtotime($row['receipt_date'])); ?></td>
                            <td><?php echo htmlspecialchars($row['receipt_number']); ?></td>
                            <td class="amount">$<?php echo number_format($row['subtotal'], 2); ?></td>
                            <td class="amount">$<?php echo number_format($row['tax_amount'], 2); ?></td>
                            <td class="amount">$<?php echo number_format($row['total_amount'], 2); ?></td>
                            <td><?php echo htmlspecialchars(ucfirst($row['payment_method'])); ?></td>
                            <td><?php echo htmlspecialchars(ucfirst($row['service_type'])); ?></td>
                            <td><?php echo htmlspecialchars($row['customer_name']); ?></td>
                            <td><?php echo $row['delivery_address'] ? htmlspecialchars($row['delivery_address']) : 'N/A'; ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="no-orders">
                <p>No order history found.</p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>