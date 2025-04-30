<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

// Get user details from session
$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

include('db.php');

if (isset($_POST['update_quantity'])) {
    $menu_id = $_POST['menu_id'];
    $quantity = $_POST['quantity'];
    
    // Get available stock for this item
    $stock_sql = "SELECT quantity FROM menu WHERE menu_id = ?";
    $stock_stmt = $conn->prepare($stock_sql);
    $stock_stmt->bind_param('i', $menu_id);
    $stock_stmt->execute();
    $stock_result = $stock_stmt->get_result();
    $stock = $stock_result->fetch_assoc()['quantity'];
    
    if ($quantity > $stock) {
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Quantity cannot exceed available stock (" . $stock . ")'
            });
        </script>";
    } elseif ($quantity > 0) {
        $update_sql = "UPDATE cart_items SET quantity = ? WHERE user_id = ? AND menu_id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param('iii', $quantity, $user_id, $menu_id);
        if ($update_stmt->execute()) {
            echo "<script>
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: 'Cart quantity updated successfully!'
                });
            </script>";
        } else {
            echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to update cart quantity.'
                });
            </script>";
        }
    } else {
        // ... existing delete code for quantity 0 ...
        if ($delete_stmt->execute()) {
            echo "<script>
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: 'Item removed from cart!'
                });
            </script>";
        } else {
            echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to remove item from cart.'
                });
            </script>";
        }
    }
}   

// After remove item operation
if (isset($_POST['remove_item'])) {
    $menu_id = $_POST['menu_id'];
    $delete_sql = "DELETE FROM cart_items WHERE user_id = ? AND menu_id = ?";
    $delete_stmt = $conn->prepare($delete_sql);
    $delete_stmt->bind_param('ii', $user_id, $menu_id);
    if ($delete_stmt->execute()) {
        echo "<script>
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: 'Item removed from cart successfully!'
            });
        </script>";
    } else {
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Failed to remove item from cart.'
            });
        </script>";
    }
}

// Fetch cart items from database with menu details
$cart_sql = "SELECT ci.*, m.name, m.price, m.image, m.quantity as stock 
             FROM cart_items ci 
             JOIN menu m ON ci.menu_id = m.menu_id 
             WHERE ci.user_id = ?";
$cart_stmt = $conn->prepare($cart_sql);
$cart_stmt->bind_param('i', $user_id);
$cart_stmt->execute();
$cart_result = $cart_stmt->get_result();

// Initialize cart items array
// Remove this first initialization and loop since it's redundant
// $cart_items = [];
// while ($item = $cart_result->fetch_assoc()) {
//     $cart_items[] = $item;
// }

// Keep only this section for fetching cart items
$total_price = 0;
$cart_items = [];
while ($item = $cart_result->fetch_assoc()) {
    // If current quantity exceeds stock, adjust it to max available
    if ($item['quantity'] > $item['stock']) {
        $adjust_sql = "UPDATE cart_items SET quantity = ? WHERE user_id = ? AND menu_id = ?";
        $adjust_stmt = $conn->prepare($adjust_sql);
        $adjust_stmt->bind_param('iii', $item['stock'], $user_id, $item['menu_id']);
        $adjust_stmt->execute();
        $item['quantity'] = $item['stock'];
    }
    
    $cart_items[$item['menu_id']] = $item;
    $total_price += $item['price'] * $item['quantity'];
}

