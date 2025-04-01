<?php
session_start();
include('../db.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

// Initialize stats array
$stats = [
    'new' => 0,
    'preparing' => 0,
    'ready' => 0,
    'delivered' => 0
];

// Get today's order counts for each status
// At the top of the file, after session_start()
$timeframe = isset($_GET['timeframe']) ? $_GET['timeframe'] : 'today';

// Modify the SQL query based on timeframe
$dateCondition = "";
switch($timeframe) {
    case 'week':
        $dateCondition = "WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
        break;
    case 'month':
        $dateCondition = "WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)";
        break;
    default: // today
        $dateCondition = "WHERE DATE(created_at) = CURDATE()";
}

$sql = "SELECT 
    SUM(CASE WHEN order_status = 'pending' THEN 1 ELSE 0 END) as new_orders,
    SUM(CASE WHEN order_status = 'preparing' THEN 1 ELSE 0 END) as preparing,
    SUM(CASE WHEN order_status = 'ready' THEN 1 ELSE 0 END) as ready,
    SUM(CASE WHEN order_status = 'delivered' THEN 1 ELSE 0 END) as delivered
FROM orders 
$dateCondition";

$result = $conn->query($sql);

if ($result) {
    $row = $result->fetch_assoc();
    $stats['new'] = $row['new_orders'] ?? 0;
    $stats['preparing'] = $row['preparing'] ?? 0;
    $stats['ready'] = $row['ready'] ?? 0;
    $stats['delivered'] = $row['delivered'] ?? 0;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - The Golden Spoon</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #FFD700;
            --secondary: #2C3E50;
            --light: #F8F9FA;
            --dark: #343A40;
            --success: #28A745;
            --info: #17A2B8;
            --warning: #FFC107;
            --danger: #DC3545;
            --sidebar-width: 280px;
            --transition-speed: 0.3s;
        }
        
        body {
            background-color: #F8F9FA;
            font-family: 'Segoe UI', Roboto, 'Helvetica Neue', sans-serif;
        }
        
        .sidebar {
            background: linear-gradient(135deg, var(--secondary) 0%, #1A252F 100%);
            color: white;
            height: 100vh;
            position: fixed;
            width: var(--sidebar-width);
            box-shadow: 2px 0 15px rgba(0, 0, 0, 0.1);
            transition: all var(--transition-speed) ease;
        }
        
        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.85);
            margin-bottom: 8px;
            border-radius: 8px;
            padding: 12px 20px;
            transition: all var(--transition-speed) ease;
        }
        
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            color: white;
            background: rgba(255, 215, 0, 0.15);
            transform: translateX(5px);
        }
        
        .sidebar .nav-link i {
            margin-right: 12px;
            font-size: 1.1rem;
        }
        
        .main-content {
            margin-left: var(--sidebar-width);
            padding: 30px;
            transition: all var(--transition-speed) ease;
        }
        
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
            transition: all var(--transition-speed) ease;
            overflow: hidden;
        }
        
        .card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 25px rgba(0, 0, 0, 0.12);
        }
        
        .card-body {
            padding: 1.5rem;
        }
        
        .card-title {
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        
        .card-text {
            font-size: 2rem;
            font-weight: 700;
            margin: 0.5rem 0;
        }
        
        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            letter-spacing: 0.5px;
        }
        
        .search-box {
            position: relative;
            margin-bottom: 1rem;
        }
        
        .search-box input {
            padding-left: 45px;
            border-radius: 8px;
            border: 1px solid #E9ECEF;
            height: 45px;
            transition: all var(--transition-speed) ease;
        }
        
        .search-box input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 0.25rem rgba(255, 215, 0, 0.25);
        }
        
        .search-box i {
            position: absolute;
            left: 15px;
            top: 14px;
            color: #6C757D;
            font-size: 1rem;
        }
        
        .table {
            border-collapse: separate;
            border-spacing: 0 8px;
        }
        
        .table thead th {
            border: none;
            font-weight: 600;
            color: #495057;
        }
        
        .table tbody tr {
            background: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            transition: all var(--transition-speed) ease;
        }
        
        .table tbody tr:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .btn {
            border-radius: 8px;
            padding: 8px 16px;
            font-weight: 500;
            transition: all var(--transition-speed) ease;
        }
        
        .btn-primary {
            background-color: var(--primary);
            border-color: var(--primary);
            color: #2C3E50;
        }
        
        .btn-primary:hover {
            background-color: #FFC800;
            border-color: #FFC800;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 d-md-block sidebar collapse">
                <div class="position-sticky pt-3">
                    <div class="text-center mb-4">
                        <h4 class="text-warning">The Golden Spoon</h4>
                        <p class="text-white-50">Admin Dashboard</p>
                    </div>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link active" href="dashboard.php">
                                <i class="fas fa-tachometer-alt"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/admin/orders.php">
                                <i class="fas fa-list-alt"></i> Orders
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/admin/menu.php">
                                <i class="fas fa-utensils"></i> Menu
                            </a>
                        </li>
                       
                        
                        <li class="nav-item">
                            <a class="nav-link" href="/admin/settings.php">
                                <i class="fas fa-cog"></i> Settings
                            </a>
                        </li>
                        <li class="nav-item mt-3">
                            <a class="nav-link text-danger" href="../logout.php">
                                <i class="fas fa-sign-out-alt"></i> Logout
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-9  main-content">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Order Management Dashboard</h1>
                    <!-- Replace the existing buttons -->
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group me-2">
                            <a href="?timeframe=today" class="btn btn-sm btn-outline-secondary <?php echo $timeframe === 'today' ? 'active' : ''; ?>">Today</a>
                            <a href="?timeframe=week" class="btn btn-sm btn-outline-secondary <?php echo $timeframe === 'week' ? 'active' : ''; ?>">Week</a>
                            <a href="?timeframe=month" class="btn btn-sm btn-outline-secondary <?php echo $timeframe === 'month' ? 'active' : ''; ?>">Month</a>
                        </div>
                       
                    </div>
                </div>

                <!-- Stats Cards -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <h5 class="card-title">New Orders</h5>
                                <h2 class="card-text"><?php echo $stats['new']; ?></h2>
                                <p class="mb-0">Today</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-warning">
                            <div class="card-body">
                                <h5 class="card-title">Preparing</h5>
                                <h2 class="card-text"><?php echo $stats['preparing']; ?></h2>
                                <p class="mb-0">In Progress</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <h5 class="card-title">Ready</h5>
                                <h2 class="card-text"><?php echo $stats['ready']; ?></h2>
                                <p class="mb-0">For Delivery</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <h5 class="card-title">Delivered</h5>
                                <h2 class="card-text"><?php echo $stats['delivered']; ?></h2>
                                <p class="mb-0">Today</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Update the search and filter section -->
                <div class="row mb-4">
                    <div class="col-md-8">
                        <div class="search-box">
                            <i class="fas fa-search"></i>
                            <input type="text" id="searchInput" class="form-control" placeholder="Search orders...">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <select class="form-select" id="statusFilter">
                            <option value="">All Status</option>
                            <option value="pending">New</option>
                            <option value="preparing">Preparing</option>
                            <option value="ready">Ready</option>
                            <option value="delivered">Delivered</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                </div>

                <!-- Update the orders query section -->
                <?php
                $search = isset($_GET['search']) ? $_GET['search'] : '';
                $status = isset($_GET['status']) ? $_GET['status'] : '';
                
                $ordersQuery = "SELECT o.*, u.first_name, u.last_name 
                                FROM orders o 
                                LEFT JOIN users u ON o.user_id = u.user_id 
                                WHERE 1=1";
                
                if (!empty($search)) {
                    $search = $conn->real_escape_string($search);
                    $ordersQuery .= " AND (o.order_id LIKE '%$search%' 
                                    OR u.first_name LIKE '%$search%' 
                                    OR u.last_name LIKE '%$search%')";
                }
                
                if (!empty($status)) {
                    $status = $conn->real_escape_string($status);
                    $ordersQuery .= " AND o.order_status = '$status'";
                }
                
                $ordersQuery .= " ORDER BY o.created_at DESC LIMIT 10";
                $ordersResult = $conn->query($ordersQuery);
                ?>

                <!-- Orders List -->
                <div class="card mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Recent Orders</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
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
                                                <td><?php echo htmlspecialchars($order['service_type']); ?></td>
                                                <td>$<?php echo number_format($order['total_amount'], 2); ?></td>
                                                <td>
                                                    <span class="badge bg-<?php 
                                                        echo $order['order_status'] === 'pending' ? 'info' : 
                                                            ($order['order_status'] === 'preparing' ? 'warning' : 
                                                            ($order['order_status'] === 'ready' ? 'success' : 
                                                            ($order['order_status'] === 'delivered' ? 'primary' : 'secondary'))); 
                                                    ?>">
                                                        <?php echo ucfirst($order['order_status']); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="d-flex gap-2">
                                                        <button class="btn btn-outline-primary btn-sm" onclick="viewOrder(<?php echo $order['order_id']; ?>)">
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
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Order Details Modal -->
    <div class="modal fade" id="orderDetailsModal" tabindex="-1" aria-labelledby="orderDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="orderDetailsModalLabel">Order Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="orderDetailsContent">
                    <!-- Content will be loaded dynamically -->
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
    function viewOrder(orderId) {
        const modal = new bootstrap.Modal(document.getElementById('orderDetailsModal'));
        const modalContent = document.getElementById('orderDetailsContent');
        modalContent.innerHTML = `
            <div class="text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        `;
        modal.show();

        fetch(`get_order_details.php?order_id=${orderId}`)
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    modalContent.innerHTML = `<div class="alert alert-danger">${data.error}</div>`;
                    return;
                }
                
                modalContent.innerHTML = `
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card border-0 shadow-sm">
                                <div class="card-body">
                                    <div class="mb-4">
                                        <h6 class="fw-bold">#ORD-${String(data.order_id).padStart(4, '0')}</h6>
                                        <p class="text-muted mb-1"><i class="fas fa-user me-2"></i>Customer: ${data.first_name} ${data.last_name}</p>
                                        <p class="text-muted mb-1"><i class="fas fa-phone me-2"></i>Phone: ${data.phone}</p>
                                        <p class="text-muted mb-1"><i class="fas fa-truck me-2"></i>Service Type: ${data.service_type ? data.service_type.charAt(0).toUpperCase() + data.service_type.slice(1) : 'N/A'}</p>
                                        <p class="text-muted mb-1"><i class="fas fa-credit-card me-2"></i>Payment Method: ${data.payment_method ? data.payment_method.replace('_', ' ').toUpperCase() : 'N/A'}</p>
                                        <p class="text-muted mb-1"><i class="fas fa-money-bill-wave me-2"></i>Payment Status: ${data.payment_status ? data.payment_status.charAt(0).toUpperCase() + data.payment_status.slice(1) : 'N/A'}</p>
                                        <p class="text-muted"><i class="fas fa-map-marker-alt me-2"></i>Delivery Address: ${data.delivery_address || 'N/A'}</p>
                                    </div>
                                    <hr>
                                    <h6 class="fw-bold mb-3">Order Items</h6>
                                    <ul class="list-group list-group-flush mb-3">
                                        ${data.items.map(item => `
                                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                                <div>
                                                    <span class="fw-medium">${item.name}</span>
                                                    <br>
                                                    <small class="text-muted">Quantity: ${item.quantity}</small>
                                                </div>
                                                <span>$${(item.price * item.quantity).toFixed(2)}</span>
                                            </li>
                                        `).join('')}
                                    </ul>
                                    <div class="d-flex justify-content-between align-items-center py-3 border-top">
                                        <h6 class="fw-bold mb-0">Total:</h6>
                                        <h6 class="fw-bold mb-0">$${data.total_amount}</h6>
                                    </div>
                                </div>
                            </div>
                            <div class="d-grid gap-2 mt-3">
                                ${data.order_status === 'pending' ? 
                                    `<button class="btn btn-warning" onclick="updateOrderStatus(${data.order_id}, 'preparing')"><i class="fas fa-utensils me-2"></i>Assign to Kitchen</button>` : ''}
                                ${data.order_status === 'preparing' ? 
                                    `<button class="btn btn-success" onclick="updateOrderStatus(${data.order_id}, 'ready')"><i class="fas fa-check me-2"></i>Mark as Ready</button>` : ''}
                                ${data.order_status === 'pending' ? 
                                    `<button class="btn btn-danger" onclick="updateOrderStatus(${data.order_id}, 'cancelled')"><i class="fas fa-times me-2"></i>Cancel Order</button>` : ''}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card border-0 shadow-sm">
                                <div class="card-body">
                                    <h6 class="fw-bold mb-4">Order Status</h6>
                                    <div class="timeline">
                                        <div class="timeline-item">
                                            <h6 class="fw-bold">Current Status</h6>
                                            <p class="text-muted small mb-1">${new Date(data.created_at).toLocaleString()}</p>
                                            <p class="mb-0">${data.order_status.charAt(0).toUpperCase() + data.order_status.slice(1)}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            })
            .catch(error => {
                modalContent.innerHTML = `<div class="alert alert-danger">Error loading order details. Please try again.</div>`;
            });
    }

    function updateOrderStatus(orderId, status) {
        fetch('update_order_status.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `order_id=${orderId}&status=${status}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Success - refresh the page to show updated status
                location.reload();
            } else {
                // Actual error case
                console.error('Error:', data.message);
                alert('Error updating status: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while updating the order status');
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchInput');
        const statusFilter = document.getElementById('statusFilter');
        let searchTimeout;
    
        function updateTable() {
            const searchValue = encodeURIComponent(searchInput.value);
            const statusValue = encodeURIComponent(statusFilter.value);
            
            fetch(`get_orders.php?search=${searchValue}&status=${statusValue}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.text();
                })
                .then(html => {
                    document.querySelector('.table-responsive').innerHTML = html;
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.querySelector('.table-responsive').innerHTML = '<div class="alert alert-danger">Error loading orders. Please try again.</div>';
                });
        }
    
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(updateTable, 300);
        });
    
        statusFilter.addEventListener('change', updateTable);
    
        // Set initial values from URL params
        const urlParams = new URLSearchParams(window.location.search);
        searchInput.value = urlParams.get('search') || '';
        statusFilter.value = urlParams.get('status') || '';
    });
    </script>
</body>
</html>