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

// Fetch user information from the database (optional for this demo)
include('db.php');

// Get categories from the database
$category_sql = "SELECT DISTINCT category FROM menu";
$category_result = $conn->query($category_sql);

// Get search term if it's set
$search_term = isset($_GET['search']) ? $_GET['search'] : '';
$category_filter = isset($_GET['category']) ? $_GET['category'] : '';

// Fetch menu items, with optional category filtering and search term
$sql = "SELECT * FROM menu WHERE 1";
if ($category_filter) {
    $sql .= " AND category = ?";
}
if ($search_term) {
    $sql .= " AND name LIKE ?";
}

$stmt = $conn->prepare($sql);
if ($category_filter && $search_term) {
    $search_term = "%" . $search_term . "%";
    $stmt->bind_param('ss', $category_filter, $search_term);
} elseif ($category_filter) {
    $stmt->bind_param('s', $category_filter);
} elseif ($search_term) {
    $search_term = "%" . $search_term . "%";
    $stmt->bind_param('s', $search_term);
}
$stmt->execute();
$result = $stmt->get_result();

// Handle Add to Cart action
if (isset($_POST['add_to_cart'])) {
    $menu_id = $_POST['menu_id'];
    
    // First, check if the menu item exists and has available quantity
    $sql = "SELECT * FROM menu WHERE menu_id = ? AND quantity > 0";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $menu_id);
    $stmt->execute();
    $item_result = $stmt->get_result();
    $item = $item_result->fetch_assoc();

    if ($item) {
        // Check if the item is already in the user's cart
        $check_cart = "SELECT * FROM cart_items WHERE user_id = ? AND menu_id = ?";
        $check_stmt = $conn->prepare($check_cart);
        $check_stmt->bind_param('ii', $user_id, $menu_id);
        $check_stmt->execute();
        $cart_result = $check_stmt->get_result();

        if ($cart_result->num_rows > 0) {
            // Check if adding one more exceeds available quantity
            $cart_item = $cart_result->fetch_assoc();
            if ($cart_item['quantity'] + 1 <= $item['quantity']) {
                // Item exists in cart, update quantity
                $update_sql = "UPDATE cart_items SET quantity = quantity + 1 WHERE user_id = ? AND menu_id = ?";
                $update_stmt = $conn->prepare($update_sql);
                $update_stmt->bind_param('ii', $user_id, $menu_id);
                $update_stmt->execute();

                // Update session cart
                $_SESSION['cart'][$menu_id]['quantity']++;
            } else {
                echo "<script>Swal.fire({icon: 'error', title: 'Not enough quantity', text: 'Not enough quantity available!'});</script>";
            }
        } else {
            // Item doesn't exist in cart, insert new record
            $insert_sql = "INSERT INTO cart_items (user_id, menu_id, quantity) VALUES (?, ?, 1)";
            $insert_stmt = $conn->prepare($insert_sql);
            $insert_stmt->bind_param('ii', $user_id, $menu_id);
            $insert_stmt->execute();

            // Update session cart
            $_SESSION['cart'][$menu_id] = [
                'name' => $item['name'],
                'price' => $item['price'],
                'quantity' => 1,
                'image' => $item['image']
            ];
        }
    } else {
        echo "<script>Swal.fire({icon: 'error', title: 'Out of stock', text: 'Item is out of stock!'});</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>The Golden Spoon - Menu</title>
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
            padding: 50px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(1, 1, 1, 1.1);
        }

        .category-filter {
            text-align: center;
            margin-bottom: 20px;
        }

        .category-filter select, .category-filter input {
            font-size: 1em;
            padding: 5px;
            width: 200px;
            border-radius: 5px;
            border: 2px solid #ddd;
        }

        .category-filter button {
            background-color: #f39c12;
            padding: 10px 10px;
            border: none;
            cursor: pointer;
            font-size: 1.1em;
            border-radius: 5px;
        }

        .category-filter button:hover {
            background-color: #e67e22;
        }

        .menu-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 30px;
        }

        .menu-item {
            background-color: #fff;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(1, 1, 1, 0.5);
            transition: transform 0.3s ease;
        }

        .menu-item:hover {
            transform: translateY(-5px);
        }

        .menu-item img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 8px;
        }

        .menu-item-details {
            padding: 20px;
        }

        .menu-item-details h3 {
            font-size: 1.5em;
            color: #333;
            margin-bottom: 10px;
        }

        .menu-item-details p {
            color: #555;
            margin-bottom: 10px;
        }

        .menu-item-details .price {
            font-size: 1.2em;
            color: #f39c12;
            margin-bottom: 15px;
        }

        .add-to-cart-btn {
            background-color: #f39c12;
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 1em;
            cursor: pointer;
            border-radius: 5px;
        }

        .add-to-cart-btn:hover {
            background-color: #e67e22;
        }

        .footer {
            background-color: #333;
            color: #fff;
            padding: 20px;
            text-align: center;
        }

        .footer a {
            color: #f39c12;
            text-decoration: none;
        }

        .footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <div class="navbar">
        <h1>The Golden Spoon</h1>
        <div>
            <a href="dashboard.php">Home</a>
            <a href="menu.php" class="active">Menu</a>
            <a href="cart.php">Cart</a>
            <a href="reservation.php">Reservation</a>
            <a href="profile.php">Profile</a>