// Update session cart to match database
$_SESSION['cart'] = $cart_items;
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cart - The Golden Spoon</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
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

        .container {
            max-width: 1200px;
            margin: 50px auto;
            padding: 30px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(1, 1, 1, 1.1);
        }

        h2 {
            text-align: center;
            color: #333;
        }

        .cart-item {
            display: flex;
            justify-content: space-between;
            padding: 15px;
            border-bottom: 1px solid #ddd;
            align-items: center;
        }
        
        .item-checkbox {
            width: 20px;
            height: 20px;
            margin-right: 15px;
            accent-color: #2ecc71;
            transform: scale(1.5);
        }
        
        .item-checkbox:hover {
            cursor: pointer;
            opacity: 0.8;
        }

        .cart-item img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 5px;
        }

        .cart-item-details {
            flex-grow: 1;
            padding-left: 15px;
        }

        .cart-item-details h3 {
            font-size: 1.5em;
            margin: 0;
            color: #333;
        }

        .cart-item-details p {
            margin: 5px 0;
            color: #555;
        }

        .cart-item-details .price {
            font-size: 1.2em;
            color: #f39c12;
        }   

        .cart-item-actions {
            display: flex;
            flex-direction: column;
            gap: 10px;
            align-items: center;
        }

        .cart-item-actions input[type="number"] {
            width: 60px;
            padding: 5px;
            font-size: 1em;
            text-align: center;
            margin-bottom: 5px;
        }

        .cart-item-actions button {
            width: 100px;
            color: white;
            border: none;
            padding: 8px;
            font-size: 1em;
            cursor: pointer;
            border-radius: 5px;
            margin: 2px 0;
        }

        .update-btn {
            background-color: #2ecc71;
        }

        .update-btn:hover {
            background-color: #27ae60;
        }

        .remove-btn {
            background-color: #e74c3c;
        }

        .remove-btn:hover {
            background-color: #c0392b;
        }
        .cart-item-actions button[name="update_quantity"] {
            background-color: #2ecc71;
        }

        .cart-item-actions button:hover {
            background-color: #c0392b;
        }

        .total-price {
            text-align: right;
            font-size: 1.5em;
            font-weight: bold;
            margin-top: 40px;
        }

        .checkout-btn {
            display: block;
            width: 100%;
            background-color: #f39c12;
            color: white;
            padding: 15px;
            font-size: 1.2em;
            text-align: center;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 30px;
            border: none;
            cursor: pointer;
        }

        .checkout-btn:disabled {
            background-color: #ccc;
            cursor: not-allowed;
        }

        .checkout-btn:hover:not(:disabled) {
            background-color: #e67e22;
        }


    </style>
</head>
<body>

    <!-- Navbar -->
    <div class="navbar">
        <h1>The Golden Spoon</h1>
        <div>
            <a href="dashboard.php">Home</a>
            <a href="menu.php">Menu</a>
            <a href="cart.php" class="active">Cart</a>
            <a href="reservation.php">Reservation</a>
            <a href="profile.php">Profile</a>
