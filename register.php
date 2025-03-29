<?php
include 'db.php'; // Database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Encrypt password

    // Check if username already exists
    $checkSql = "SELECT id FROM users WHERE username = ?";
    $stmt = $conn->prepare($checkSql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $error = "❌ Username already exists. Please choose a different one.";
    } else {
        // Insert new user
        $stmt->close();
        $insertSql = "INSERT INTO users (username, password) VALUES (?, ?)";
        $stmt = $conn->prepare($insertSql);
        $stmt->bind_param("ss", $username, $password);

        if ($stmt->execute()) {
            $success = "✅ Registration successful! You can now login.";
        } else {
            $error = "❌ Error: " . $stmt->error;
        }
    }
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Banana Game - Register</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: url('https://source.unsplash.com/1600x900/?banana') no-repeat center center fixed;
            background-size: cover;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .register-container {
            background: rgba(255, 255, 255, 0.9);
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            text-align: center;
            max-width: 400px;
            width: 100%;
        }
        h2 {
            color: #ffcc00;
            font-size: 24px;
        }
        .register-container input {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }
        .register-container button {
            background: #ffcc00;
            border: none;
            padding: 12px 20px;
            border-radius: 5px;
            font-size: 18px;
            cursor: pointer;
            transition: background 0.3s ease;
            width: 100%;
        }
        .register-container button:hover {
            background: #e6b800;
        }
        .message {
            font-size: 14px;
            margin-top: 10px;
        }
        .success-message {
            color: green;
        }
        .error-message {
            color: red;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <h2>🍌 Join the Banana Game 🍌</h2>
        <?php 
        if (isset($success)) { echo "<p class='message success-message'>$success</p>"; } 
        if (isset($error)) { echo "<p class='message error-message'>$error</p>"; }
        ?>
        <form method="POST" action="">
            <input type="text" name="username" placeholder="Enter Username" required>
            <input type="password" name="password" placeholder="Enter Password" required>
            <button type="submit">Register</button>
        </form>
        <p>Already have an account? <a href="login.php">Login here</a></p>
    </div>
</body>
</html>