<a href="history.php">History</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>

    <!-- Category Filter and Search -->
    <div class="container">
        <h2>Our Menu</h2>

        <div class="category-filter">
            <form action="menu.php" method="GET">
                <label for="category">Choose a Category:</label>
                <select name="category" id="category" onchange="this.form.submit()">
                    <option value="">All Categories</option>
                    <?php while ($category_row = $category_result->fetch_assoc()) { ?>
                        <option value="<?php echo $category_row['category']; ?>"
                            <?php echo $category_row['category'] == $category_filter ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($category_row['category']); ?>
                        </option>
                    <?php } ?>
                </select>

                <label for="search">Search:</label>
                <input type="text" name="search" id="search" placeholder="Search for a dish" value="<?php echo htmlspecialchars($search_term); ?>">
                <button type="submit">Go</button>
            </form>
        </div>

        <!-- Menu Items -->
        <div class="menu-grid">
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="menu-item">
                        <img src="images/<?php echo htmlspecialchars($row['image']); ?>" alt="<?php echo htmlspecialchars($row['name']); ?>">
                        <div class="menu-item-details">
                            <h3><?php echo htmlspecialchars($row['name']); ?></h3>
                            <p><?php echo htmlspecialchars($row['description']); ?></p>
                            <p class="price">PHP:<?php echo number_format($row['price'], 2); ?></p>
                            <p>Available: <?php echo $row['quantity']; ?></p>
                        </div>
                        <form action="menu.php" method="POST">
                            <input type="hidden" name="menu_id" value="<?php echo $row['menu_id']; ?>">
                            <button type="submit" name="add_to_cart" class="add-to-cart-btn" <?php echo $row['quantity'] <= 0 ? 'disabled' : ''; ?>>
                                <?php echo $row['quantity'] <= 0 ? 'Out of Stock' : 'Add to Cart'; ?>
                            </button>
                        </form>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No items found based on your filters.</p>
            <?php endif; ?>
        </div>
    </div>


    <script>
        // AJAX search function
        function searchMenu() {
            var searchTerm = document.getElementById('search').value;
            var categoryFilter = document.getElementById('category').value;

            var xhr = new XMLHttpRequest();
            xhr.open('GET', 'search.php?search=' + searchTerm + '&category=' + categoryFilter, true);
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    document.getElementById('menu-results').innerHTML = xhr.responseText;
                }
            };
            xhr.send();
        }

        // Highlight search term
        function highlightText(text, term) {
            var regEx = new RegExp('(' + term + ')', 'gi');
            return text.replace(regEx, '<span class="highlight">$1</span>');
        }
    </script>

</body>



</html>

<?php
// Close database connection
$conn->close();
?>