<a href="history.php">History</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>

    <!-- Cart Content -->
    <div class="container">
        <h2>Your Cart</h2>

        <?php if (!empty($cart_items)): ?>
            <form id="checkout-form" action="checkout.php" method="POST">
                <?php foreach ($cart_items as $menu_id => $item): ?>
                    <div class="cart-item">
                    <input type="checkbox" class="item-checkbox"  name="selected[]" value="<?php echo $menu_id; ?>" data-price="<?php echo ($item['price'] * $item['quantity']); ?>" <?php echo ($item['stock'] == 0 || $item['quantity'] > $item['stock']) ? 'disabled' : ''; ?>>

                        <img src="images/<?php echo htmlspecialchars($item['image']); ?>" 
                             alt="<?php echo htmlspecialchars($item['name']); ?>">
                        <div class="cart-item-details">
                            <h3><?php echo htmlspecialchars($item['name']); ?></h3>
                            <p class="price">PHP <?php echo number_format($item['price'], 2); ?></p>
                            <p>Quantity: <?php echo $item['quantity']; ?> (Available: <?php echo $item['stock']; ?>)</p>
                            <p>Subtotal: PHP <?php echo number_format($item['price'] * $item['quantity'], 2); ?></p>
                        </div>
                        <div class="cart-item-actions">
                            <input type="hidden" name="menu_id" value="<?php echo $item['menu_id']; ?>">
                            <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" min="<?php echo ($item['stock'] == 0 || $item['quantity'] > $item['stock']) ? '0' : '1'; ?>" max="<?php echo $item['stock']; ?>" <?php echo ($item['stock'] == 0 || $item['quantity'] > $item['stock']) ? 'disabled' : ''; ?> required>
                            <button type="button" onclick="updateQuantity(<?php echo $item['menu_id']; ?>, this)" class="update-btn">Update</button>
                            <button type="button" onclick="removeItem(<?php echo $item['menu_id']; ?>, this)" class="remove-btn">Remove</button>
                        </div>
                    </div>
                <?php endforeach; ?>

                <div class="total-price">
                    Total: PHP <span id="total">0.00</span>
                </div>

                <input type="hidden" name="selected_items" id="selected-items-input">
                <button type="submit" class="checkout-btn" id="checkout-button">Proceed to Checkout</button>
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const form = document.getElementById('checkout-form');
                        const selectedItemsInput = document.getElementById('selected-items-input');
                        
                        form.addEventListener('submit', function(e) {
                            const selectedItems = [];
                            document.querySelectorAll('input[name="selected[]"]:checked').forEach(checkbox => {
                                selectedItems.push(checkbox.value);
                            });
                            selectedItemsInput.value = JSON.stringify(selectedItems);
                        });
                    });
                </script>
            </form>
        <?php else: ?>
            <p>Your cart is empty.</p>
        <?php endif; ?>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const checkboxes = document.querySelectorAll('.item-checkbox');
            const totalSpan = document.getElementById('total');
            const checkoutButton = document.getElementById('checkout-button');
            const checkoutForm = document.getElementById('checkout-form');
            const selectedItemsInput = document.getElementById('selected-items-input');

            function updateTotal(event) {
                let total = 0;
                let selectedItems = [];

                checkboxes.forEach(checkbox => {
                    if (checkbox.checked && !checkbox.disabled) {
                        total += parseFloat(checkbox.dataset.price);
                        selectedItems.push(checkbox.value);
                    } else if (checkbox.disabled && event && event.type === 'click') {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Item Unavailable',
                            text: 'The item "' + checkbox.closest('.cart-item').querySelector('h3').textContent + '" is out of stock or quantity exceeds available stock.'
                        });
                        checkbox.checked = false;
                    }
                });

                totalSpan.textContent = total.toFixed(2);
                selectedItemsInput.value = JSON.stringify(selectedItems);
                
                // Enable/disable checkout button based on selection
                checkoutButton.disabled = selectedItems.length === 0;
            }

            // Add event listeners to all checkboxes
            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', updateTotal);
            });

            // Initialize total
            updateTotal();
        });
    </script>

<script>
    function updateQuantity(menuId, button) {
        const quantityInput = button.parentElement.querySelector('input[type="number"]');
        const quantity = quantityInput.value;

        fetch('cart.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `update_quantity=1&menu_id=${menuId}&quantity=${quantity}`
        })
        .then(response => response.text())
        .then(html => {
            // Refresh the page to update the cart
            location.reload();
        })
        .catch(error => {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Failed to update cart quantity.'
            });
        });
    }

    function removeItem(menuId, button) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, remove it!'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('cart.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `remove_item=1&menu_id=${menuId}`
                })
                .then(response => response.text())
                .then(html => {
                    // Remove the cart item from DOM
                    const cartItem = button.closest('.cart-item');
                    cartItem.remove();
                    
                    // Update total price
                    updateTotal();
                    
                    // Show success message
                    Swal.fire(
                        'Removed!',
                        'Item has been removed from cart.',
                        'success'
                    );
                    
                    // Reload if cart is empty
                    if (document.querySelectorAll('.cart-item').length === 0) {
                        location.reload();
                    }
                })
                .catch(error => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to remove item from cart.'
                    });
                });
            }
        });
    }
</script>
</body>
</html>
