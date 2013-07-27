<?php

include 'verifyCookie.php';
if( !verifyCookie() ) exit("Error: Not Logged In!");

// Get the current user nickname from session
$mynickname = $_SESSION['nickname'];

// Connect to Database
$mysqli = new mysqli("localhost", "root", "12345", "test");
if ($mysqli->connect_errno)
{
    echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
}

// Get list of active users (in last one hour)
$time = time()-60;
$result = $mysqli->query("SELECT * FROM users WHERE last_active > '".$time."'");

// Check if nickname is in games list meaning he was invited to a game
$invites = $mysqli->query("SELECT * FROM games WHERE nickname = '".$mynickname."'");

// If no users are active return message
if($result->num_rows == 0)
{
    echo "No Online Users";
}

$game_session = $invites->fetch_array();
$last_active = $game_session['last_active'];

// If no user is in game list -> move him to game.php
if($invites->num_rows > 0 && ($last_active > time()-60))
{
    echo "window.location.href='Game.php';";
}

$userlist = array();
$i = 0;
while ($row = $result->fetch_array())
{
    $nickname = $row['nickname'];
    $free2play = $row['free_to_play'];
    $userList[$i] = array($nickname, $free2play);
    $i++;
}

echo "var userList = " .json_encode($userList);
?>
