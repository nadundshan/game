<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Include the database connection file
include('db.php');

// Get the current logged-in user's username from the session
$username = $_SESSION['username'];

// Fetch the user's ID from the Users table
$sql = "SELECT id FROM Users WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    $user_id = $user['id'];

    // Fetch the user's score from the Scores table
    $sql_score = "SELECT score FROM Scores WHERE user_id = ?";
    $stmt_score = $conn->prepare($sql_score);
    $stmt_score->bind_param("i", $user_id);
    $stmt_score->execute();
    $result_score = $stmt_score->get_result();
    
    if ($result_score->num_rows > 0) {
        $score_row = $result_score->fetch_assoc();
        $user_score = $score_row['score'];
    } else {
        $user_score = 0;
    }
} else {
    die("User not found.");
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Banana Game</title>
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

        .game-container {
            background: rgba(134, 47, 156, 0.95);
            padding: 25px;
            border-radius: 20px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.4);
            max-width: 1500px;
            width: 80%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            text-align: left;
            animation: fadeIn 0.8s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: scale(0.9); }
            to { opacity: 1; transform: scale(1); }
        }

        h1, h2 {
            color: rgb(255, 222, 33);
            text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.3);
            text-align: center;
            width: 100%;
        }

        #gameArea {
            flex: 1;
            text-align: center;
        }

        #gameArea img {
            width: 550px;
            margin: 10px 0;
            border-radius: 12px;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.3);
        }

        .right-container {
            flex: 1;
            text-align: left;
            padding-left: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .game-inputs {
            display: flex;
            flex-direction: column;
            gap: 12px;
            align-items: flex-start;
        }

        input {
            padding: 12px;
            border-radius: 10px;
            border: 2px solid #ffcc00;
            width: 160px;
            text-align: center;
            font-size: 20px;
            font-weight: bold;
            background: #fff8dc;
        }

        button {
            background: linear-gradient(45deg, #ffcc00, #ff9900);
            border: none;
            padding: 14px 24px;
            border-radius: 12px;
            font-size: 20px;
            font-weight: bold;
            cursor: pointer;
            transition: 0.3s;
            box-shadow: 3px 3px 10px rgba(0, 0, 0, 0.3);
            align-self: flex-start;
            color: #fff;
        }

        button:hover {
            background: linear-gradient(45deg, #ff9900, #ff6600);
            transform: scale(1.1);
        }

        #score {
            font-size: 24px;
            font-weight: bold;
            color: #d44d00;
        }

        #result {
            font-size: 20px;
            font-weight: bold;
            color: #333;
            margin-top: 12px;
        }

        .bomb {
            width: 100px;
            display: none;
        }

        .back-btn {
            margin-top: 20px;
            padding: 12px 20px;
            font-size: 16px;
            background-color: rgb(255, 174, 0);
            color: black;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .back-btn:hover {
            background-color: #e6b800;
        }

        
        .exit {
            margin-top: 20px;
            padding: 12px 20px;
            font-size: 16px;
            background-color:rgb(255, 30, 0);
            color: black;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .exit:hover {
            background-color:rgb(230, 0, 0);
        }
    </style>
</head>
<body>

    <div class="game-container">  
        <div id="gameArea">
            <img id="questionImage" src="" alt="Loading...">
            <img id="bombExplosion" class="bomb" src="bomb_explosion.gif" alt="💣💥">
        </div>

        <div class="right-container">
            <h1>🍌 Welcome, YOU <?php echo $_SESSION['username']; ?>! 🍌</h1>
           <!-- <p>Your current score: <strong><span id="score"><?php echo $user_score; ?></span></strong></p> -->

            <div class="game-inputs">
                <label>Enter the missing value:</label>
                <input type="text" id="answer">
                <button onclick="checkAnswer()">Submit</button>
            </div>

            <p id="result"></p>
            <button class="back-btn" onclick="goBack()">🔙 Home Page</button>

            <a href="login.php">
        <button class="exit">Exit</button>
    </a>

        </div>
    </div>

    <script>
        let wrongAttempts = 0;

        function loadQuestion() {
            $.get("game.php", function(data) {
                let response = JSON.parse(data);
                $("#questionImage").attr("src", response.image);
            });
        }

        function checkAnswer() {
            let userAnswer = $("#answer").val();
            $.post("check.php", { answer: userAnswer }, function(response) {
                $("#result").html(response);
                if (response.includes("Wrong")) {
                    wrongAttempts++;
                } else {
                    wrongAttempts = 0;
                }

                if (wrongAttempts >= 3) {
                    triggerBombExplosion();
                    wrongAttempts = 0;
                }

                updateScore();
                loadQuestion();
            });
        }

        function triggerBombExplosion() {
            $("#bombExplosion").fadeIn().delay(1000).fadeOut();
            alert("💥 Boom! You answered wrong 3 times! Be careful!");
        }

        function updateScore() {
            $.get("update_score.php", function(data) {
                $("#score").text(data);
            });
        }

        function goBack() {
            window.location.href = "index.php";
        }

        $(document).ready(function() {
            loadQuestion();
            updateScore();
        });
    </script>

</body>
</html>