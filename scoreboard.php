<?php
session_start();

// Include the database connection file
include('db.php');

// Ensure the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Get the logged-in user's username
$username = $_SESSION['username'];

// Fetch the logged-in user's ID
$sql = "SELECT id FROM Users WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$user_id = $user['id'];

// Fetch the top score of the logged-in user by joining Users and Scores tables
$sql_score = "
    SELECT Users.username, Scores.score
    FROM Users
    JOIN Scores ON Users.id = Scores.user_id
    WHERE Users.id = ? 
    ORDER BY Scores.score DESC
    LIMIT 1
";
$stmt_score = $conn->prepare($sql_score);
$stmt_score->bind_param("i", $user_id);
$stmt_score->execute();
$result_score = $stmt_score->get_result();

// Check if the user has a score
if ($result_score->num_rows > 0) {
    $row = $result_score->fetch_assoc();
    $user_score = $row["score"];
} else {
    $user_score = 0; // Default to 0 if no score found
}

// Fetch top 5 scores (or however many you want) from all users
$sql_top_scores = "
    SELECT Users.username, Scores.score
    FROM Users
    JOIN Scores ON Users.id = Scores.user_id
    ORDER BY Scores.score DESC
    LIMIT 5
";
$result_top_scores = $conn->query($sql_top_scores);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scoreboard</title>
    <style>
        body {
            font-family: 'Comic Sans MS', cursive, sans-serif;
            background-color: #f0f0f0;
            text-align: center;
            padding: 20px;
        }

        table {
            width: 50%;
            margin: 20px auto;
            border-collapse: collapse;
            background-color: #fff;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }

        th, td {
            padding: 10px;
            text-align: center;
            border: 1px solid #ddd;
        }

        th {
            background-color: #ffcc00;
        }

        td {
            background-color: #f9f9f9;
        }
        .back-btn {
            display: inline-block;
            padding: 14px 28px;
            font-size: 18px;
            color: #fff;
            background: linear-gradient(45deg, #ffcc00, #ff9900);
            border: none;
            border-radius: 12px;
            text-decoration: none;
            cursor: pointer;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            transition: background 0.3s, transform 0.3s;
        }
        .back-btn:hover {
            background: linear-gradient(45deg, #ff9900, #ff6600);
            transform: scale(1.1);
        }

        .back-btn:active {
            transform: scale(1);
            box-shadow: 0 4px 4px rgba(0, 0, 0, 0.1);
        }

        .pay-the-game {
            display: inline-block;
            padding: 14px 28px;
            font-size: 18px;
            color: #fff;
            background: linear-gradient(45deg, #ffcc00, #ff9900);
            border: none;
            border-radius: 12px;
            text-decoration: none;
            cursor: pointer;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            transition: background 0.3s, transform 0.3s;
        }
        .pay-the-game:hover {
            background: linear-gradient(45deg, #ff9900, #ff6600);
            transform: scale(1.1);
        }

        .pay-the-game:active {
            transform: scale(1);
            box-shadow: 0 4px 4px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>

    <h1>Banana Game - Scoreboard</h1>

    <table>
        <tr>
            <th>Username</th>
            <th>Score</th>
        </tr>

        <?php
        // Display "YOU" for the logged-in user's score in the top row
        echo "<tr><td><strong>YOU (" . $_SESSION['username'] . ")</strong></td><td><strong>" . $user_score . "</strong></td></tr>";

        // Output the other top scores
        if ($result_top_scores->num_rows > 0) {
            while ($row = $result_top_scores->fetch_assoc()) {
                echo "<tr><td>" . $row["username"] . "</td><td>" . $row["score"] . "</td></tr>";
            }
        } else {
            echo "<tr><td colspan='2'>No scores available</td></tr>";
        }

        // Close the connection
        $conn->close();
        ?>

    </table>

    <a href="index.php">
        <button class="back-btn">Back to Home</button>
    </a>
    
    <a href="game_interface.php">
        <button class="pay-the-game">Play the Game</button>
    </a>

</body>
</html>
