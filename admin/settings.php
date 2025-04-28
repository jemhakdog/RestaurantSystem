<?php
session_start();
include('../db.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - The Golden Spoon</title>
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
                            <a class="nav-link" href="dashboard.php">
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
                            <a class="nav-link active" href="/admin/settings.php">
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
            <div class="col-md-9 main-content">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">System Settings</h1>
                </div>

                <!-- Settings Tabs -->
                <ul class="nav nav-tabs mb-4" id="settingsTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="general-tab" data-bs-toggle="tab" data-bs-target="#general" type="button" role="tab" aria-controls="general" aria-selected="true">General</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="payment-tab" data-bs-toggle="tab" data-bs-target="#payment" type="button" role="tab" aria-controls="payment" aria-selected="false">Payment</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="notifications-tab" data-bs-toggle="tab" data-bs-target="#notifications" type="button" role="tab" aria-controls="notifications" aria-selected="false">Notifications</button>
                    </li>
                </ul>

                <!-- Settings Content -->
                <div class="tab-content" id="settingsTabsContent">
                    <!-- General Settings -->
                    <div class="tab-pane fade show active" id="general" role="tabpanel" aria-labelledby="general-tab">
                        <div class="card mb-4">
                            <div class="card-header bg-white">
                                <h5 class="mb-0">Restaurant Information</h5>
                            </div>
                            <div class="card-body">
                                <form>
                                    <div class="mb-3">
                                        <label for="restaurantName" class="form-label">Restaurant Name</label>
                                        <input type="text" class="form-control" id="restaurantName" value="The Golden Spoon">
                                    </div>
                                    <div class="mb-3">
                                        <label for="restaurantAddress" class="form-label">Address</label>
                                        <textarea class="form-control" id="restaurantAddress" rows="3">123 Main Street, City</textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label for="restaurantPhone" class="form-label">Phone Number</label>
                                        <input type="tel" class="form-control" id="restaurantPhone" value="+1 (555) 123-4567">
                                    </div>
                                    <div class="mb-3">
                                        <label for="restaurantEmail" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="restaurantEmail" value="contact@goldenspoon.com">
                                    </div>
                                    <button type="submit" class="btn btn-primary">Save Changes</button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Settings -->
                    <div class="tab-pane fade" id="payment" role="tabpanel" aria-labelledby="payment-tab">
                        <div class="card mb-4">
                            <div class="card-header bg-white">
                                <h5 class="mb-0">Payment Methods</h5>
                            </div>
                            <div class="card-body">
                                <form>
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" id="cashPayment" checked>
                                        <label class="form-check-label" for="cashPayment">Cash Payment</label>
                                    </div>
                                
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" id="paypalPayment" checked>
                                        <label class="form-check-label" for="paypalPayment">PayPal</label>
                                    </div>
                                    <div class="mb-3">
                                        <label for="taxRate" class="form-label">Tax Rate (%)</label>
                                        <input type="number" class="form-control" id="taxRate" value="8.25" step="0.01">
                                    </div>
                                    <button type="submit" class="btn btn-primary">Save Changes</button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Notification Settings -->
                    <div class="tab-pane fade" id="notifications" role="tabpanel" aria-labelledby="notifications-tab">
                        <div class="card">
                            <div class="card-header bg-white">
                                <h5 class="mb-0">Notification Preferences</h5>
                            </div>
                            <div class="card-body">
                                <form>
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" id="newOrderNotifications" checked>
                                        <label class="form-check-label" for="newOrderNotifications">New Order Notifications</label>
                                    </div>
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" id="reservationNotifications" checked>
                                        <label class="form-check-label" for="reservationNotifications">Reservation Notifications</label>
                                    </div>
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" id="promotionNotifications">
                                        <label class="form-check-label" for="promotionNotifications">Promotion Notifications</label>
                                    </div>
                                    <div class="mb-3">
                                        <label for="notificationEmail" class="form-label">Notification Email</label>
                                        <input type="email" class="form-control" id="notificationEmail" value="admin@goldenspoon.com">
                                    </div>
                                    <button type="submit" class="btn btn-primary">Save Changes</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>