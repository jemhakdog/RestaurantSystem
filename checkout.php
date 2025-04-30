<?php
session_start();

// Ensure the cart is initialized if it doesn't exist
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = array();
}

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

// Get user details from session
$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

// Database connection
include('db.php');

// Handle saving new address
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['address'])) {
    header('Content-Type: application/json');
    
    $address = $_POST['address'];
    $user_id = $_SESSION['user_id'];
    
    try {
        // Insert new address
        $stmt = $conn->prepare("INSERT INTO address (user_id, address) VALUES (?, ?)");
        $stmt->bind_param('is', $user_id, $address);
        $stmt->execute();
        
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit();
}

// Fetch user's saved addresses
$query = "SELECT * FROM address WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
$addresses = $result->fetch_all(MYSQLI_ASSOC);

// Calculate the total price of the cart
$total_price = 0;
$selected_items = json_decode($_POST['selected_items'] ?? '[]', true);
foreach ($_SESSION['cart'] as $menu_id => $item) {
    if (!empty($selected_items) && !in_array($menu_id, $selected_items)) continue;
    $total_price += $item['price'] * $item['quantity'];
}


require __DIR__ . '/vendor/autoload.php'; // Include Composer's autoload

// Load environment variables from .env file
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Access environment variables
$paypal_client_id = $_ENV['PAYPAL_CLIENT_ID'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - The Golden Spoon</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script src="https://www.paypal.com/sdk/js?client-id=<?php echo htmlspecialchars($paypal_client_id); ?>&currency=PHP"></script>
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

        .total-price {
            text-align: right;
            font-size: 1.5em;
            font-weight: bold;
            margin-top: 40px;
        }

        .payment-methods {
            margin-top: 40px;
        }

        .payment-methods h3 {
            margin-bottom: 20px;
            color: #333;
        }

        .payment-methods form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .payment-methods input[type="radio"] {
            margin-right: 10px;
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
            transition: background-color 0.3s ease;
        }

        .checkout-btn:hover {
            background-color: #e67e22;
        }

        .paypal-button-container {
            margin-top: 30px;
        }
    .item-notes {
        width: 100%;
        margin-top: 10px;
        padding: 8px;
        border: 1px solid #ddd;
        border-radius: 4px;
        resize: vertical;
        min-height: 60px;
        font-family: inherit;
    }

    .item-notes:focus {
        border-color: var(--primary);
        outline: none;
        box-shadow: 0 0 5px rgba(255, 159, 28, 0.3);
    }

        .dine-option {
            margin-top: 30px;
            background-color: #f9f9f9;
            padding: 15px;
            border-radius: 5px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .dine-option label {
            font-size: 1.1em;
            margin-right: 10px;
            cursor: pointer;
        }

        .dine-option input[type="radio"] {
            margin-right: 10px;
            cursor: pointer;
        }

        #delivery-address {
            margin-top: 20px;
            background-color: #f9f9f9;
            padding: 15px;
            border-radius: 5px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        #delivery-address input[type="text"], #delivery-address select {
            width: 100%;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            margin-top: 10px;
            font-size: 1em;
        }

        .address-option {
            margin-bottom: 15px;
        }

        .new-address-form {
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
        }

    </style>
    <script>
        // Add this at the beginning of your script section
        // Sync notes from textarea to hidden input
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.item-notes').forEach(function(textarea) {
                textarea.addEventListener('input', function() {
                    const menuId = this.dataset.menuId;
                    document.getElementById('notes-hidden-' + menuId).value = this.value;
                });
                // Initial sync on page load
                const menuId = textarea.dataset.menuId;
                document.getElementById('notes-hidden-' + menuId).value = textarea.value;
            });
        });

        // Toggle the delivery address field visibility based on the selection
        document.querySelectorAll('input[name="service_type"]').forEach(function(input) {
            input.addEventListener('change', function() {
                if (document.getElementById('delivery').checked) {
                    document.getElementById('delivery-address').style.display = 'block';
                } else {
                    document.getElementById('delivery-address').style.display = 'none';
                }
            });
        });

        // Toggle between saved and new address
        function toggleAddressForm() {
            const addressOption = document.getElementById('address_option');
            const savedAddresses = document.getElementById('saved-addresses');
            const newAddressForm = document.getElementById('new-address-form');
            
            if (addressOption.value === 'saved') {
                savedAddresses.style.display = 'block';
                newAddressForm.style.display = 'none';
            } else {
                savedAddresses.style.display = 'none';
                newAddressForm.style.display = 'block';
            }
        }

        // Initial toggle and add event listener
        document.getElementById('address_option').addEventListener('change', toggleAddressForm);
        toggleAddressForm(); // Call immediately to set initial state


        // Payment method toggle functionality
        document.querySelectorAll('input[name="payment_method"]').forEach(function(input) {
            input.addEventListener('change', function() {
                const paypalContainer = document.getElementById('paypal-button-container');
                if (this.value === 'paypal') {
                    paypalContainer.style.display = 'block';
                } else {
                    paypalContainer.style.display = 'none';
                }
            });
        });

        // Initialize with PayPal hidden (since COD is default)
        document.getElementById('paypal-button-container').style.display = 'none';

        paypal.Buttons({
            createOrder: function(data, actions) {
                return actions.order.create({
                    purchase_units: [{
                        amount: {
                            currency_code: 'PHP', // Philippine Peso
                            value: <?php echo $total_price?>      // Total amount
                        },
                        description: 'Payment for your order'
                    }]
                });
            },
            onApprove: function(data, actions) {
                return actions.order.capture().then(function(details) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Transaction completed',
                        text: 'Transaction completed by ' + details.payer.name.given_name
                    });
                    Swal.fire({
                        title: 'Transaction Details',
                        text: JSON.stringify(details, null, 2),
                        icon: 'info'
                    });
                    // // Redirect or handle success here
                    // window.location.href = '/success.html';
                });
            },
            onCancel: function(data) {
                Swal.fire({
                    title: 'Payment Cancelled',
                    text: 'You have cancelled the payment process.',
                    icon: 'warning'
                });
                // // Redirect or handle cancellation here
                // window.location.href = '/cancel.html';
            },
            onError: function(err) {
                console.error('Error during payment:', err);
                Swal.fire({
                    title: 'Error',
                    text: 'An error occurred during payment.',
                    icon: 'error'
                });
            }
        }).render('#paypal-button-container');
    </script>
