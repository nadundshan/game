<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Banana Game - Home</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body {
            font-family: 'Comic Sans MS', cursive, sans-serif;
            text-align: center;
            background: url('https://source.unsplash.com/1600x900/?banana,fruit') no-repeat center center fixed;
            background-size: cover;
            color: #fff;
            margin: 0;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }

        .home-container {
            background: rgba(134, 47, 156, 0.85);
            padding: 45px;
            border-radius: 20px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.4);
            max-width: 800px;
            width: 90%;
            text-align: center;
            animation: fadeIn 0.8s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: scale(0.9); }
            to { opacity: 1; transform: scale(1); }
        }

        h1 {
            color: rgb(255, 222, 33);
            text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.3);
            margin-bottom: 20px;
        }

        button {
            background: linear-gradient(45deg, #ffcc00, #ff9900);
            border: none;
            padding: 16px 28px;
            border-radius: 12px;
            font-size: 20px;
            font-weight: bold;
            cursor: pointer;
            transition: 0.3s;
            box-shadow: 3px 3px 10px rgba(0, 0, 0, 0.3);
            color: #fff;
            margin: 15px;
        }

        button:hover {
            background: linear-gradient(45deg, #ff9900, #ff6600);
            transform: scale(1.1);
        }
    </style>
</head>
<body>

    <div class="home-container">
        <h1>🍌 Welcome to the Banana Game Home Page! 🍌</h1>
        <p><strong>Hey <?php echo $_SESSION['username']; ?>, let's have some fun!</strong></p>

        <button onclick="window.location.href='login.php'">Back to Login</button>
        <button onclick="window.location.href='scoreboard.php'">Score Board</button>
        <button onclick="window.location.href='game_interface.php'">Play the Game</button>
       
        
    </div>

</body>
</html>
