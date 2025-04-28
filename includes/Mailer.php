<?php
require_once __DIR__ . '/../vendor/autoload.php';

// Load environment variables from .env file
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

// Access environment variables
$email = $_ENV['EMAIL'];
$password = $_ENV['PASSWORD'];

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Mailer {
    private $mail;
    private $email;
    private $password;

    public function __construct() {
        global $email, $password;
        $this->email = $email;
        $this->password = $password;
        $this->mail = new PHPMailer(true);
        $this->mail->isSMTP();
        $this->mail->Host = 'smtp.gmail.com'; // Replace with your SMTP host
        $this->mail->SMTPAuth = true;
        $this->mail->Username = $this->email; // Replace with your email
        $this->mail->Password = $this->password; // Replace with your app password
        $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $this->mail->Port = 587;
        $this->mail->setFrom($this->email, 'The Golden Spoon'); // Replace with your email and restaurant name
    }

    public function sendReservationConfirmation($name, $email, $date, $table, $phone) {
        try {
            $this->mail->addAddress($email, $name);
            $this->mail->isHTML(true);
            $this->mail->Subject = 'Reservation Confirmation - The Golden Spoon';
            
            $body = "<h2>Reservation Confirmation</h2>";
            $body .= "<p>Dear {$name},</p>";
            $body .= "<p>Thank you for choosing The Golden Spoon. Your reservation has been confirmed.</p>";
            $body .= "<p><strong>Details:</strong></p>";
            $body .= "<ul>";
            $body .= "<li>Date and Time: {$date}</li>";
            $body .= "<li>Table Number: {$table}</li>";
            $body .= "<li>Contact Number: {$phone}</li>";
            $body .= "</ul>";
            $body .= "<p>If you need to make any changes to your reservation, please contact us.</p>";
            $body .= "<p>We look forward to serving you!</p>";
            
            $this->mail->Body = $body;
            $this->mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Email sending failed: {$this->mail->ErrorInfo}");
            return false;
        }
    }

    public function sendOrderConfirmation($name, $email, $orderId, $items, $total, $serviceType, $address = null) {
        try {
            $this->mail->addAddress($email, $name);
            $this->mail->isHTML(true);
            $this->mail->Subject = 'Order Confirmation - The Golden Spoon';
            
            $body = "<h2>Order Confirmation</h2>";
            $body .= "<p>Dear {$name},</p>";
            $body .= "<p>Thank you for your order. Here are your order details:</p>";
            $body .= "<p><strong>Order ID:</strong> #{$orderId}</p>";
            $body .= "<p><strong>Service Type:</strong> " . ucfirst($serviceType) . "</p>";
            
            if ($address) {
                $body .= "<p><strong>Delivery Address:</strong> {$address}</p>";
            }
            
            $body .= "<h3>Order Items:</h3>";
            $body .= "<ul>";
            foreach ($items as $item) {
                $body .= "<li>{$item['name']} x {$item['quantity']} - ₱{$item['price']}</li>";
            }
            $body .= "</ul>";
            $body .= "<p><strong>Total Amount:</strong> ₱{$total}</p>";
            $body .= "<p>Thank you for choosing The Golden Spoon!</p>";
            
            $this->mail->Body = $body;
            $this->mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Email sending failed: {$this->mail->ErrorInfo}");
            return false;
        }
    }

    public function sendStatusUpdate($name, $email, $orderId, $status, $items, $total) {
        try {
            $this->mail->clearAddresses(); // Clear previous recipients
            $this->mail->addAddress($email, $name);
            $this->mail->isHTML(true);
            $this->mail->Subject = 'Order Status Update - The Golden Spoon';
            
            $body = "<h2>Order Status Update</h2>";
            $body .= "<p>Dear {$name},</p>";
            $body .= "<p>Your order status has been updated:</p>";
            $body .= "<p><strong>Order ID:</strong> #{$orderId}</p>";
            $body .= "<p><strong>New Status:</strong> " . ucfirst($status) . "</p>";
            
            $body .= "<h3>Order Items:</h3>";
            $body .= "<ul>";
            foreach ($items as $item) {
                $body .= "<li>{$item['name']} x {$item['quantity']} - ₱{$item['price']}</li>";
            }
            $body .= "</ul>";
            $body .= "<p><strong>Total Amount:</strong> ₱{$total}</p>";
            $body .= "<p>Thank you for choosing The Golden Spoon!</p>";
            
            $this->mail->Body = $body;
            $this->mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Status update email failed: {$this->mail->ErrorInfo}");
            return false;
        }
    }
}