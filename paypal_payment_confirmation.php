<?php
session_start();

// Include database connection
include('db.php');

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve the reservation ID and PayPal transaction ID
    $reservation_id = $_POST['reservation_id'];
    $paypal_transaction_id = $_POST['paypal_transaction_id'];

    // Basic validation
    if (empty($paypal_transaction_id)) {
        echo "Transaction ID is required.";
        exit();
    }

    // Check if the reservation exists
    $reservation_sql = "SELECT * FROM reservations WHERE id = ?";
    $reservation_stmt = $conn->prepare($reservation_sql);
    $reservation_stmt->bind_param('i', $reservation_id);
    $reservation_stmt->execute();
    $reservation = $reservation_stmt->get_result()->fetch_assoc();

    if (!$reservation) {
        echo "Reservation not found.";
        exit();
    }

    // Update the reservation's payment status to 'Paid' and store the PayPal transaction ID
    $update_sql = "UPDATE reservations SET payment_status = 'Paid', paypal_transaction_id = ? WHERE id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param('si', $paypal_transaction_id, $reservation_id);

    if ($update_stmt->execute()) {
        // Payment successfully confirmed
        header("Location: thank_you.php?reservation_id=" . $reservation_id);
        exit();
    } else {
        echo "Error updating reservation. Please try again.";
    }
} else {
    echo "Invalid request.";
}
?>
