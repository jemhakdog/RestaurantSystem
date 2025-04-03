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

// Fetch user information from the database
include('db.php');
$sql = "SELECT * FROM users WHERE user_id = '$user_id'";
$result = $conn->query($sql);
$user = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>The Golden Spoon - Profile</title>
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
            background-color: #fff; /* White background for content area */
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(1, 1, 1, 1.1);
        }

        .profile-heading {
            font-size: 2rem;
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }

        .profile-container {
            max-width: 900px;
            margin: 0 auto;
            padding: 30px;
            border-radius: 8px;
            background-color: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .profile-image {
            text-align: center;
            margin-bottom: 20px;
        }

        .profile-image img {
            width: 250px;
            height: 250px;
            border-radius: 50%;
            border: 5px solid #ddd;
        }

        .user-info p {
            font-size: 16px;
            color: #555;
        }

        .user-info strong {
            color: #333;
        }

        .profile-actions {
            display: flex;
            justify-content: center;
            gap: 20px;
        }

        .btn {
            padding: 12px 24px;
            font-size: 16px;
            color: white;
            text-align: center;
            text-decoration: none;
            border-radius: 4px;
            transition: background-color 0.3s;
        }

        .edit-profile {
            background-color: #007bff;
        }

        .edit-profile:hover {
            background-color: #0056b3;
        }

        .change-password {
            background-color: #28a745;
        }

        .change-password:hover {
            background-color: #218838;
        }

        .logout {
            background-color: #dc3545;
        }

        .logout:hover {
            background-color: #c82333;
        }

        /* Footer Styles */
        footer {
            background-color: #333; /* Dark footer background */
            color: #fff;
            padding: 30px 0;
            text-align: center;
        }

        footer .footer-links {
            margin-bottom: 20px;
        }

        footer .footer-links a {
            color: #fff;
            text-decoration: none;
            margin: 0 15px;
            font-size: 1.1em;
        }

        footer .footer-links a:hover {
            color: #f39c12;
        }

        footer .footer-text {
            font-size: 0.9em;
            color: #bbb;
        }

        footer .footer-text a {
            color: #f39c12;
            text-decoration: none;
        }

        footer .footer-text a:hover {
            text-decoration: underline;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .navbar {
                flex-direction: column;
                align-items: flex-start;
            }

            .container {
                padding: 20px;
            }

            footer {
                padding: 20px 0;
            }
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
            <a href="profile.php" class="active">Profile</a>
            <a href="history.php">History</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>

    <div class="container">
        <div class="profile-container">
            <h1 class="profile-heading">Welcome, <?php echo htmlspecialchars($user['first_name']) . ' ' . htmlspecialchars($user['last_name']); ?>!</h1>

            <!-- Profile Picture -->
            <div class="profile-image">
                <?php
                $profile_pic = $user['profile_picture'] ? 'uploads/' . $user['profile_picture'] : 'images/default-profile.jpg';
                echo "<img src='$profile_pic' alt='Profile Picture'>";
                ?>
            </div>

            <div class="user-info">
                <p><strong>Name:</strong> <?php echo htmlspecialchars($user['first_name']) . ' ' . htmlspecialchars($user['last_name']); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                <p><strong>Phone:</strong> <?php echo htmlspecialchars($user['phone']); ?></p>
                <?php
                $address_sql = "SELECT address FROM address WHERE user_id = '$user_id'";
                $address_result = $conn->query($address_sql);
                $address = $address_result->fetch_assoc();
                ?>
                <p><strong>Delivery Address:</strong> <?php echo htmlspecialchars($address['address'] ?? 'No address set'); ?></p>
                <p><strong>Account Created:</strong> <?php echo htmlspecialchars($user['created_at']); ?></p>
            </div>

            <!-- Profile Actions -->
            <div class="address-section">
           
        </div>
        <div class="profile-actions">
            <a href="edit_profile.php" class="btn edit-profile">Edit Profile</a>
            <a href="forgot-password.php" class="btn change-password">Change Password</a>
            <a href="logout.php" class="btn logout">Logout</a>
        </div>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <div class="footer-links">
            <a href="about.php">About Us</a>
            <a href="contact.php">Contact</a>
            <a href="privacy.php">Privacy Policy</a>
        </div>
        <div class="footer-text">
            &copy; 2025 The Golden Spoon. All rights reserved. <br>
            Follow us on 
            <a href="https://facebook.com" target="_blank">Facebook</a> | 
            <a href="https://twitter.com" target="_blank">Twitter</a> | 
            <a href="https://instagram.com" target="_blank">Instagram</a>
        </div>
    </footer>

</body>
</html>
