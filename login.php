<?php
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['username'] = $username;
            header("Location: index.php");
            exit();
        } else {
            $error = "❌ Invalid username or password!";
        }
    } else {
        $error = "❌ Invalid username or password!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Banana Game - Login</title>
    <style>
        /*Background Styling */
        body {
            font-family: 'Comic Sans MS', cursive, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            overflow: hidden;
            
            animation: fadeIn 1.5s ease-in-out;
        }

        body {
            background: url('images/background_ime_login_page.jpg') no-repeat center center fixed;
            background-size: cover;   
        }

        /* Fade-in Animation */
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        /* Login Container */
        .login-container {
            background: rgba(56, 53, 53, 0.9);
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            text-align: center;
            max-width: 250px;
            width: 100%;
            animation: slideIn 0.8s ease-in-out;
        }

        /* Slide-in Effect */
        @keyframes slideIn {
            from { transform: translateY(-30px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        h2 {
            color: #ffcc00;
            font-size: 26px;
            animation: bounce 1s infinite alternate;
        }

        /* Bounce Effect */
        @keyframes bounce {
            from { transform: translateY(0px); }
            to { transform: translateY(-5px); }
        }

        .login-container input {
            width: 89%;
            padding: 12px;
            margin: 10px 0;
            border: 2px solid #ffcc00;
            border-radius: 5px;
            font-size: 16px;
            transition: transform 0.2s ease-in-out;
        }

        /* Input Zoom Effect */
        .login-container input:focus {
            transform: scale(1.05);
            border-color: #e6b800;
        }

        .login-container button {
            background: #ffcc00;
            border: none;
            padding: 12px 20px;
            border-radius: 5px;
            font-size: 18px;
            cursor: pointer;
            transition: background 0.3s ease, transform 0.2s ease-in-out;
            width: 100%;
        }

        /* Button Hover Effect */
        .login-container button:hover {
            background: #e6b800;
            transform: scale(1.05);
        }

        .error-message {
            color: red;
            font-size: 14px;
        }

        .forgot-password, .register-button {
            margin-top: 10px;
            font-size: 14px;
        }

        .forgot-password a, .register-button a {
            color: #ffcc00;
            text-decoration: none;
        }

        .forgot-password a:hover, .register-button a:hover {
            text-decoration: underline;
        }

        /* Music Control */
        .music-controls {
            position: absolute;
            bottom: 20px;
            right: 20px;
            background: rgba(255, 255, 255, 0.8);
            padding: 10px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .music-controls button {
            background:rgb(201, 27, 143);
            border: none;
            padding: 8px 12px;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .music-controls button:hover {
            background: #e6b800;
        }

        .music-controls input {
            width: 100px;
        }
    </style>
</head>
<body>

    <!-- Background Music -->
    <audio id="bg-music" loop>
        <source src="audio_tracks\Aylex_Guardians.mp3" type="audio/mp3">
    </audio>

    <div class="login-container">
        <h2>🍌 Welcome 🍌</h2>
        <h2>- Banana Game -</h2>
        <?php if (isset($error)) { echo "<p class='error-message'>$error</p>"; } ?>
        <form method="post" action="">
            <input type="text" name="username" placeholder="Enter Username" required>
            <input type="password" name="password" placeholder="Enter Password" required>
            <button type="submit">Login</button>
        </form>
        <div class="forgot-password">
            <a href="reset_password.php">Forgot Password?</a>
        </div>
        <div class="register-button">
            <a href="register.php"><button>Register</button></a>
        </div>
    </div>

    <!-- Music Controls -->
    <div class="music-controls">
        <button id="music-toggle" onclick="toggleMusic()">🔊</button>
        <input type="range" id="volume-slider" min="0" max="1" step="0.1" value="0.5">
    </div>

    <script>
        let music = document.getElementById("bg-music");
        let musicToggle = document.getElementById("music-toggle");
        let volumeSlider = document.getElementById("volume-slider");

        // Auto-play music when page loads
        document.addEventListener("DOMContentLoaded", function() {
            music.volume = 0.5;
            music.play().catch(error => {
                console.log("Autoplay blocked by browser, user must interact first.");
            });
        });

        function toggleMusic() {
            if (music.paused) {
                music.play();
                musicToggle.textContent = "🔊";
            } else {
                music.pause();
                musicToggle.textContent = "🔇";
            }
        }

        // Adjust volume with slider
        volumeSlider.addEventListener("input", function() {
            music.volume = volumeSlider.value;
        });
    </script>

</body>
</html>
