<?php
// Database connection settings
$servername = "localhost";
$username = "root";  // Adjust as needed (default is 'root' for XAMPP)
$password = "";      // Adjust as needed (empty by default for XAMPP)
$dbname = "restaurant_db";  // Name of your database
$charset = 'utf8mb4';        // Charset

// Create MySQLi connection (optional if you need mysqli also)
$conn = new mysqli($servername, $username, $password, $dbname);

// Check MySQLi connection
if ($conn->connect_error) {
    die("MySQLi connection failed: " . $conn->connect_error);
}

// DSN (Data Source Name) for PDO connection
$dsn = "mysql:host=$servername;dbname=$dbname;charset=$charset";

// PDO Options
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    // Create PDO instance
    $pdo = new PDO($dsn, $username, $password, $options);
    // Optionally check if the PDO connection is working
    // echo "PDO connection successful!";
} catch (PDOException $e) {
    // Catch errors and display message
    echo "PDO connection failed: " . $e->getMessage();
    exit();
}
?>
