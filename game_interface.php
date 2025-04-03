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
          
        /*.Your-Score  {
            display: flex;
            width: 95%;
            max-width: 1200px;
            background: rgb(240, 243, 55);
            padding: 10px;
            border-radius: 10px;
            box-shadow: 0 5px 20px rgba(33, 240, 92, 0.4);
            animation: fadeIn 7s ease-in-out;
        }  */        

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
            border: 2px solid #ffcc00;
            width: 160px;
            text-align: center;
            font-size: 20px;
            font-weight: bold;
            background: #fff8dc;
        }

        input[type='text']:invalid {
            border: 2px solid red;
        }

        label{
            font-size: 24px;
            font-weight: bold;
            color: blue;
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
        #moodContainer {
    position: absolute;
    top: 10%;
    left: 50%;
    transform: translateX(-50%);
    z-index: 999;
    display: none; /* Initially hidden */
}

#moodContainer img {
    width: 80px;
    position: absolute;
    opacity: 1;
}

@keyframes fall {
    0% { transform: translateY(-50px); opacity: 1; }
    100% { transform: translateY(300px); opacity: 0; }
}

.fall-animation {
    animation: fall 1.5s ease-in-out forwards;
}

    </style>
</head>
<body>
<div id="moodContainer">
    <img id="sadMood" src="😴😴" alt="😴😴">
    <img id="happyMood" src="👌👌" alt="👌👌">
</div>

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
   
              <p>Your Score In Previous Play Time: <strong><span id="score"><?php echo $user_score; ?></span></strong></p>

              <label>Enter the Answer:</label>
             </div>

             <div class="time-counting">
                   <input type="text" id="answer" pattern="[0-9]*" inputmode="numeric" oninput="validateInput(this)">
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
    let userAnswer = $("#answer").val();

    if (userAnswer === "") {
        alert("Enter the Answer!"); // Show an alert
        return; // Stop function execution
    }
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
        document.getElementById("answer").value = ""; // Clear the input field after checking the answer
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

    updateScore();
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
    //checkAnswer();
});

function checkAnswer() {
    let userAnswer = $("#answer").val();

    if (!userAnswer) {
        alert("Enter the Answer!");
        return;
    }

    $.post("check.php", { answer: userAnswer }, function(response) {
        $("#result").html(response);
        
        if (response.includes("Wrong")) {
            wrongAttempts++;
            showMoodEffect("sad");
        } else {
            wrongAttempts = 0;
            showMoodEffect("happy");
        }

        if (wrongAttempts >= 3) {
            triggerBombExplosion();
            wrongAttempts = 0;
        }

        updateScore();
        loadQuestion();
        $("#answer").val(""); // Clear input field
    });
}

function showMoodEffect(type) {
    let moodImg = (type === "sad") ? "#sadMood" : "#happyMood";

    $("#moodContainer").show();
    $(moodImg).addClass("fall-animation");

    setTimeout(() => {
        $(moodImg).removeClass("fall-animation");
        $("#moodContainer").hide();
    }, 1500);
}
 
            
</script>
</body>
</html>
