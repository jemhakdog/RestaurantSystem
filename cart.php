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

// Fetch user information from the database (optional for this demo)
include('db.php');

// Handle updating cart item quantity
if (isset($_POST['update_quantity'])) {
    $menu_id = $_POST['menu_id'];
    $quantity = $_POST['quantity'];

    // Update quantity if it's a valid number
    if ($quantity > 0) {
        $_SESSION['cart'][$menu_id]['quantity'] = $quantity;
    } else {
        // If quantity is 0 or less, remove the item from the cart
        unset($_SESSION['cart'][$menu_id]);
    }
}   

// Handle removing an item from the cart
if (isset($_POST['remove_item'])) {
    $menu_id = $_POST['menu_id'];
    unset($_SESSION['cart'][$menu_id]);
}

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
    <title>Cart - The Golden Spoon</title>
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

        .cart-item-actions {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .cart-item-actions input {
            width: 50px;
            padding: 5px;
            font-size: 1em;
            text-align: center;
        }

        .cart-item-actions button {
            background-color: #e74c3c;
            color: white;
            border: none;
            padding: 5px;
            font-size: 1em;
            cursor: pointer;
            border-radius: 10px;
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
        }

        .checkout-btn:hover {
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

        <?php if (!empty($_SESSION['cart'])): ?>
            <?php foreach ($_SESSION['cart'] as $menu_id => $item): ?>
                <div class="cart-item">
                    <img src="images/<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                    <div class="cart-item-details">
                        <h3><?php echo htmlspecialchars($item['name']); ?></h3>
                        
                        <p class="price">PHP:<?php echo number_format($item['price'], 2); ?></p>
                    </div>
                    <div class="cart-item-actions">
                        <form action="cart.php" method="POST">
                            <input type="hidden" name="menu_id" value="<?php echo $menu_id; ?>">
                            <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" min="1" required>
                            <button type="submit" name="update_quantity">Update</button>
                        </form>
                        <form action="cart.php" method="POST">
                            <input type="hidden" name="menu_id" value="<?php echo $menu_id; ?>">
                            <button type="submit" name="remove_item">Remove</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>

            <div class="total-price">
                Total: $<?php echo number_format($total_price, 2); ?>
            </div>

            <a href="checkout.php" class="checkout-btn">Proceed to Checkout</a>
        <?php else: ?>
            <p>Your cart is empty.</p>
        <?php endif; ?>

    </div>

</body>
</html>
