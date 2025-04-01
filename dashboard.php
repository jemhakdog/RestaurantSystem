<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
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
$sql = "SELECT * FROM users WHERE user_id = '$user_id'";
$result = $conn->query($sql);
$user = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>The Golden Spoon - Dashboard</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #e8e8e8; /* Light gray background */
            color: #444; /* Darker text for better readability */
        }

        .navbar {
            background-color: #333; /* Dark background */
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .navbar h1 {
            color: #f1c40f; /* Golden yellow text */
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
            transform: scale(1.05); /* Slight scale effect on hover */
        }

        .navbar a.active {
            background-color: #f39c12;
        }

        .container {
            max-width: 1200px;
            margin: 50px auto;
            padding: 50px;
            background-color: #fff; /* White background for content area */
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(1, 1, 1, 1.1);
        }

        .welcome {
            font-size: 1.5em;
            text-align: center;
            color: #333;
            margin-bottom: 20px;
            font-weight: 600;
        }

        .carousel {
            display: flex;
            justify-content: center;
            overflow: hidden;
            width: 80%;
            height: 400px;
            margin: 0 auto;
            border-radius: 10px;
            position: relative;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .carousel-images {
            display: flex;
            transition: transform 0.5s ease-in-out;
        }

        .carousel-images img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            flex-shrink: 0;
            border-radius: 10px;
        }

        .carousel-controls {
            position: absolute;
            top: 50%;
            left: 30px;
            transform: translateY(-50%);
            font-size: 1em;
            color: white;
            cursor: pointer;
            background-color: rgba(1, 0, 2, 0.5);
            padding: 20px;
            border-radius: 50%;
        }

        .carousel-controls.right {
            left: auto;
            right: 10px;
        }

        .logout {
            text-align: center;
            margin-top: 30px;
        }

        .logout a {
            font-size: 1.2em;
            color: #f39c12;
            text-decoration: none;
        }

        .logout a:hover {
            text-decoration: underline;
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

            .carousel {
                margin-bottom: 15px;
                width: 100%;
                height: 250px;
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
            <a href="dashboard.php" class="active">Home</a>
            <a href="menu.php">Menu</a>
            <a href="cart.php">Cart</a>
            <a href="reservation.php">Reservation</a>
            <a href="profile.php">Profile</a>
<a href="history.php">History</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>

    <div class="container">
        <div class="welcome">🔥🔥 Most ordered meal🔥🔥 </div>

        <!-- Image Carousel -->
        <div class="carousel">
            <div class="carousel-images">
                <img src="images/1.jpg" alt="Popular Dish 1">
                <img src="images/2.jpg" alt="Popular Dish 2">
                <img src="images/3.jpg" alt="Popular Dish 3">
                <img src="images/4.jpg" alt="Popular Dish 4">
                <img src="images/5.jpg" alt="Popular Dish 5">
                <img src="images/6.jpg" alt="Popular Dish 6">
            </div>
            <div class="carousel-controls left" onclick="moveSlide(-1)">&#10094;</div>
            <div class="carousel-controls right" onclick="moveSlide(1)">&#10095;</div>
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

    <script>
        // JavaScript for Carousel
        let currentIndex = 0;

        function moveSlide(direction) {
            const images = document.querySelectorAll('.carousel-images img');
            const totalImages = images.length;

            currentIndex += direction;

            if (currentIndex < 0) {
                currentIndex = totalImages - 1;
            } else if (currentIndex >= totalImages) {
                currentIndex = 0;
            }

            const newTransform = -currentIndex * 100;
            document.querySelector('.carousel-images').style.transform = `translateX(${newTransform}%)`;
        }

        // Auto Slide functionality
        setInterval(() => {
            moveSlide(1); // Automatically move to the next slide
        }, 4500); // Change slide every 4.5 seconds
    </script>

</body>
</html>
