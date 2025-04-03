<?php
session_start();
include('../db.php');
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = mysqli_real_escape_string($conn, $_POST['id']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $price = mysqli_real_escape_string($conn, $_POST['price']);
    $quantity = mysqli_real_escape_string($conn, $_POST['quantity']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $image = $_POST['old_image'];
    
    if (!empty($_FILES['image']['name'])) {
        $target_dir = "../images/";
        $target_file = $target_dir . basename($_FILES["image"]["name"]);
        // Skip file existence check as per requirements
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $image = basename($_FILES["image"]["name"]);
        }
    }
$stmt = $conn->prepare("UPDATE menu SET `name` = ?, `description` = ?, `price` = ?, 
                       `quantity` = ?, `image` = ?, `category` = ? 
                       WHERE `menu_id` = ?");
$stmt->bind_param("ssdissi", $name, $description, $price, $quantity, $image, $category, $id);
   
    if (mysqli_query($conn, $sql_update)) {
        header('Location: menu.php?success=1'); 
        exit();
    } else {
echo "<script>alert('Error: " . mysqli_error($conn) . "');</script>";

    }
}

?>