<?php
session_start();
include "db.php";

if (!isset($_SESSION['username'])) {
    exit("Not logged in");
}

$username = $_SESSION['username'];
$correctAnswer = $_SESSION['solution'] ?? null;
$userAnswer = $_POST['answer'];

if ($correctAnswer === null) {
    exit("Error: No solution set in session.");
}

// Determine score change
$scoreChange = ($userAnswer == $correctAnswer) ? 10 : -5;
echo $scoreChange > 0 
    ? "<p style='color: white;'>Correct! +10 Points</p>" 
    : "<p style='color: red;'>Wrong! -5 Points</p>";

// Step 1: Get user_id from users table
$userQuery = "SELECT id FROM users WHERE username = ?";
$userStmt = $conn->prepare($userQuery);
$userStmt->bind_param("s", $username);
$userStmt->execute();
$userResult = $userStmt->get_result();

if ($userResult->num_rows === 0) {
    exit("Error: User not found.");
}

$userRow = $userResult->fetch_assoc();
$userId = $userRow['id'];
$userStmt->close();

// Step 2: Check if the user already has a score record
$scoreQuery = "SELECT score FROM scores WHERE user_id = ?";
$scoreStmt = $conn->prepare($scoreQuery);
$scoreStmt->bind_param("i", $userId);
$scoreStmt->execute();
$scoreResult = $scoreStmt->get_result();

// Step 3: Update existing score or insert new record
if ($scoreResult->num_rows > 0) {
    $scoreRow = $scoreResult->fetch_assoc();
    $currentScore = $scoreRow['score'];

    // Update score
    $updateQuery = "UPDATE scores SET score = score + ? WHERE user_id = ?";
    $updateStmt = $conn->prepare($updateQuery);
    $updateStmt->bind_param("ii", $scoreChange, $userId);
    $updateStmt->execute();
    $updateStmt->close();
    
    $currentScore += $scoreChange; // Manually update the current score
} else {
    // Insert new score record
    $currentScore = $scoreChange;
    $insertQuery = "INSERT INTO scores (user_id, score) VALUES (?, ?)";
    $insertStmt = $conn->prepare($insertQuery);
    $insertStmt->bind_param("ii", $userId, $currentScore);
    $insertStmt->execute();
    $insertStmt->close();
}

$scoreStmt->close();
$conn->close();

// Display the updated score
echo "<p><strong>Your Score: $currentScore</strong></p>";
?>