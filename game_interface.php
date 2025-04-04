<?php
session_start();
include 'music_player.php';
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

include('db.php');  

$username = $_SESSION['username'];
$sql = "SELECT id FROM Users WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    $user_id = $user['id'];

    $sql_score = "SELECT score FROM Scores WHERE user_id = ?";
    $stmt_score = $conn->prepare($sql_score);
    $stmt_score->bind_param("i", $user_id);
    $stmt_score->execute();
    $result_score = $stmt_score->get_result();
    
    $user_score = ($result_score->num_rows > 0) ? $result_score->fetch_assoc()['score'] : 0;


} else {
    die("User not found.");
}

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
            background: url('https://source.unsplash.com/1600x900/?banana,fruit') no-repeat center center fixed;
            background-size: cover;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }

        .game-container {
            display: flex;
            width: 100%;
            max-width: 1200px;
            background: rgba(207, 102, 242, 0.95);
            padding: 25px;
            border-radius: 20px;
            box-shadow: 0 5px 20px rgba(8, 1, 1, 0.4);
            animation: fadeIn 3s ease-in-out;
        }

        .time-counting {
            display: flex;
            width: 98%;
            max-width: 1200px;
            background:  rgba(178, 18, 231, 0.95);
            padding: 01px;
            border-radius: 5px;
            box-shadow: 0 5px 20px rgba(8, 1, 1, 0.4);
            animation: fadeIn 3s ease-in-out;
        }

        .back {
            display: flex;
            width: 97%;
            max-width: 1200px;
            background: rgb(211, 26, 214);
            padding: 10px;
            border-radius: 10px;
            box-shadow: 0 5px 20px rgba(8, 1, 1, 0.4);
            animation: fadeIn 3s ease-in-out;
        }

        .back {
             display: flex;
             gap: 10px; /* Space between buttons */
             }
             
        .qi {
            display: flex;
            width: 97%;
            max-width: 1200px;
            background: rgb(211, 26, 214);
            padding: 10px;
            border-radius: 10px;
            box-shadow: 0 5px 20px rgba(8, 1, 1, 0.4);
            animation: fadeIn 7s ease-in-out;
        }  
          
        .Your-Score  {
            display: block;
            width: 95%;
            max-width: 1200px;
            background: rgb(0, 0, 0);
            padding: 10px;
            border-radius: 10px;
            box-shadow: 0 25px 20px rgb(255, 251, 0);
            animation: fadeIn 5s ease-in-out;
        }         

        .time-counting {
             display: flex;
             gap: 30px; /* answer and time counting */
             }     

        @keyframes fadeIn {
            from { opacity: 0; transform: scale(0.9); }
            to { opacity: 1; transform: scale(1); }
        }

        .left-container {
            flex: 1;
            text-align: center;
        }

        .right-container {
            flex: 1;
            text-align: left;
            padding-left: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        input[type='text'] {
            padding: 12px;
            border-radius: 10px;
            border: 2px solidhsl(48, 100.00%, 50.00%);
            width: 160px;
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            background: #fff8dc;
        }

        input[type='text']:invalid {
            border: 2px solid red;
        }

        label{
            font-size: 24px;
            font-weight: bold;
            color: black;
        }

        button {
            background: linear-gradient(45deg, #ffcc00, #ff9900);
            border: none;
            padding: 16px 28px;
            border-radius: 12px;
            font-size: 15px;
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

        #timer {
            font-size: 24px;
            font-weight: bold;
            color: white;
        }

        #score {
            font-size: 24px;
            font-weight: bold;
            color:rgb(37, 26, 239);
        }

        .game-control {
            margin-top: 20px;
        }

        #gameButtons button {
            display: inline-block;
            margin: 10px;
        }

        #questionImage {
            display: none;
        }
  

@keyframes fall {
    0% { transform: translateY(-50px); opacity: 1; }
    100% { transform: translateY(300px); opacity: 0; }
}

.fall-animation {
    animation: fall 1.5s ease-in-out forwards;
}
h3 {
    font-size: 22px;
    color: #ffeb3b;
    text-shadow: 2px 2px 4px black;
}

    </style>
</head>
<body>


    <div class="game-container">
        <div class="left-container">
           
            <div class="back">
             <button id="logoutBtn" onclick="goToExit()"> Logout</button> 
             <button id="homeBtn" onclick="goToHome()"> Home</button>
             <button id="scoreboardBtn" onclick="goToScoreboard()">Scoreboard</button><br>
             <button id="gameStartBtn" onclick="toggleGame()">Start Game</button>
            </div>
            <br>
            <div class="qi">
              <img id="questionImage" src="" alt="Loading..."> <br>
            </div>
           
        </div>

        <div class="right-container">
            <div>
              <h1>🍌 Welcome, <?php echo $_SESSION['username']; ?>! 🍌</h1>

