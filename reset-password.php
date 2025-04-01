<?php
include('db.php');

// Get the email from the query string
if (isset($_GET['email'])) {
    $email = $_GET['email'];
} else {
    // If no email, redirect to forgot password page
    header("Location: forgot-password.php");
    exit();
}

// Handle the password reset
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_password = $_POST['new_password'];

    // Hash the new password
    $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);

    // Update the password in the database
    $stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
    $stmt->bind_param("ss", $hashed_password, $email);
    $stmt->execute();

    $success = "Your password has been successfully reset.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Your Password</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(to right, #FF7E5F, #feb47b);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            color: #fff;
        }
        .container {
            background: rgba(0, 0, 0, 0.7);
            padding: 70px;
            border-radius: 10px;
            box-shadow: 0 8px 16px rgba(1, 1, 1, 1.2);
            width: 100%;
            max-width: 450px;
            text-align: center;
        }
        .container h1 {
            margin-bottom: 30px;
            font-size: 2.8em;
            color: #FFD700;  /* Golden color */
            font-family: 'Georgia', serif;
        }
        .success {
            color: green;
            font-size: 16px;
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin: 20px 0 10px;
            font-weight: bold;
            font-size: 1.5em;
            color: #fff;
        }
        input {
            width: 100%;
            padding: 14px;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-bottom: 30px;
            background-color: #fff;
            font-size: 1em;
            color: #333;
        }
        input:focus {
            border-color: #FFD700;
            outline: none;
        }
        button {
            width: 100%;
            padding: 12px;
            background-color: #FFD700;  /* Golden color */
            border: none;
            border-radius: 5px;
            font-size: 1.2em;
            color: white;
            cursor: pointer;
            transition: all 0.3s;
        }
        button:hover {
            background-color: #e0a800;
        }
        .back-to-login {
            margin-top: 20px;
            padding: 5px;
            background-color: #444;
            color: #fff;
            border-radius: 5px;
            text-decoration: none;
            font-size: 1.1em;
        }
        .back-to-login:hover {
            background-color: #333;
        }
        .error {
            color: red;
            font-size: 16px;
            text-align: center;
            margin-bottom: 10px;
        }
        @media (max-width: 500px) {
            .container {
                padding: 20px;
                max-width: 300px;
            }
        }
    </style>
</head>
<body>

    <div class="container">
        <h1>Reset Your Password</h1>
        
        <?php if (isset($success)): ?>
            <p class="success"><?php echo $success; ?></p>
            <a href="index.php" class="back-to-login">Back to Login</a>
        <?php else: ?>
            <form method="POST" action="reset-password.php?email=<?php echo urlencode($email); ?>">
                <label for="new_password">New Password:</label>
                <input type="password" name="new_password" id="new_password" required><br>

                <button type="submit">Reset Password</button>
            </form>
        <?php endif; ?>
    </div>

</body>
</html>
