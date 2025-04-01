<?php
session_start();
include('../db.php');
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = mysqli_real_escape_string($conn, $_POST['id']);
    
   $delete_query = "DELETE FROM menu WHERE menu_id = '$id'";
    if (mysqli_query($conn, $delete_query)) {
        header('Location: menu.php?success=1');
        exit();
    } else {
        echo "<script>alert('Error: ". mysqli_error($conn). "');</script>";
    }
   
}

?>