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
    <title>Menu Management - The Golden Spoon</title>
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
                            <a class="nav-link active" href="/admin/menu.php">
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
                    <h1 class="h2">Menu Management</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <!-- Update the button to trigger the modal -->
                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addItemModal">
    <i class="fas fa-plus"></i> Add Menu
</button>
                    </div>
                </div>

                <!-- Search and Filter -->
                <div class="row mb-4">
                    <div class="col-md-8">
                        <div class="search-box">
                            <i class="fas fa-search"></i>
                            <input type="text"  id="search-input" class="form-control" placeholder="Search menu items...">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <select class="form-select">
                            <option selected>All Categories</option>
                            <option>Appetizers</option>
                            <option>Main Courses</option>
                            <option>Desserts</option>
                            <option>Drinks</option>
                        </select>
                    </div>
                </div>

               
                <div class="card">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">All Menu Items</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Image</th>
                                        <th>Name</th>
                                        <th>Quantity</th>
                                        <th>Category</th>
                                        <th>Price</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id='menu-table-body'>
                
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Item Modal -->
<div class="modal fade" id="addItemModal" tabindex="-1" aria-labelledby="addItemModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addItemModalLabel">Add New Menu Item</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="add_menu_item.php" method="POST" enctype="multipart/form-data">
            <div class="modal-body">
                        
                    <div class="mb-3">
                        <label for="item_name" class="form-label">Item Name</label>
                        <input type="text" class="form-control" id="item_name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="item_description" class="form-label">Description</label>
                        <textarea class="form-control" id="item_description" name="description" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="item_price" class="form-label">Price</label>
                        <input type="number" step="0.01" class="form-control" id="item_price" name="price" required>
                    </div>
                    <div class="mb-3">
                        <label for="item_quantity" class="form-label">Quantity</label>
                        <input type="number" class="form-control" id="item_quantity" name="quantity" required>
                    </div>
                    <div class="mb-3">
                        <label for="item_category" class="form-label">Category</label>
                        <select class="form-select" id="item_category" name="category" required>
                            <option value="">Select Category</option>
                            <option value="Appetizers">Appetizers</option>
                            <option value="Main Courses">Main Courses</option>
                            <option value="Desserts">Desserts</option>
                            <option value="Drinks">Drinks</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="item_image" class="form-label">Image</label>
                        <input type="file" class="form-control" id="item_image" name="image" accept="image/*">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Item</button>
                </div>
            </form>
        </div>
    </div>
</div>


<div class="modal fade" id="editItemModal" tabindex="-1" aria-labelledby="editItemModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addItemModalLabel">Edit</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <center> <img src="" class="img img-responsive img-thumbnail"  id="edit_img" alt="menu img"></center>
            <form action="edit_menu.php" method="POST" enctype="multipart/form-data">
                
                <div class="modal-body">
                <input type="hidden" id="edit_item_id" name="id">
                <input type="hidden" id="edit_old_image" name="old_image">
                    <div class="mb-3">
                        <label for="edit_item_name" class="form-label">Item Name</label>
                        <input type="text" class="form-control" id="edit_item_name" name="name" required>

                    </div>
                    <div class="mb-3">
                        <label for="edit_item_description" class="form-label">Description</label>
                        <textarea class="form-control" id="edit_item_description" name="description" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="edit_item_quantity" class="form-label">Quantity</label>
                        <input type="number" class="form-control" id="edit_item_quantity" name="quantity" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_item_price" class="form-label">Price</label>
                        <input type="number" step="0.01" class="form-control" id="edit_item_price" name="price" required>
                    </div>
                 
                    <div class="mb-3">
                        <label for="edit_item_category" class="form-label">Category</label>
<input type="text" class="form-control" id="edit_item_category" name="category" placeholder="Enter category" required>
                    </div>
                    <div class="mb-3">
                        <input type="file" class="form-control d-none" id="edit_item_image" name="image" accept="image/*" >
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
      


// Add event listeners for edit and delete buttons
document.addEventListener('DOMContentLoaded', function() {
    // Prevent duplicate event listeners by using event delegation where possible
    const menuTable = document.getElementById('menu-table');
    if (menuTable) {
        menuTable.addEventListener('click', function(event) {
            const target = event.target;
            if (target.classList.contains('edit-btn')) {
                // Handle edit button click
                const id = this.getAttribute('data-id');
                const name = this.getAttribute('data-name');
                const description = this.getAttribute('data-description');
                const price = this.getAttribute('data-price');
                const quantity = this.getAttribute('data-quantity');
                const img = this.getAttribute('data-image');
                const category = this.getAttribute('data-category');
                

                document.getElementById('edit_item_id').value = id;
                document.getElementById('edit_item_name').value = name;
                document.getElementById('edit_item_description').value = description;
                document.getElementById('edit_item_price').value = price;
                document.getElementById('edit_item_quantity').value = quantity;
                document.getElementById('edit_item_category').value = category;
                
                document.getElementById('edit_img').src = '../images/' + img;
                document.getElementById('edit_old_image').value = img;

                // Make image clickable to trigger file input
                document.getElementById('edit_img').addEventListener('click', function() {
                    document.getElementById('edit_item_image').click();
                });

                // Preview uploaded image immediately
                document.getElementById('edit_item_image').addEventListener('change', function() {
                    if (this.files && this.files[0]) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            document.getElementById('edit_img').src = e.target.result;
                        };
                        reader.readAsDataURL(this.files[0]);
                    }
                });
                // Show modal
                var editModal = new bootstrap.Modal(document.getElementById('editItemModal'));
                editModal.show();
            });
        }
        });
    });
    document.querySelectorAll('.edit-btn').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const name = this.getAttribute('data-name');
            const description = this.getAttribute('data-description');
            const price = this.getAttribute('data-price');
            const quantity = this.getAttribute('data-quantity');
            const img = this.getAttribute('data-image');
            const category = this.getAttribute('data-category');
            

            document.getElementById('edit_item_id').value = id;
            document.getElementById('edit_item_name').value = name;
            document.getElementById('edit_item_description').value = description;
            document.getElementById('edit_item_price').value = price;
            document.getElementById('edit_item_quantity').value = quantity;
            document.getElementById('edit_item_category').value = category;
            
            document.getElementById('edit_img').src = '../images/' + img;
            document.getElementById('edit_old_image').value = img;

            // Make image clickable to trigger file input
            document.getElementById('edit_img').addEventListener('click', function() {
                document.getElementById('edit_item_image').click();
            });

            // Preview uploaded image immediately
            document.getElementById('edit_item_image').addEventListener('change', function() {
                if (this.files && this.files[0]) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        document.getElementById('edit_img').src = e.target.result;
                    };
                    reader.readAsDataURL(this.files[0]);
                }
            });
            // Show modal
            var editModal = new bootstrap.Modal(document.getElementById('editItemModal'));
            editModal.show();
        });
    });
});
});
document.getElementById('search-input').addEventListener('keyup', function() {
    const searchTerm = this.value.toLowerCase();
    const rows = document.querySelectorAll('.table tbody tr');
    fetch('search_menu.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'search=' + encodeURIComponent(searchTerm)
    })
    .then(response => response.text())
    .then(data => {
        document.getElementById('menu-table-body').innerHTML = data;
    })
});

    </script>
</body>
</html>