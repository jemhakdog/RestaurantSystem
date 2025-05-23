<?php
session_start();
include('../db.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

// Initialize variables to avoid undefined warnings
$search = isset($_GET['search']) ? $_GET['search'] : '';
$status = isset($_GET['status']) ? $_GET['status'] : '';
$timeframe = isset($_GET['timeframe']) ? $_GET['timeframe'] : 'today';
function getStatusClass($status) {
    switch($status) {
        case 'pending': return 'badge-new';
        case 'preparing': return 'badge-preparing';
        case 'ready': return 'badge-ready';
        case 'delivered': return 'badge-delivered';
        case 'cancelled': return 'badge-cancelled';
        default: return 'badge-secondary';
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Management - The Golden Spoon</title>
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
    <!-- Bootstrap JS and Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
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
                            <a class="nav-link" href="dashboard.php">
                                <i class="fas fa-tachometer-alt"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="/admin/orders.php">
                                <i class="fas fa-list-alt"></i> Orders
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/admin/menu.php">
                                <i class="fas fa-utensils"></i> Menu
                            </a>
                        </li>
                       
                      
                        <li class="nav-item">
                            <a class="nav-link" href="/admin/reports.php">
                                <i class="fas fa-chart-line"></i> Reports
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
            <div class="col-md-9 ms-sm-auto col-lg-10 px-md-4 main-content">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Order Management</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group me-2">
                            <a href="?timeframe=today" class="btn btn-sm btn-outline-secondary <?php echo $timeframe === 'today' ? 'active' : ''; ?>">Today</a>
                            <a href="?timeframe=week" class="btn btn-sm btn-outline-secondary <?php echo $timeframe === 'week' ? 'active' : ''; ?>">Week</a>
                            <a href="?timeframe=month" class="btn btn-sm btn-outline-secondary <?php echo $timeframe === 'month' ? 'active' : ''; ?>">Month</a>
                        </div>
                    </div>
                </div>

                <!-- Search and Filter -->
                <form method="GET">
                    <input type="hidden" name="timeframe" id="timeframe" value="<?php echo $timeframe; ?>">
                    <div class="row mb-4">
                        <div class="col-md-8">
                            <div class="search-box">
                                <i class="fas fa-search"></i>
                                <input type="text" class="form-control" name="search" id="searchInput" placeholder="Search orders..." value="<?php echo htmlspecialchars($search); ?>">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <select class="form-select" name="status" id="statusFilter">
                                <option value="" <?php echo $status === '' ? 'selected' : ''; ?>>All Status</option>
                                <option value="pending" <?php echo $status === 'pending' ? 'selected' : ''; ?>>New</option>
                                <option value="preparing" <?php echo $status === 'preparing' ? 'selected' : ''; ?>>Preparing</option>
                                <option value="ready" <?php echo $status === 'ready' ? 'selected' : ''; ?>>Ready</option>
                                <option value="delivered" <?php echo $status === 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                                <option value="cancelled" <?php echo $status === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                            </select>
                        </div>
                    </div>
                  
                </form>
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
                <!-- Order Actions Modals -->
                <!-- Status Update Modal -->
                <div class="modal fade" id="statusModal" tabindex="-1" aria-labelledby="statusModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Update Order Status</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form id="statusForm">
                                    <input type="hidden" id="orderId" name="orderId">
                                    <div class="mb-3">
                                        <label for="statusSelect" class="form-label">New Status</label>
                                        <select class="form-select" id="statusSelect" name="status">
                                            <option value="new">New</option>
                                            <option value="preparing">Preparing</option>
                                            <option value="ready">Ready</option>
                                            <option value="delivered">Delivered</option>
                                            <option value="cancelled">Cancelled</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="statusNotes" class="form-label">Notes</label>
                                        <textarea class="form-control" id="statusNotes" name="notes" rows="3"></textarea>
                                    </div>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="button" class="btn btn-primary" id="saveStatusBtn">Save changes</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Assign Order Modal -->
                <div class="modal fade" id="assignModal" tabindex="-1" aria-labelledby="assignModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Assign Order</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form id="assignForm">
                                    <input type="hidden" id="assignOrderId" name="orderId">
                                    <div class="mb-3">
                                        <label for="staffSelect" class="form-label">Assign To</label>
                                        <select class="form-select" id="staffSelect" name="staffId">
                                            <option value="1">Kitchen Staff 1</option>
                                            <option value="2">Kitchen Staff 2</option>
                                            <option value="3">Delivery Staff 1</option>
                                            <option value="4">Delivery Staff 2</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="assignNotes" class="form-label">Instructions</label>
                                        <textarea class="form-control" id="assignNotes" name="instructions" rows="3"></textarea>
                                    </div>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="button" class="btn btn-primary" id="saveAssignBtn">Save changes</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Orders List -->
                <div class="">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">All Orders</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover" id="ordersTable">
                               
                                <tbody id="ordersTableBody">
                                    <!-- Orders will be loaded dynamically via AJAX -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
             

            
                </div>
                </div>
                </div> </div>
            </div>
        </div>
    </div>


                <script>
                
                document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('searchInput'); // Target the search input
    const statusFilter = document.getElementById('statusFilter'); // Target the status filter
    const timeframe = document.getElementById('timeframe'); // Target the timeframe filter

    // Function to update the table via AJAX
    function updateTable() {
        if (!timeframe) return; // Skip if element not found
        const searchValue = encodeURIComponent(searchInput.value); // Get and encode search value
        const statusValue = encodeURIComponent(statusFilter.value); // Get and encode status value

        fetch(`get_orders.php?search=${searchValue}&status=${statusValue}&timeframe=${timeframe.value}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.text(); // Parse the response as text (HTML)
            })
            .then(html => {
                document.querySelector('.table-responsive tbody').innerHTML = html; // Update the table body
            })
            .catch(error => {
                console.error('Error:', error);
                document.querySelector('.table-responsive tbody').innerHTML =
                    '<tr><td colspan="6" class="text-center">Error loading orders</td></tr>';
            });
    }

    // Add event listener for search input with debouncing
    searchInput.addEventListener('input', function () {
        clearTimeout(searchTimeout); // Clear any pending timeout
        searchTimeout = setTimeout(updateTable, 300); // Trigger updateTable after 300ms of inactivity
    });

    // Add event listeners for filters
    statusFilter.addEventListener('change', updateTable);
    timeframe.addEventListener('change', updateTable);

    // Set initial values from URL params (optional)
    const urlParams = new URLSearchParams(window.location.search);
    searchInput.value = urlParams.get('search') || ''; // Pre-fill search input if present in URL
    statusFilter.value = urlParams.get('status') || ''; // Pre-select status filter if present in URL

    // Initial load of the table
    updateTable();
});
                </script>
</html>

