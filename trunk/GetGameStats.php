<?php

include 'verifyCookie.php';
if( !verifyCookie() ) exit("Error: Not Logged In!");
session_start();

// Get the current user nickname from session
$mynickname = $_SESSION['nickname'];

$db_ini = parse_ini_file('Arcomage.ini');
$mysqli = new mysqli($db_ini['host'], $db_ini['username'], $db_ini['password'], $db_ini['db']);
if ($mysqli->connect_errno) echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;

// Get user's game result from table
$result = $mysqli->query("SELECT game_end_status FROM games WHERE nickname = '$mynickname';");
if (!$result) exit("User's game result SQL failed: (" . $mysqli->errno . ") " . $mysqli->error);
$user_result = $result->fetch_array()[0];

// If we got here it means the game has ended successfully - remove it from table
$result = $mysqli->query("DELETE FROM games WHERE nickname = '$mynickname' AND game_end_status != '0';");
if (!$result) exit("Remove game seesion SQL failed: (" . $mysqli->errno . ") " . $mysqli->error);

// Get User information from db
$result = $mysqli->query("SELECT * FROM users WHERE nickname = '$mynickname'");
if (!$result) echo "Get user information SQL failed: (" . $mysqli->errno . ") " . $mysqli->error;

$my_user_stat = $result->fetch_array();

// Return variable with list of user's values
echo json_encode($my_user_stat);
?>
