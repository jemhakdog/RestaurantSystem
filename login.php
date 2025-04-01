
<?php
session_start();

// After successful login
if (isset($_GET['redirect'])) {
    header('Location: ' . urldecode($_GET['redirect']));
} else {
    // Default redirect based on role
    if ($_SESSION['role'] === 'admin') {
        header('Location: admin/dashboard.php');
    } else {
        header('Location: dashboard.php');
    }
}
exit();
?>