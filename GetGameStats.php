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

//$_SESSION['game_id'] = $game_session['game_id'];

// Return variable with list of user's values
$my_game_stat = $result->fetch_array();
echo "var userGameStat = " .json_encode($my_game_stat). ";";
?>