</head>
<body>

    <!-- Navbar -->
    <div class="navbar">
        <h1>The Golden Spoon</h1>
        <div>
            <a href="dashboard.php">Home</a>
            <a href="menu.php">Menu</a>
            <a href="cart.php">Cart</a>
            <a href="reservation.php">Reservation</a>
            <a href="profile.php">Profile</a>
<a href="history.php">History</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>

    <!-- Checkout Content -->
    <div class="container">
        <h2>Your Cart</h2>

        <?php if (!empty($_SESSION['cart'])): ?>
            <?php 
            $selected_items = json_decode($_POST['selected_items'] ?? '[]', true);
            foreach ($_SESSION['cart'] as $menu_id => $item): 
                if (!empty($selected_items) && !in_array($menu_id, $selected_items)) continue;
            ?>
                <div class="cart-item">
                    <img src="images/<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                    <div class="cart-item-details">
                        <h3><?php echo htmlspecialchars($item['name']); ?></h3>
                        <p>Quantity: <?php echo htmlspecialchars($item['quantity']); ?></p>
                        <p class="price">PHP:<?php echo number_format($item['price'], 2); ?></p>
                        <p class="subtotal">Subtotal: PHP:<?php echo number_format($item['price'] * $item['quantity'], 2); ?></p>
                        <textarea name="notes[<?php echo $menu_id; ?>]" class="item-notes" data-menu-id="<?php echo $menu_id; ?>" placeholder="Add special instructions for this item..."><?php echo isset($item['notes']) ? htmlspecialchars($item['notes']) : ''; ?></textarea>
                    </div>
                </div>
            <?php endforeach; ?>

            <div class="total-price">
                Total: PHP:<?php echo number_format($total_price, 2); ?>
            </div>
            <form action="order-confirmation.php" method="POST" id="checkout-form">
                <!-- Fix the cart data structure -->
                <?php foreach ($_SESSION['cart'] as $menu_id => $item): ?>
                    <input type="hidden" name="cart[<?php echo $menu_id; ?>][menu_id]" value="<?php echo $menu_id; ?>">
                    <input type="hidden" name="cart[<?php echo $menu_id; ?>][quantity]" value="<?php echo $item['quantity']; ?>">
                    <input type="hidden" name="cart[<?php echo $menu_id; ?>][price]" value="<?php echo $item['price']; ?>">
                    <input type="hidden" name="cart[<?php echo $menu_id; ?>][name]" value="<?php echo $item['name']; ?>">
                    <input type="hidden" name="cart[<?php echo $menu_id; ?>][notes]" id="notes-hidden-<?php echo $menu_id; ?>" value="<?php echo isset($item['notes']) ? htmlspecialchars($item['notes']) : ''; ?>">
                <?php endforeach; ?>
                <input type="hidden" name="total_amount" value="<?php echo $total_price; ?>">
                <input type="hidden" name="selected_items" value="<?php echo htmlspecialchars(json_encode($selected_items)); ?>">

            <!-- Dine-in, Takeout, Delivery Option -->
            <div class="dine-option">
                <h3>Select an Option:</h3>
                <label for="dine-in">
                    <input type="radio" id="dine-in" name="service_type" value="dine-in" required>
                    Dine-in
                </label><br>
                <label for="pickup">
                    <input type="radio" id="pickup" name="service_type" value="pickup">
                    Pickup
                </label><br>
                <label for="delivery">
                    <input type="radio" id="delivery" name="service_type" value="delivery">
                    Delivery
                </label>
            </div>

            <!-- Delivery Address (only appears if Delivery is selected) -->
            <div id="delivery-address" style="display:none;">
                <h3>Delivery Address</h3>
                <div class="address-option">
                    <select name="address_option" id="address_option">
                        <option value="saved" <?php echo empty($addresses) ? '' : 'selected'; ?>>Use Saved Address</option>
                        <option value="new" <?php echo empty($addresses) ? 'selected' : ''; ?>>Add New Address</option>
                    </select>
                </div>

                <div id="saved-addresses">
                    <select name="saved_address" id="saved_address">
                        <?php foreach($addresses as $address): ?>
                            <option value="<?php echo htmlspecialchars($address['address']); ?>">
                                <?php echo htmlspecialchars($address['address']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div id="new-address-form" class="new-address-form" style="display:none;">
                    <input type="text" name="new_address" id="new_address" placeholder="Enter your new delivery address">
                    <button type="button" class="checkout-btn" style="margin-top: 10px;" onclick="saveNewAddress()">Add</button>
                    <script>
                    function saveNewAddress() {
                        const newAddress = document.getElementById('new_address').value;
                        if (!newAddress) {
                            Swal.fire('Please enter a valid address');
                            return;
                        }
                        
                        fetch('save_address.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({
                                address: newAddress
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire('Address saved successfully!');
                                window.location.reload();
                            } else {
                                Swal.fire('Error saving address: ' + data.message);
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire('Error', 'An error occurred while saving the address', 'error');
                        });
                    }
                    </script>
                </div>
            </div>

            <!-- Payment Methods Section -->
            <div class="payment-methods">
                <h3>Select a Payment Method:</h3>
                    <div>
                        <input type="radio" id="paypal" name="payment_method" value="paypal" required>
                        <label for="paypal">Pay with PayPal</label>
                    </div>
                    <div>
                        <input type="radio" id="cod" name="payment_method" value="cod" required checked>
                        <label for="cod">Cash on Delivery</label>
                    </div>
                    

                    <div class="paypal-button-container" id="paypal-button-container"></div>

                    <!-- Checkout Button -->
                    <button type="submit" class="checkout-btn">Proceed to Payment</button>
                </form>
            </div>

        <?php else: ?>
            <p>Your cart is empty.</p>
        <?php endif; ?>

    </div>

    <script>
        // Toggle the delivery address field visibility based on the selection
        document.querySelectorAll('input[name="service_type"]').forEach(function(input) {
            input.addEventListener('change', function() {
                if (document.getElementById('delivery').checked) {
                    document.getElementById('delivery-address').style.display = 'block';
                } else {
                    document.getElementById('delivery-address').style.display = 'none';
                }
            });
        });

        // Toggle between saved and new address
        function toggleAddressForm() {
            const addressOption = document.getElementById('address_option');
            const savedAddresses = document.getElementById('saved-addresses');
            const newAddressForm = document.getElementById('new-address-form');
            
            if (addressOption.value === 'saved') {
                savedAddresses.style.display = 'block';
                newAddressForm.style.display = 'none';
            } else {
                savedAddresses.style.display = 'none';
                newAddressForm.style.display = 'block';
            }
        }

        // Initial toggle and add event listener
        document.getElementById('address_option').addEventListener('change', toggleAddressForm);
        toggleAddressForm(); // Call immediately to set initial state


        // Payment method toggle functionality
        document.querySelectorAll('input[name="payment_method"]').forEach(function(input) {
            input.addEventListener('change', function() {
                const paypalContainer = document.getElementById('paypal-button-container');
                if (this.value === 'paypal') {
                    paypalContainer.style.display = 'block';
                } else {
                    paypalContainer.style.display = 'none';
                }
            });
        });

        // Initialize with PayPal hidden (since COD is default)
        document.getElementById('paypal-button-container').style.display = 'none';

        paypal.Buttons({
            createOrder: function(data, actions) {
                return actions.order.create({
                    purchase_units: [{
                        amount: {
                            currency_code: 'PHP', // Philippine Peso
                            value: <?php echo $total_price?>      // Total amount
                        },
                        description: 'Payment for your order'
                    }]
                });
            },
            onApprove: function(data, actions) {
                return actions.order.capture().then(function(details) {
                    Swal.fire('Success', 'Transaction completed by ' + details.payer.name.given_name, 'success');
                    Swal.fire({
                        title: 'Transaction Details',
                        text: JSON.stringify(details, null, 2),
                        icon: 'info'
                    });
                    // // Redirect or handle success here
                    // window.location.href = '/success.html';
                });
            },
            onCancel: function(data) {
                Swal.fire({
                    title: 'Payment Cancelled',
                    text: 'You have cancelled the payment process.',
                    icon: 'warning'
                });
                // // Redirect or handle cancellation here
                // window.location.href = '/cancel.html';
            },
            onError: function(err) {
                console.error('Error during payment:', err);
                Swal.fire({
                    title: 'Error',
                    text: 'An error occurred during payment.',
                    icon: 'error'
                });
            }
        }).render('#paypal-button-container');
    </script>

</body>
</html>
