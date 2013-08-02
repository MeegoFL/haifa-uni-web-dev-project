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

$GameStat = $result->fetch_array();
if ($GameStat['nickname'] == $nickname) {
    echo "var userGameStat = " .json_encode($GameStat). ";";
    $GameStat = $result->fetch_array();
    echo "var opponentGameStat = " .json_encode($GameStat). ";";
}

else {
    echo "var opponentGameStat = " .json_encode($GameStat). ";";
    $GameStat = $result->fetch_array();
    echo "var userGameStat = " .json_encode($GameStat). ";";
}

$mysqli->close();
?>
