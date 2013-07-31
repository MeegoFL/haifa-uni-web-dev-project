<?php
include 'verifyCookie.php';
verifyCookie();
session_start();

// Get the values and check for XSS or SQL injection
preg_match('/^[a-zA-Z0-9]+$/', $_REQUEST["nickname1"]) ? $nickname1 = $_REQUEST["nickname1"] : exit('XSS is detected!');
$nickname2 = $_SESSION['nickname'];

$mysqli = new mysqli("localhost", "root", "12345", "test");
// Check connection
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

// Clear Game sessions not active for last 24hr
$deleteLastActive = time() - 86400;
$mysqli->query("DELETE FROM games WHERE last_active < '$deleteLastActive'");

$time   = time() - 60;
// Check if username exists
$result = $mysqli->query("SELECT * FROM users WHERE nickname = '" . $nickname1 . "' AND last_active > '" . $time . "' AND free_to_play = '1'");

// If username doesn't exist return error
if ($result->num_rows == 0) {
    exit("window.alert('Suspicious Activity, access denied');\nwindow.location.href='index.html';");
}

// Set users as busy since they going into a game
$mysqli->query("UPDATE users SET free_to_play = 0 WHERE nickname = '$nickname1' OR nickname = '$nickname2'");

for ($i = 1, $game_id = 0; $game_id == 0; $i++) {
    $result = $mysqli->query("SELECT * FROM games WHERE game_id = '$i'");
    if ($result->num_rows == 0) {
        $game_id = $i;
    }
}

$_SESSION['game_id'] = $game_id;

$time = time();
$first_turn = rand(0, 1);
$opponent_turn = !$first_turn;

if (!$mysqli->query("INSERT INTO games (game_id, nickname, current_flag, card1_id, card2_id, card3_id, card4_id, card5_id, card6_id, last_active)
        VALUES ('$game_id', '$nickname1', '$first_turn', " . rand(1, 102) . ", " . rand(1, 102) . ", " . rand(1, 102) . ", " . rand(1, 102) . ", " . rand(1, 102) . ", " . rand(1, 102) . ", " .$time. ")")) {
            echo "Connect 1 failed: (" . $mysqli->errno . ") " . $mysqli->error;
        }

if(!$mysqli->query("INSERT INTO games (game_id, nickname, current_flag, card1_id, card2_id, card3_id, card4_id, card5_id, card6_id, last_active)
        VALUES ('$game_id', '$nickname2', '$opponent_turn', " . rand(1, 102) . ", " . rand(1, 102) . ", " . rand(1, 102) . ", " . rand(1, 102) . ", " . rand(1, 102) . ", " . rand(1, 102) . ", " .$time. ")")) {
            echo "Connect 2 failed: (" . $mysqli->errno . ") " . $mysqli->error;
        }

echo "window.location.href='Game.php';";
?>