<?php
session_start();

// Include database connection
include('db.php');

// Fetch reservation details based on reservation ID
if (isset($_GET['reservation_id'])) {
    $reservation_id = $_GET['reservation_id'];
    $reservation_sql = "SELECT * FROM reservations WHERE id = ?";
    $reservation_stmt = $conn->prepare($reservation_sql);
    $reservation_stmt->bind_param('i', $reservation_id);
    $reservation_stmt->execute();
    $reservation = $reservation_stmt->get_result()->fetch_assoc();
    
    if (!$reservation) {
        // Reservation not found
        echo "Reservation not found.";
        exit();
    }
} else {
    // No reservation ID passed
    echo "No reservation ID provided.";
    exit();
}

// Set the payment amount based on the reservation or a fixed value
$payment_amount = 50.00; // Example amount
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pay for Reservation - The Golden Spoon</title>
    <script src="https://www.paypal.com/sdk/js?client-id=YOUR_PAYPAL_CLIENT_ID&currency=USD"></script>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f0f0f5;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
            margin: 50px auto;
            padding: 30px;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(1, 1, 1, 0.1);
            transition: all 0.3s ease;
        }

        h2 {
            text-align: center;
            color: #f39c12;
            margin-bottom: 20px;
        }

        .reservation-details {
            margin-bottom: 30px;
            font-size: 1.1em;
            color: #555;
        }

        .reservation-details p {
            margin: 5px 0;
        }

        .payment-options {
            margin-top: 20px;
        }

        .payment-option {
            margin: 15px 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .payment-option label {
            font-size: 1.1em;
            color: #333;
        }

        input[type="radio"] {
            width: 20px;
            height: 20px;
        }

        #paypal-details, #gcash-details, #bank-transfer-details {
            display: none;
            margin-top: 20px;
        }

        #paypal-details input,
        #gcash-details input,
        #bank-transfer-details input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            border: 1px solid #ddd;
            transition: all 0.3s ease;
        }

        #paypal-details input:focus,
        #gcash-details input:focus,
        #bank-transfer-details input:focus {
            border-color: #f39c12;
        }

        button {
            padding: 12px 25px;
            background-color: #f39c12;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #e67e22;
        }

        .footer {
            background-color: #333;
            color: #fff;
            text-align: center;
            padding: 20px;
            margin-top: 30px;
        }

        .footer a {
            color: #f39c12;
            text-decoration: none;
        }

        @media (max-width: 768px) {
            .container {
                padding: 15px;
                margin: 20px;
            }

            h2 {
                font-size: 1.5em;
            }

            .reservation-details {
                font-size: 1em;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Pay for Your Reservation</h2>

    <div class="reservation-details">
        <p><strong>Table:</strong> <?php echo $reservation['table_number']; ?></p>
        <p><strong>Date:</strong> <?php echo $reservation['reservation_date']; ?></p>
        <p><strong>Name:</strong> <?php echo $reservation['name']; ?></p>
        <p><strong>Email:</strong> <?php echo $reservation['email']; ?></p>
        <p><strong>Phone:</strong> <?php echo $reservation['phone']; ?></p>
    </div>

    <!-- Payment Options -->
    <div class="payment-options">
        <p><strong>Select a Payment Method:</strong></p>

        <div class="payment-option">
            <input type="radio" id="paypal" name="payment_method" value="paypal">
            <label for="paypal">Pay with PayPal</label>
        </div>

        <div class="payment-option">
            <input type="radio" id="gcash" name="payment_method" value="gcash">
            <label for="gcash">Pay with GCash</label>
        </div>

        <div class="payment-option">
            <input type="radio" id="bank_transfer" name="payment_method" value="bank_transfer">
            <label for="bank_transfer">Bank Transfer</label>
        </div>
    </div>

    <!-- PayPal Details -->
    <div id="paypal-details">
        <p>To confirm your PayPal payment, please visit PayPal and enter the transaction ID below:</p>
        <form action="paypal_payment_confirmation.php" method="POST">
            <label for="paypal_transaction_id">Enter PayPal Transaction ID:</label>
            <input type="text" name="paypal_transaction_id" required>
            <input type="hidden" name="reservation_id" value="<?php echo $reservation['id']; ?>">
            <button type="submit">Confirm PayPal Payment</button>
        </form>
    </div>

    <!-- GCash Details -->
    <div id="gcash-details">
        <p>This Payment Method is Unavailable! Try again later!</p>
        
        </form>
    </div>

    <!-- Bank Transfer Details -->
    <div id="bank-transfer-details">
        <p>This Payment Method is Unavailable! Try again later!</p>

    </div>

    
</div>

<!-- Footer -->


<script>
    // Handle payment option selection
    document.querySelectorAll('input[name="payment_method"]').forEach(function(radio) {
        radio.addEventListener('change', function() {
            // Hide all payment options first
            document.getElementById('paypal-details').style.display = 'none';
            document.getElementById('gcash-details').style.display = 'none';
            document.getElementById('bank-transfer-details').style.display = 'none';

            // Show selected payment option
            if (this.value === 'paypal') {
                document.getElementById('paypal-details').style.display = 'block';
            } else if (this.value === 'gcash') {
                document.getElementById('gcash-details').style.display = 'block';
            } else if (this.value === 'bank_transfer') {
                document.getElementById('bank-transfer-details').style.display = 'block';
            }
        });
    });
</script>

</body>
</html>
