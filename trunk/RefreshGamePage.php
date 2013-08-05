<?php
include 'verifyCookie.php';
verifyCookie();
session_start();

$nickname = $_SESSION['nickname'];
$game_id = $_SESSION['game_id'];

$db_ini = parse_ini_file('Arcomage.ini');
$mysqli = new mysqli($db_ini['host'], $db_ini['username'], $db_ini['password'], $db_ini['db']);
if ($mysqli->connect_errno) echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;

// Update last_active time for user
$currentTime = time();
$stmt = $mysqli->prepare("UPDATE users SET last_active = ? WHERE nickname = ?;");
$stmt->bind_param('is', $currentTime, $nickname);
$stmt->execute();
$stmt->close();

$stmt = $mysqli->prepare("UPDATE games SET last_active = ? WHERE game_id = ?;");
$stmt->bind_param('is', $currentTime, $game_id);
$stmt->execute();
$stmt->close();

// Get the player's game stat for current game
$stmt = $mysqli->prepare("SELECT * FROM games WHERE game_id = ?;");
$stmt->bind_param('i', $game_id);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();

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
