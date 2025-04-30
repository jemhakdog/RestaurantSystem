<?php
session_start();
include('../db.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $price = floatval($_POST['price']);
    $quantity = intval($_POST['quantity']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    
    // Handle file upload
    $image = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../images/';
        $uploadFile = $uploadDir . basename($_FILES['image']['name']);
        
        // Check if file is an actual image
        $check = getimagesize($_FILES['image']['tmp_name']);
        if ($check !== false) {
            if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile)) {
                $image = $_FILES['image']['name'];
            }
        }
    }
    $sql = "INSERT INTO menu (name, description, price, quantity, image, category) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'ssdiss', $name, $description, $price, $quantity, $image, $category);
    $result = mysqli_stmt_execute($stmt);
    
    if ($result) {
        header('Location: menu.php?success=1');
    } else {
        header('Location: menu.php?error=1');
    }
    exit();
}

header('Location: menu.php');
exit();
?>