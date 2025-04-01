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

// Fetch user's saved addresses
$query = "SELECT * FROM address WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
$addresses = $result->fetch_all(MYSQLI_ASSOC);

// Calculate the total price of the cart
$total_price = 0;
foreach ($_SESSION['cart'] as $item) {
    $total_price += $item['price'] * $item['quantity'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - The Golden Spoon</title>
    <script src="https://www.paypal.com/sdk/js?client-id=YOUR_PAYPAL_CLIENT_ID&currency=USD"></script>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            color: #444;
        }

        .navbar {
            background-color: #333;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .navbar h1 {
            color: #f1c40f;
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
            transform: scale(1.05);
        }

        .navbar a.active {
            background-color: #f39c12;
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
            <?php foreach ($_SESSION['cart'] as $menu_id => $item): ?>
                <div class="cart-item">
                    <img src="images/<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                    <div class="cart-item-details">
                        <h3><?php echo htmlspecialchars($item['name']); ?></h3>
                        <p>Quantity: <?php echo htmlspecialchars($item['quantity']); ?></p>
                        <p class="price">$<?php echo number_format($item['price'], 2); ?></p>
                        <p class="subtotal">Subtotal: $<?php echo number_format($item['price'] * $item['quantity'], 2); ?></p>
                    </div>
                </div>
            <?php endforeach; ?>

            <div class="total-price">
                Total: $<?php echo number_format($total_price, 2); ?>
            </div>
            <form action="order-confirmation.php" method="POST" id="checkout-form">
                <!-- Fix the cart data structure -->
                <?php foreach ($_SESSION['cart'] as $menu_id => $item): ?>
                    <input type="hidden" name="cart[<?php echo $menu_id; ?>][menu_id]" value="<?php echo $menu_id; ?>">
                    <input type="hidden" name="cart[<?php echo $menu_id; ?>][quantity]" value="<?php echo $item['quantity']; ?>">
                    <input type="hidden" name="cart[<?php echo $menu_id; ?>][price]" value="<?php echo $item['price']; ?>">
                    <input type="hidden" name="cart[<?php echo $menu_id; ?>][name]" value="<?php echo $item['name']; ?>">
                <?php endforeach; ?>
                <input type="hidden" name="total_amount" value="<?php echo $total_price; ?>">

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
                        <input type="radio" id="gcash" name="payment_method" value="gcash">
                        <label for="gcash">Pay with GCash</label>
                    </div>
                    <div>
                        <input type="radio" id="bank_transfer" name="payment_method" value="bank_transfer">
                        <label for="bank_transfer">Bank Transfer</label>
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


        // Initialize PayPal button on selection
        paypal.Buttons({
            createOrder: function(data, actions) {
                return actions.order.create({
                    purchase_units: [{
                        amount: {
                            value: '<?php echo number_format($total_price, 2); ?>'
                        }
                    }]
                });
            },
            onApprove: function(data, actions) {
                return actions.order.capture().then(function(details) {
                    alert('Payment successful! Order confirmed.');
                    document.getElementById('checkout-form').submit();  // Submit the checkout form
                });
            }
        }).render('#paypal-button-container');
    </script>

</body>
</html>
