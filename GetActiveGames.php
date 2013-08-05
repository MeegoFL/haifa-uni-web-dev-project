<?php

include 'verifyCookie.php';
if( !verifyCookie() ) exit("Error: Not Logged In!");
session_start();

// Connect to Database
$mysqli = new mysqli("localhost", "root", "12345", "test");
if ($mysqli->connect_errno) echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;

// Get list of active games (in last one min)
$time = time()-60;
$stmt = $mysqli->prepare("SELECT * FROM games WHERE last_active > ?");
$stmt->bind_param('i', $time);
$stmt->execute();
$result = $stmt->get_result();

// If no games are active return message
if($result->num_rows == 0)
{
    echo "EMPTY";
    exit();
}

$gameslist = array();
while ($row = $result->fetch_array())
{
    $game_id = $row['game_id'];
    $nickname1 = $row['nickname'];
    $nickname2 = $result->fetch_array()['nickname'];
    $gameslist[] = array($game_id, $nickname1, $nickname2);
}

echo json_encode($gameslist);
$mysqli->close();
?>
