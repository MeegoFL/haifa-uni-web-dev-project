<?php
include 'verifyCookie.php';
verifyCookie();
session_start();

// Get the values and check for XSS or SQL injection
preg_match('/^[a-zA-Z0-9]+$/', $_REQUEST["nickname"]) ? $opponent_nickname = $_REQUEST["nickname"] : exit('XSS is detected!');
$my_nickname = explode('|', $_COOKIE["ArcomageCookie"])[0];

$mysqli = new mysqli("localhost", "root", "12345", "test");
// Check connection
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

// Clear Game sessions not active for last 2hr
$deleteLastActive = time() - 120;
$mysqli->query("DELETE FROM games WHERE last_active < '$deleteLastActive'");

$time   = time() - 60;
// Check if username exists
$result = $mysqli->query("SELECT * FROM users WHERE nickname = '$my_nickname' AND last_active > '$time'");

// If username doesn't exist return error
if ($result->num_rows == 0) {
    exit("window.alert('Suspicious Activity, access denied');\nwindow.location.href='index.html';");
}

// Set users as busy since they going into a game
$mysqli->query("UPDATE users SET free_to_play = 0 WHERE nickname = '$my_nickname' OR nickname = '$opponent_nickname'");

for ($i = 1, $game_id = 0; $game_id == 0; $i++) {
    $result = $mysqli->query("SELECT * FROM games WHERE game_id = '$i'");
    if ($result->num_rows == 0) {
        $game_id = $i;
    }
}

$_SESSION['game_id'] = $game_id;
$_SESSION['nickname'] = $my_nickname;
$_SESSION['cards_played'] = 0;

$time = time();
$first_turn = $time%1;
$opponent_turn = !$first_turn;
sleep(1); // for some unknown reason somtimes opponent_turn gets null value -> might need to wait for function to end.

if (!$mysqli->query("INSERT INTO games (game_id, nickname, current_flag, card1_id, card2_id, card3_id, card4_id, card5_id, card6_id, last_active)
        VALUES ('$game_id', '$my_nickname', '$first_turn', " . rand(1, 102) . ", " . rand(1, 102) . ", " . rand(1, 102) . ", " . rand(1, 102) . ", " . rand(1, 102) . ", " . rand(1, 102) . ", " .$time. ")")) {
    echo "Connect 1 failed: (" . $mysqli->errno . ") " . $mysqli->error;
}

if(!$mysqli->query("INSERT INTO games (game_id, nickname, current_flag, card1_id, card2_id, card3_id, card4_id, card5_id, card6_id, last_active)
        VALUES ('$game_id', '$opponent_nickname', '$opponent_turn', " . rand(1, 102) . ", " . rand(1, 102) . ", " . rand(1, 102) . ", " . rand(1, 102) . ", " . rand(1, 102) . ", " . rand(1, 102) . ", " .$time. ")")) {
    echo "Connect 2 failed: (" . $mysqli->errno . ") " . $mysqli->error;
}

echo "GAME_START";
?>