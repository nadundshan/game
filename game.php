<?php 
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$api_url = "http://marcconrad.com/uob/banana/api.php?out=json";
$response = file_get_contents($api_url);
$data = json_decode($response, true);

$_SESSION['solution'] = $data['solution'];

echo json_encode(['image' => $data['question']]);
?> 
