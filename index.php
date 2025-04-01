<?php
include('db.php');
session_start();

// Check if user is already logged in
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] === 'admin') {
        header('Location: admin/dashboard.php');
    } else {
        header('Location: dashboard.php');
    }
    exit();
}

// Handle login attempts limitation
$max_attempts = 5;
$lockout_time = 30; // Lockout time in minutes

if (isset($_SESSION['login_attempts']) && $_SESSION['login_attempts'] >= $max_attempts) {
    // Check if the lockout time has expired
    $time_since_last_attempt = time() - $_SESSION['last_attempt_time'];
    if ($time_since_last_attempt >= $lockout_time * 60) {
        unset($_SESSION['login_attempts']);  // Reset login attempts after lockout time
        unset($_SESSION['last_attempt_time']);
    } else {
        $error = "Too many failed attempts. Please try again in " . ceil(($lockout_time * 60 - $time_since_last_attempt) / 60) . " minutes.";
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Collect form data
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } else {
        // Use prepared statement to prevent SQL injection
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            // Verify the password
            if (password_verify($password, $row['password'])) {
                // Reset login attempts on successful login
                unset($_SESSION['login_attempts']);
                unset($_SESSION['last_attempt_time']);

                // Start the session and set user session variables
                $_SESSION['user_id'] = $row['user_id'];
                $_SESSION['role'] = $row['role'];

                // Redirect based on role
                if ($row['role'] === 'admin') {
                    header('Location: admin/dashboard.php');
                } else {
                    header('Location: dashboard.php');
                }
                exit();
            } else {
                // Increment failed login attempts
                $_SESSION['login_attempts'] = isset($_SESSION['login_attempts']) ? $_SESSION['login_attempts'] + 1 : 1;
                $_SESSION['last_attempt_time'] = time();
                $error = "Incorrect password.";
            }
        } else {
            $error = "No user found with that email.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>The Golden Spoon - Login</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(to right, #FF7E5F, #feb47b);
            background-size: cover;
            background-position: center;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            color: #fff;
        }
        .login-container {
            background: rgba(0, 0, 0, 0.7);
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 450px;
            text-align: center;
            transition: all 0.3s ease-in-out;
        }
        .login-container:hover {
            transform: scale(1.05);
        }
        .login-container h1 {
            margin-bottom: 30px;
            font-size: 2.8em;
            color: #FFD700;
            font-family: 'Georgia', serif;
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
            margin-bottom: 20px;
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
            border-radius: 5px;
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
            .login-container {
                padding: 20px;
                max-width: 300px;
            }
        }
    </style>
</head>
<body>

    <div class="login-container">
        <h1>The Golden Spoon</h1>

        <?php if (isset($error)): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>

        <form method="POST" action="index.php" onsubmit="showLoadingSpinner()">
            <label for="email">Email:</label>
            <input type="email" name="email" id="email" required value="<?php echo isset($email) ? $email : ''; ?>"><br>

            <label for="password">Password:</label>
            <input type="password" name="password" id="password" required><br>

            <button type="submit">Login</button>
        </form>

        <div class="loading-spinner" id="loadingSpinner"></div>

        <p>Don't have an account? <a href="register.php">Register</a></p>
        <p><a href="forgot-password.php">Forgot Password</a></p>
    </div>

    <script>
        function showLoadingSpinner() {
            document.getElementById('loadingSpinner').style.display = 'block';
        }
    </script>
</body>
</html>
