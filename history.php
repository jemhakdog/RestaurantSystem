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

        :root {
            --primary: #ff9f1c;
            --secondary: #2ec4b6;
            --dark: #011627;
            --light: #fdfffc;
            --accent: #e71d36;
        }

        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
            color: var(--dark);
            line-height: 1.6;
        }
             
        .navbar {
            background-color: var(--dark);
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .navbar h1 {
            color: var(--primary);
            font-size: 2.2rem;
            font-weight: 700;
            letter-spacing: 1px;
            margin: 0;
            font-family: 'Montserrat', sans-serif;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        .navbar a {
            color: var(--light);
            text-decoration: none;
            padding: 0.8rem 1.2rem;
            margin: 0 0.5rem;
            font-size: 1rem;
            font-weight: 500;
            border-radius: 50px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .navbar a::before {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 2px;
            background-color: var(--primary);
            transition: width 0.3s ease;
        }

        .navbar a:hover {
            color: var(--primary);
            transform: translateY(-2px);
        }

        .navbar a:hover::before {
            width: 100%;
        }

        .navbar a.active {
            background-color: rgba(255, 159, 28, 0.1);
            color: var(--primary);
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