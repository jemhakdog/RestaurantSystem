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
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&family=Montserrat:wght@700&display=swap" rel="stylesheet">
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
        .welcome {
            font-size: 1.8rem;
            text-align: center;
            color: var(--dark);
            margin-bottom: 2.5rem;
            font-weight: 700;
            position: relative;
            display: block;
            width: 100%;
            padding: 0 1rem;
        }

        .container {
            max-width: 1200px;
            margin: 3rem auto;
            padding: 2.5rem;
            background-color: var(--light);
            border-radius: 16px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.05);
            position: relative;
            overflow: hidden;
        }

        .container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 8px;
            background: linear-gradient(90deg, var(--primary), var(--secondary));
        }

        .welcome {
            font-size: 1.8rem;
            text-align: center;
            color: var(--dark);
            margin-bottom: 2.5rem;
            font-weight: 700;
            position: relative;
            display: inline-block;
            padding: 0 1rem;
        }

        .welcome::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 60px;
            height: 4px;
            background: var(--primary);
            border-radius: 2px;
        }

        .carousel {
            display: flex;
            justify-content: center;
            overflow: hidden;
            width: 90%;
            height: 500px;
            margin: 0 auto;
            border-radius: 16px;
            position: relative;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
        }

        .carousel-images {
            display: flex;
            transition: transform 0.5s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        }

        .carousel-images img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            flex-shrink: 0;
            border-radius: 16px;
        }

        .carousel-controls {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            width: 50px;
            height: 50px;
            background-color: rgba(255, 255, 255, 0.8);
            color: var(--dark);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 1.5rem;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            z-index: 10;
        }

        .carousel-controls:hover {
            background-color: var(--primary);
            color: white;
            transform: translateY(-50%) scale(1.1);
        }

        .carousel-controls.left {
            left: 20px;
        }

        .carousel-controls.right {
            right: 20px;
        }

        footer {
            background-color: var(--dark);
            color: var(--light);
            padding: 3rem 0;
            text-align: center;
            margin-top: 4rem;
        }

        .footer-links {
            display: flex;
            justify-content: center;
            gap: 2rem;
            margin-bottom: 1.5rem;
        }

        .footer-links a {
            color: var(--light);
            text-decoration: none;
            font-size: 1rem;
            transition: color 0.3s ease;
            position: relative;
        }

        .footer-links a::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 0;
            height: 2px;
            background-color: var(--primary);
            transition: width 0.3s ease;
        }

        .footer-links a:hover {
            color: var(--primary);
        }

        .footer-links a:hover::after {
            width: 100%;
        }

        .footer-text {
            font-size: 0.9rem;
            color: rgba(255, 255, 255, 0.7);
            margin-top: 1rem;
        }

        .footer-text a {
            color: var(--primary);
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .footer-text a:hover {
            color: var(--secondary);
            text-decoration: underline;
        }

        @media (max-width: 768px) {
            .navbar {
                flex-direction: column;
                padding: 1rem;
            }

            .navbar h1 {
                margin-bottom: 1rem;
            }

            .carousel {
                width: 100%;
                height: 300px;
            }

            .container {
                padding: 1.5rem;
                margin: 1.5rem;
            }

            .footer-links {
                flex-direction: column;
                gap: 1rem;
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
