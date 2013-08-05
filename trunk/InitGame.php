<?php
include 'verifyCookie.php';
verifyCookie();
session_start();

// Get the values and check for XSS or SQL injection
preg_match('/^[a-zA-Z0-9]+$/', $_REQUEST["nickname"]) ? $opponent_nickname = $_REQUEST["nickname"] : exit('XSS is detected!');
$my_nickname = explode('|', $_COOKIE["ArcomageCookie"])[0];

$db_ini = parse_ini_file('Arcomage.ini');
$mysqli = new mysqli($db_ini['host'], $db_ini['username'], $db_ini['password'], $db_ini['db']);
if ($mysqli->connect_errno) echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;

// Clear Game sessions not active for last 2hr
$deleteLastActive = time() - 120;
$stmt = $mysqli->prepare("DELETE FROM games WHERE last_active < ?;");
$stmt->bind_param('i', $deleteLastActive);
$stmt->execute();
if ($stmt->errno) exit("Clear Game sessions SQL failed: (" . $stmt->errno . ") " . $stmt->error);
$stmt->close();


// Check if username exists
$time   = time() - 60;
$stmt = $mysqli->prepare("SELECT * FROM users WHERE nickname = ? AND last_active > ?;");
$stmt->bind_param('si', $my_nickname, $time);
$stmt->execute();
if ($stmt->errno) exit("Check username SQL failed: (" . $stmt->errno . ") " . $stmt->error);
$result = $stmt->get_result();

// If username doesn't exist return error
if ($result->num_rows == 0) exit('Suspicious Activity, access denied');

// Set users as busy since they going into a game
$stmt = $mysqli->prepare("UPDATE users SET free_to_play = 0 WHERE nickname = ? OR nickname = ?;");
$stmt->bind_param('ss', $my_nickname, $opponent_nickname);
$stmt->execute();
if ($stmt->errno) exit("Set users as busy SQL failed: (" . $stmt->errno . ") " . $stmt->error);
$stmt->close();

$stmt = $mysqli->prepare("SELECT * FROM games WHERE game_id = ?;");
$stmt->bind_param('i', $game_id);
for ($i = 1, $game_id = 0; $game_id == 0; $i++)
{
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows == 0) $game_id = $i;
}
$stmt->close();

// Initilze the new game parmeters for player who init the game
$_SESSION['game_id'] = $game_id;
$_SESSION['nickname'] = $my_nickname;
$_SESSION['cards_played'] = 0;

$time = time();

// Draw who has first turn
$first_turn = $time % 2;
$opponent_turn = !$first_turn;

$stmt = $mysqli->prepare("INSERT INTO games (game_id, nickname, current_flag, card1_id, card2_id, card3_id, card4_id, card5_id, card6_id, last_active)
        VALUES (?, ?, ?, " . rand(1, 102) . ", " . rand(1, 102) . ", " . rand(1, 102) . ", " . rand(1, 102) . ", " . rand(1, 102) . ", " . rand(1, 102) . ", ?);");
$stmt->bind_param('isii', $game_id, $my_nickname, $first_turn, $time);
$stmt->execute();
if($stmt->errno) echo "Connect 1 failed: (" . $stmt->errno . ") " . $stmt->error;
$stmt->close();

$stmt = $mysqli->prepare("INSERT INTO games (game_id, nickname, current_flag, card1_id, card2_id, card3_id, card4_id, card5_id, card6_id, last_active)
        VALUES (?, ?, ?, " . rand(1, 102) . ", " . rand(1, 102) . ", " . rand(1, 102) . ", " . rand(1, 102) . ", " . rand(1, 102) . ", " . rand(1, 102) . ", ?);");
$stmt->bind_param('isii', $game_id, $opponent_nickname, $opponent_turn, $time);
$stmt->execute();
if($stmt->errno) echo "Connect 1 failed: (" . $stmt->errno . ") " . $stmt->error;
$stmt->close();

echo "GAME_START";
$mysqli->close();
?>