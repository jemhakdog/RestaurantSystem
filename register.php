<?php
include('db.php');

// Check if user is already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');  // Redirect to the dashboard if the user is already logged in
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Collect form data and sanitize inputs
    $first_name = htmlspecialchars(trim($_POST['first_name']));
    $last_name = htmlspecialchars(trim($_POST['last_name']));
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $phone = htmlspecialchars(trim($_POST['phone']));
    $secret_key = htmlspecialchars(trim($_POST['secret_key']));  // Secret key input
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);  // Hash the password
    
    // Validate the inputs
    $error = null;
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } else {
        // Use prepared statements to avoid SQL injection
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $error = "Email is already registered.";
        } else {
            // Insert into the database, including the secret_key
            $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, email, phone, password, secret_key) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssss", $first_name, $last_name, $email, $phone, $password, $secret_key);

            if ($stmt->execute()) {
                $success = "Registration successful! ";
            } else {
                $error = "Error: " . $stmt->error;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>The Golden Spoon - Register</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(to right, #FF7E5F, #feb47b); /* New background */
            background-size: cover;
            background-position: center;
            height: 150vh;
            display: flex;
            justify-content: center;
            align-items: center;
            color: #fff;
        }
        .register-container {
            background: rgba(0, 0, 0, 0.7);
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 8px 16px rgba(1, 1, 1, 1.3);
            width: 100%;
            max-width: 500px;
            text-align: center;
            transition: all 0.3s ease-in-out;
        }
        .register-container:hover {
            transform: scale(1.05);
        }
        .register-container h1 {
            margin-bottom: 40px;
            font-size: 2.8em;
            color: #FFD700;  /* Golden color */
            font-family: 'Georgia', serif;
        }
        label {
            display: block;
            margin: 20px 0 10px;
            font-weight: bold;
            font-size: 1.2em;
            color: #fff;
        }
        input {
            width: 100%;
            padding: 14px;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-bottom: 15px;
            background-color: #fff;
            font-size: 1.1em;
            color: #333;
            transition: border 0.3s ease;
        }
        input:focus {
            border-color: #FFD700;
            outline: none;
        }
        button {
            width: 100%;
            padding: 12px;
            background-color: #FFD700;
            border: none;
            border-radius: 10px;
            font-size: 1.2em;
            color: white;
            cursor: pointer;
            transition: all 0.3s;
        }
        button:hover {
            background-color: #e0a800;
        }
        .error {
            color: red;
            font-size: 16px;
            text-align: center;
            margin-bottom: 10px;
        }
        .success {
            color: green;
            font-size: 16px;
            text-align: center;
            margin-bottom: 10px;
        }
        .loading-spinner {
            display: none;
            border: 4px solid #f3f3f3;
            border-top: 4px solid #3498db;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            animation: spin 2s linear infinite;
            margin: 0 auto;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        p {
            text-align: center;
            font-size: 1.1em;
        }
        p a {
            color: #FFD700;
            text-decoration: none;
            font-weight: bold;
        }
        p a:hover {
            text-decoration: underline;
        }
        @media (max-width: 500px) {
            .register-container {
                padding: 20px;
                max-width: 300px;
            }
        }
    </style>
</head>
<body>

    <div class="register-container">
        <h1>The Golden Spoon</h1>

        <?php if (isset($error)): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>

        <?php if (isset($success)): ?>
            <p class="success"><?php echo $success; ?></p>
        <?php endif; ?>

        <form method="POST" action="register.php" onsubmit="showLoadingSpinner()">
            <label for="first_name">First Name:</label>
            <input type="text" name="first_name" id="first_name" required><br>

            <label for="last_name">Last Name:</label>
            <input type="text" name="last_name" id="last_name" required><br>

            <label for="email">Email:</label>
            <input type="email" name="email" id="email" required><br>

            <label for="phone">Phone:</label>
            <input type="text" name="phone" id="phone" required><br>

            <label for="secret_key">Enter your secret key:</label>
            <input type="text" name="secret_key" id="secret_key" required><br>

            <label for="password">Password:</label>
            <input type="password" name="password" id="password" required><br>

            <button type="submit">Register</button>
        </form>

        <div class="loading-spinner" id="loadingSpinner"></div>

        <p>Already have an account? <a href="index.php">Login</a></p>
    </div>

    <script>
        function showLoadingSpinner() {
            document.getElementById('loadingSpinner').style.display = 'block';
        }
    </script>

</body>
</html>
