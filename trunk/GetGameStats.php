<?php

include 'verifyCookie.php';
if( !verifyCookie() ) exit("Error: Not Logged In!");
session_start();

// Get the current user nickname from session
$mynickname = $_SESSION['nickname'];

// Connect to Database
$mysqli = new mysqli("localhost", "root", "12345", "test");
if ($mysqli->connect_errno)
{
    echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
}

// Get User information from db
$result = $mysqli->query("SELECT * FROM users WHERE nickname = '$mynickname'");
if (!$result) {
    echo "Get user information SQL failed: (" . $mysqli->errno . ") " . $mysqli->error;
}

//$_SESSION['game_id'] = $game_session['game_id'];
$my_game_stat = $result->fetch_array();

// If we got here it means the game has ended successfully - remove it from table
$game_id = $my_game_stat['game_id'];
$my_result = $mysqli->query("DELETE FROM games WHERE game_id = '$game_id' AND game_end_status != '0';");
if (!$my_result) {
    exit("Remove game seesion SQL failed: (" . $mysqli->errno . ") " . $mysqli->error);
}

// Return variable with list of user's values
echo "var userGameStat = " .json_encode($my_game_stat). ";";
?>
