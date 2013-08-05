<?php
// Get the values and check for XSS or SQL injection
preg_match('/^[a-zA-Z0-9]+$/',$_REQUEST["nickname"]) ? $nickname = $_REQUEST["nickname"] : exit('XSS is detected!');

// Connect to Database
$db_ini = parse_ini_file('Arcomage.ini');
$mysqli = new mysqli($db_ini['host'], $db_ini['username'], $db_ini['password'], $db_ini['db']);
if ($mysqli->connect_errno) echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;

// Check if username exists
$result = $mysqli->query("SELECT * FROM users WHERE nickname = '$nickname'");

// If username doesn't exist return error
if($result->num_rows == 0)
{
    exit("User Name wasn't found!");
}

$result->data_seek(0);
$row = $result->fetch_assoc();
($row['free_to_play'] == "0") ? $newState = "1" : $newState = "0";

$result = $mysqli->query("UPDATE users SET free_to_play = '".$newState."' WHERE nickname = '$nickname'");
?>