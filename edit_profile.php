<?php
session_start();
include('db.php');

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Get user details from the database
if (isset($pdo)) {
    try {
        // Get current user details
        $sql = "SELECT * FROM users WHERE user_id = :user_id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        $user = $stmt->fetch();

        // If form is submitted
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $new_email = $_POST['email'];
            $new_phone = $_POST['phone'];
            $new_address = $_POST['address'];
            $profile_picture = $_FILES['profile_picture'];

            // Handle profile picture upload
            if ($profile_picture['name']) {
                $target_dir = "uploads/";
                $target_file = $target_dir . basename($profile_picture["name"]);
                move_uploaded_file($profile_picture["tmp_name"], $target_file);
                $profile_picture_name = basename($profile_picture["name"]);
            } else {
                $profile_picture_name = $user['profile_picture']; // Keep the old profile picture
            }

            // Update the address in the address table
            $check_address_sql = "SELECT * FROM address WHERE user_id = :user_id";
            $check_stmt = $pdo->prepare($check_address_sql);
            $check_stmt->bindParam(':user_id', $user_id);
            $check_stmt->execute();

            if ($check_stmt->rowCount() > 0) {
                $address_sql = "UPDATE address SET address = :address WHERE user_id = :user_id";
            } else {
                $address_sql = "INSERT INTO address (user_id, address) VALUES (:user_id, :address)";
            }
            $address_stmt = $pdo->prepare($address_sql);
            $address_stmt->bindParam(':address', $new_address);
            $address_stmt->bindParam(':user_id', $user_id);
            $address_stmt->execute();

            // Update the user data in the database
            $update_sql = "UPDATE users SET email = :email, phone = :phone, profile_picture = :profile_picture WHERE user_id = :user_id";
            $stmt = $pdo->prepare($update_sql);
            $stmt->bindParam(':email', $new_email);
            $stmt->bindParam(':phone', $new_phone);
            $stmt->bindParam(':profile_picture', $profile_picture_name);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->execute();

            echo "Profile updated successfully!";
            header('Location: profile.php'); // Redirect to profile page after update
        }
    } catch (PDOException $e) {
        echo "Error fetching or updating data: " . $e->getMessage();
    }
}

?>

<h1>Edit Profile</h1>
<form method="POST" enctype="multipart/form-data" class="edit-form">
    <label for="email">Email:</label>
    <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required><br>

    <label for="phone">Phone:</label>
    <input type="text" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>" required><br>

    <label for="address">Delivery Address:</label>
    <textarea name="address" rows="3" style="width: 100%; margin-bottom: 20px; padding: 8px;" placeholder="Enter your delivery address"><?php
        $address_sql = "SELECT address FROM address WHERE user_id = :user_id";
        $address_stmt = $pdo->prepare($address_sql);
        $address_stmt->bindParam(':user_id', $user_id);
        $address_stmt->execute();
        $address_result = $address_stmt->fetch();
        echo htmlspecialchars($address_result['address'] ?? '');
    ?></textarea><br>

    <label for="profile_picture">Profile Picture:</label>
    <input type="file" name="profile_picture" accept="image/*"><br><br>

    <button type="submit">Save Changes</button>
</form>

<style>
    h1 {
        text-align: center;
        font-size: 2rem;
        margin-bottom: 20px;
        color: #333;
    }

    .edit-form {
        max-width: 600px;
        margin: 0 auto;
        background-color: #fff;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .edit-form label {
        font-weight: bold;
        margin-bottom: 10px;
        display: block;
    }

    .edit-form input {
        width: 100%;
        padding: 8px;
        margin-bottom: 20px;
        border: 1px solid #ddd;
        border-radius: 4px;
    }

    .edit-form button {
        padding: 12px 24px;
        background-color: #007bff;
        color: white;
        font-size: 16px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }

    .edit-form button:hover {
        background-color: #0056b3;
    }
</style>
