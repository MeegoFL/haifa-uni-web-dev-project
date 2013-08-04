<?php
include 'verifyCookie.php';
verifyCookie();
session_start();

$nickname = $_SESSION['nickname'];
$game_id = $_SESSION['game_id'];

$mysqli = new mysqli("localhost", "root", "12345", "test");

// Check connection
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

// Update last_active time for user
$currentTime = time();
$mysqli->query("UPDATE users SET last_active = '$currentTime' WHERE nickname = '$nickname'");
$mysqli->query("UPDATE games SET last_active = '$currentTime' WHERE game_id = '$game_id'");

// Get the player's game stat for current game
$result = $mysqli->query("SELECT * FROM games WHERE game_id = '$game_id'");

$GameStat[0] = $result->fetch_array();
if ($GameStat[0]['nickname'] == $nickname) {
    $GameStat[1] = $result->fetch_array();
}

else {
    $GameStat[1] = $GameStat[0];
    $GameStat[0] = $result->fetch_array();
}

echo json_encode($GameStat);

$mysqli->close();
?>
