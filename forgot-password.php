<?php
include('db.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $secret_key = $_POST['secret_key'];

    // Check if the email and secret key match
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? AND secret_key = ?");
    $stmt->bind_param("ss", $email, $secret_key);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // User found, allow them to reset the password
        header("Location: reset-password.php?email=" . urlencode($email));
        exit();
    } else {
        $error = "Invalid email or secret key.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
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
            background-color: rgba(0, 0, 0, 0.7);
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }
        .container h1 {
            margin-bottom: 30px;
            color: #FFD700;
            font-family: 'Georgia', serif;
        }
        .error {
            color: red;
            font-size: 14px;
            margin-bottom: 20px;
        }
        label {
            display: block;
            font-size: 1.1em;
            color: #fff;
            margin-bottom: 5px;
        }
        input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            border: 1px solid #ddd;
            background-color: #fff;
            color: #333;
            font-size: 1em;
        }
        button {
            width: 100%;
            padding: 12px;
            background-color: #FFD700;
            border: none;
            border-radius: 5px;
            font-size: 1.1em;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        button:hover {
            background-color: #e0a800;
        }
        p {
            margin-top: 20px;
            font-size: 1.1em;
        }
        p a {
            color: #FFD700;
            text-decoration: none;
        }
        p a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Forgot Password</h1>
        <?php if (isset($error)): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>
        
        <form method="POST" action="forgot-password.php">
            <label for="email">Enter your email address:</label>
            <input type="email" name="email" id="email" required><br>

            <label for="secret_key">Enter your secret key:</label>
            <input type="text" name="secret_key" id="secret_key" required><br>

            <button type="submit">Verify Secret Key</button>
        </form>
        
        <p>Remember your password? <a href="index.php">Login</a></p>
    </div>
</body>
</html>
