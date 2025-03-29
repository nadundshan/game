<?php
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Validate passwords match
    if ($new_password != $confirm_password) {
        $error = "❌ Passwords do not match!";
    } else {
        // Hash the new password
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        // Update password in database
        $sql = "UPDATE users SET password = ? WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $hashed_password, $username);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            $success = "✔️ Password updated successfully!";
        } else {
            $error = "❌ Username not found!";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Banana Game - Reset Password</title>
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
        .reset-container {
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
        .reset-container input {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }
        .reset-container button {
            background: #ffcc00;
            border: none;
            padding: 12px 20px;
            border-radius: 5px;
            font-size: 18px;
            cursor: pointer;
            transition: background 0.3s ease;
            width: 100%;
        }
        .reset-container button:hover {
            background: #e6b800;
        }
        .error-message {
            color: red;
            font-size: 14px;
        }
        .success-message {
            color: green;
            font-size: 14px;
        }
        .back-button {
            margin-top: 10px;
            font-size: 14px;
        }
        .back-button a {
            color: #ffcc00;
            text-decoration: none;
        }
        .back-button a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="reset-container">
        <h2>🍌 Reset Your Password 🍌</h2>
        <?php if (isset($error)) { echo "<p class='error-message'>$error</p>"; } ?>
        <?php if (isset($success)) { echo "<p class='success-message'>$success</p>"; } ?>
        <form method="post" action="">
            <input type="text" name="username" placeholder="Enter Username" required>
            <input type="password" name="new_password" placeholder="Enter New Password" required>
            <input type="password" name="confirm_password" placeholder="Confirm New Password" required>
            <button type="submit">Reset Password</button>
        </form>
        <div class="back-button">
            <a href="login.php">Back to Login</a>
        </div>
    </div>
</body>
</html>
