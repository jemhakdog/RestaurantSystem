<?php
include('../db.php');


$search = isset($_GET['search']) ? $_GET['search'] : '';
$status = isset($_GET['status']) ? $_GET['status'] : '';
$timeframe = isset($_GET['timeframe']) ? $_GET['timeframe'] : 'today';

$ordersQuery = "SELECT o.*, u.first_name, u.last_name 
                FROM orders o 
                LEFT JOIN users u ON o.user_id = u.user_id 
                WHERE 1=1";

if (!empty($search)) {
    $search = $conn->real_escape_string($search);
    $ordersQuery .= " AND (o.order_id LIKE '%$search%' 
                    OR u.first_name LIKE '%$search%' 
                    OR u.last_name LIKE '%$search%'
                    OR CONCAT(u.first_name, ' ', u.last_name) LIKE '%$search%'
                    OR CONCAT(u.last_name, ' ', u.first_name) LIKE '%$search%')";
}

if (!empty($status)) {
    $status = $conn->real_escape_string($status);
    $ordersQuery .= " AND o.order_status = '$status'";
}

// Add timeframe filter
if (!empty($timeframe)) {
    switch($timeframe) {
        case 'today':
            $ordersQuery .= " AND DATE(o.created_at) = CURDATE()";
            break;
        case 'week':
            $ordersQuery .= " AND YEARWEEK(o.created_at, 1) = YEARWEEK(CURDATE(), 1)";
            break;
        case 'month':
            $ordersQuery .= " AND YEAR(o.created_at) = YEAR(CURDATE()) AND MONTH(o.created_at) = MONTH(CURDATE())";
            break;
    }
}

$ordersQuery .= " ORDER BY o.created_at DESC LIMIT 10";
$ordersResult = $conn->query($ordersQuery);
?>

<table class="table table-hover">
    <thead>
        <tr>
            <th>Order ID</th>
            <th>Customer</th>
            <th>Service Type</th>
            <th>Total</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($ordersResult && $ordersResult->num_rows > 0): ?>
            <?php while($order = $ordersResult->fetch_assoc()): ?>
                <tr>
                    <td>#ORD-<?php echo str_pad($order['order_id'], 4, '0', STR_PAD_LEFT); ?></td>
                    <td><?php echo htmlspecialchars($order['first_name'] . ' ' . $order['last_name']); ?></td>
                    <td><?php echo ucfirst($order['service_type']); ?></td>
                    <td>$<?php echo number_format($order['total_amount'], 2); ?></td>
                    <td>
                        <span class="badge bg-<?php 
                            echo $order['order_status'] === 'pending' ? 'info' : 
                                ($order['order_status'] === 'preparing' ? 'warning' : 
                                ($order['order_status'] === 'ready' ? 'success' : 
                                ($order['order_status'] === 'delivered' ? 'primary' : 'secondary'))); 
                        ?>">
                            <?php echo ucfirst($order['order_status'] ?: 'Pending'); ?>
                        </span>
                    </td>
                    <td>
                        <div class="d-flex gap-2">
                            <button class="btn btn-outline-prim
                            ary btn-sm" onclick="viewOrder(<?php echo $order['order_id']; ?>)">
                                <i class="fas fa-eye"></i> View
                            </button>
                            <?php if ($order['order_status'] === 'pending'): ?>
                                <button class="btn btn-outline-success btn-sm" onclick="updateOrderStatus(<?php echo $order['order_id']; ?>, 'preparing')">
                                    <i class="fas fa-utensils"></i> Assign
                                </button>
                                <button class="btn btn-outline-danger btn-sm" onclick="updateOrderStatus(<?php echo $order['order_id']; ?>, 'cancelled')">
                                    <i class="fas fa-times"></i> Cancel
                                </button>
                            <?php endif; ?>
                            <?php if ($order['order_status'] === 'preparing'): ?>
                                <button class="btn btn-outline-warning btn-sm" onclick="updateOrderStatus(<?php echo $order['order_id']; ?>, 'ready')">
                                    <i class="fas fa-check"></i> Ready
                                </button>
                            <?php endif; ?>
                            <?php if ($order['order_status'] === 'ready'): ?>
                                <button class="btn btn-outline-info btn-sm" onclick="updateOrderStatus(<?php echo $order['order_id']; ?>, 'delivered')">
                                    <i class="fas fa-truck"></i> Deliver
                                </button>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="6" class="text-center">No orders found</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>