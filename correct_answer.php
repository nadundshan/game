<?php
include('db.php');  

$sql = "SELECT correct_answer FROM Questions ORDER BY RAND() LIMIT 1"; // Random correct answer
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo $row['correct_answer'];
} else {
    echo "N/A";
}

$conn->close();
?>
