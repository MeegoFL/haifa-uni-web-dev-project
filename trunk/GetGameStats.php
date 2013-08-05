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
$stmt = $mysqli->prepare("SELECT game_end_status FROM games WHERE nickname = ?;");
$stmt->bind_param('s', $mynickname);
$stmt->execute();
if ($stmt->errno) exit("User's game result SQL failed: (" . $stmt->errno . ") " . $stmt->error);
$result = $stmt->get_result();
$stmt->close();
$user_result = $result->fetch_array()[0];

// If we got here it means the game has ended successfully - remove it from table
$stmt = $mysqli->prepare("DELETE FROM games WHERE nickname = ? AND game_end_status != '0';");
$stmt->bind_param('s', $mynickname);
$stmt->execute();
if ($stmt->errno) exit("Remove game seesion SQL failed: (" . $stmt->errno . ") " . $stmt->error);
$result = $stmt->get_result();
$stmt->close();

// Get User information from db
$stmt = $mysqli->prepare("SELECT * FROM users WHERE nickname = ?;");
$stmt->bind_param('s', $mynickname);
$stmt->execute();
if ($stmt->errno) exit("Get user information SQL failed: (" . $stmt->errno . ") " . $stmt->error);
$result = $stmt->get_result();
$stmt->close();
$my_user_stat = $result->fetch_array();

// Return variable with list of user's values
echo json_encode($my_user_stat);
$mysqli->close();
?>
