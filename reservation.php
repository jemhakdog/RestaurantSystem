<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Include database connection and mailer
include('db.php');
include('includes/Mailer.php');

// Handle reservation form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_reservation'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $reservation_date = $_POST['reservation_date'];
    $table_number = $_POST['table_number'];

    // Insert reservation into the database
    $sql = "INSERT INTO reservations (user_id, name, email, phone, reservation_date, table_number, status) 
            VALUES (?, ?, ?, ?, ?, ?, 'active')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('issssi', $user_id, $name, $email, $phone, $reservation_date, $table_number);
    if ($stmt->execute()) {
        // Send confirmation email
        $mailer = new Mailer();
        if ($mailer->sendReservationConfirmation($name, $email, $reservation_date, $table_number, $phone)) {
            $_SESSION['success_message'] = "Your reservation for Table $table_number on $reservation_date has been successfully made! A confirmation email has been sent to your email address.";
        } else {
            $_SESSION['success_message'] = "Your reservation for Table $table_number on $reservation_date has been successfully made! However, we couldn't send the confirmation email.";
        }
    }
    header('Location: reservation.php');
    exit();
}

// Handle remove reservation request
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['remove_reservation'])) {
    $remove_id = $_POST['remove_id'];

    // Update reservation status to 'removed'
    $remove_sql = "UPDATE reservations SET status = 'removed' WHERE id = ?";
    $remove_stmt = $conn->prepare($remove_sql);
    $remove_stmt->bind_param('i', $remove_id);

    if ($remove_stmt->execute()) {
        $_SESSION['success_message'] = "Your reservation has been successfully removed!";
    } else {
        $_SESSION['error_message'] = "An error occurred while removing the reservation.";
    }

    // Redirect back to the reservation page
    header('Location: reservation.php');
    exit();
}

// Fetch available tables from the database
$table_sql = "SELECT table_number FROM tables WHERE available = 1";
$table_result = $conn->query($table_sql);

// Fetch user reservations with status 'active'
$reservations_sql = "SELECT * FROM reservations WHERE user_id = ? AND status = 'active'";
$reservations_stmt = $conn->prepare($reservations_sql);
$reservations_stmt->bind_param('i', $user_id);
$reservations_stmt->execute();
$reservations_result = $reservations_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Make a Reservation - The Golden Spoon</title>
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
            max-width: 900px;
            margin: 50px auto;
            padding: 50px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(1, 1, 1, 1.1);
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            font-size: 1.1em;
            color: #333;
            margin-bottom: 5px;
            display: block;
        }

        .form-group input, .form-group select {
            width: 100%;
            padding: 10px;
            font-size: 1em;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .form-group input[type="datetime-local"] {
            font-size: 1em;
        }

        .form-group button {
            background-color: #f39c12;
            color: white;
            padding: 12px 25px;
            font-size: 1.1em;
            border: none;
            cursor: pointer;
            border-radius: 5px;
        }

        .form-group button:hover {
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

        .success-message {
            background-color: #28a745;
            color: white;
            padding: 15px;
            border-radius: 5px;
            text-align: center;
            margin-bottom: 20px;
            font-size: 1.2em;
        }

        .reservation-item {
            padding: 20px;
            background-color: #2c3e50;
            color: white;
            margin-bottom: 15px;
            border-radius: 10px;
        }

        .reservation-item p {
            font-size: 1.1em;
            margin: 10px 0;
        }

        .pay-now {
            display: inline-block;
            padding: 12px 25px;
            background-color: #f39c12;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 10px;
            text-align: center;
        }

        .pay-now:hover {
            background-color: #e67e22;
        }

        .remove-btn {
            background-color: #e74c3c;
            color: white;
            padding: 8px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 10px;
        }

        .remove-btn:hover {
            background-color: #c0392b;
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
            <a href="reservation.php" class="active">Reservation</a>
            <a href="profile.php">Profile</a>
<a href="history.php">History</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>

    <!-- Reservation Form -->
    <div class="container">
        <h2>Make a Reservation</h2>

        <?php
        if (isset($_SESSION['success_message'])) {
            echo '<div class="success-message">' . $_SESSION['success_message'] . '</div>';
            unset($_SESSION['success_message']);
        }
        ?>

        <form action="reservation.php" method="POST">
            <div class="form-group">
                <label for="name">Your Name:</label>
                <input type="text" id="name" name="name" required>
            </div>

            <div class="form-group">
                <label for="email">Your Email:</label>
                <input type="email" id="email" name="email" required>
            </div>

            <div class="form-group">
                <label for="phone">Your Phone Number:</label>
                <input type="text" id="phone" name="phone" required>
            </div>

            <div class="form-group">
                <label for="reservation_date">Reservation Date & Time:</label>
                <input type="datetime-local" id="reservation_date" name="reservation_date" required>
            </div>

            <div class="form-group">
                <label for="table_number">Select Table Number:</label>
                <select name="table_number" id="table_number" required>
                    <option value="">Select a Table</option>
                    <?php while ($table_row = $table_result->fetch_assoc()) { ?>
                        <option value="<?php echo $table_row['table_number']; ?>">
                            Table <?php echo $table_row['table_number']; ?>
                        </option>
                    <?php } ?>
                </select>
            </div>

            <div class="form-group">
                <button type="submit" name="submit_reservation">Make Reservation</button>
            </div>
        </form>
    </div>

    <!-- Reservation Details Section -->
    <div class="container">
        <h2>Your Reservations</h2>
        <?php while ($reservation = $reservations_result->fetch_assoc()) { ?>
            <div class="reservation-item">
                <p><strong>Table:</strong> <?php echo $reservation['table_number']; ?></p>
                <p><strong>Date:</strong> <?php echo $reservation['reservation_date']; ?></p>

                <form method="POST" action="reservation.php">
                    <input type="hidden" name="remove_id" value="<?php echo $reservation['id']; ?>">
                    <button type="submit" name="remove_reservation" class="remove-btn">Remove Reservation</button>
                </form>

                <?php if ($reservation['payment_status'] == 'Paid') { ?>
                    <p><strong>Status:</strong> Paid</p>
                    <button disabled class="pay-now">Already Paid</button>
                <?php } else { ?>
                    <a href="pay_now.php?reservation_id=<?php echo $reservation['id']; ?>" class="pay-now">Pay Now</a>
                <?php } ?>
            </div>
        <?php } ?>
    </div>

</body>
</html>

<?php
// Close database connection
$conn->close();
?>