<p id="levelDisplay" style="font-size: 24px; font-weight: bold; color: green;">Achievement Level: </p>


              <label>Enter the Answer:</label>
             </div>

             <div class="time-counting">
                   <input type="text" id="answer" pattern="[0-9]*" placeholder="Answer" inputmode="numeric" oninput="validateInput(this)">
                   <h1>Time</h1>
                   <p id="timer">60</p>
                   </div>
                   <p id="error-message" style="color: yellow; font-weight: bold; display: none;">Please enter numbers only!</p>
                   <br>
                  <button onclick="checkAnswer()">Check Answer</button>
                  
                  <div>
                  <p id="result" class="Your-Score">Your Score: 00</p>
                 </div>

                 <div class="game-control">
        
                  </div>
                  </div>

 <script>
    let wrongAttempts = 0;
    let timer;
    let isGameActive = false;
    let currentScore = 0;
    let currentLevel = "";

    function getLevel(score) {
    if (score < 50) return "Banana Baby";
    else if (score < 100) return "Banana Warrior";
    else if (score < 150) return "Banana Master";
    else return "Banana God";
}

function updateLevel(score) {
    const newLevel = getLevel(score);
    if (newLevel !== currentLevel) {
        currentLevel = newLevel;
        $("#levelDisplay").fadeOut(300, function () {
            $(this).text("Level: " + newLevel).fadeIn(300);
        });
    }
}




    function startTimer() {
        let timeLeft = 60;
        clearInterval(timer);
        timer = setInterval(() => {
            if (timeLeft <= 0) {
                clearInterval(timer);
                loadQuestion();
            }
            document.getElementById('timer').innerText = timeLeft;
            timeLeft--;
        }, 1000);
    }

    function validateInput(input) {
        let value = input.value;
        let filteredValue = value.replace(/[^0-9]/g, ''); // Letters & symbols ඉවත් කරලා numbers විතරක් තබාගන්න.

        if (value !== filteredValue) {
            document.getElementById("error-message").style.display = "block"; // Error message පෙන්වන්න
            alert("Please enter numbers only!"); // Alert box එක
        } else {
            document.getElementById("error-message").style.display = "none"; // Error message එක remove කරන්න
        }

        input.value = filteredValue;
    }


    function loadQuestion() {
        $.get("game.php", function(data) {
            let response = JSON.parse(data);
            $("#questionImage").attr("src", response.image);
            $("#questionImage").show(); // Show question image
            startTimer();
        });
    }

function checkAnswer() {
    const userAnswer = document.getElementById("answer").value;

    $.post("check.php", { answer: userAnswer }, function(response) {
        $("#result").html(response);

        // The Score extract from response
        const match = response.match(/Your Score:\s*(\d+)/i);
        if (match) {
            currentScore = parseInt(match[1]);
            updateLevel(currentScore); // <-- Level auto update
        }

        if (response.includes("Wrong")) {
            wrongAttempts++;
        } else {
            wrongAttempts = 0;
        }

        if (wrongAttempts >= 3) {
            triggerBombExplosion();
            wrongAttempts = 0;
        }

        loadQuestion();
        document.getElementById("answer").value = "";
    });
}



    function triggerBombExplosion() {
        $("#bombExplosion").fadeIn().delay(1000).fadeOut();
        alert("💥 Boom! You answered wrong 3 times! Be careful!");
    }

    

    function goToScoreboard() {
        window.location.href = "scoreboard.php";
    }
    function goToHome() {
        window.location.href = "index.php";
    }
    function goToExit() {
        window.location.href = "login.php";
    }

    $(document).ready(function() {
    // Disable answer input and check answer button on page load
    $("#answer").prop("disabled", true);
    $("button:contains('Check Answer')").prop("disabled", true);

    
});

$(document).ready(function () {
    $("#logoutBtn, #homeBtn, #scoreboardBtn").on("mouseenter", function () {
        if (isGameActive) {
            alert("First stop the game!");
        }
    });
});


function toggleGame() {
    if (isGameActive) {
        // Stop the game
        clearInterval(timer);
        $("#questionImage").hide(); // Hide question image
        $("#gameStartBtn").text("Start Game");

        // Disable answer input and check answer button
        $("#answer").prop("disabled", true);
        $("button:contains('Check Answer')").prop("disabled", true);

        // Enable other buttons
        $("#logoutBtn, #homeBtn, #scoreboardBtn").prop("disabled", false);
    } else {
        // Start the game
        loadQuestion();
        $("#gameStartBtn").text("Stop Game");

        // Enable answer input and check answer button
        $("#answer").prop("disabled", false);
        $("button:contains('Check Answer')").prop("disabled", false);

        // Disable other buttons
        $("#logoutBtn, #homeBtn, #scoreboardBtn").prop("disabled", true);
    }
    isGameActive = !isGameActive;
}

$("button:contains('Check Answer')").off("click").on("click", function() {
    if (!isGameActive) {
        alert("Please start the game first!");
        return;
    }
    
});           
</script>
</body>
</html>
