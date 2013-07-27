<?php

include 'verifyCookie.php';
if( !verifyCookie() ) exit("Error: Not Logged In!");
// Connect to Database
$mysqli = new mysqli("localhost", "root", "12345", "test");
if ($mysqli->connect_errno)
{
    echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
}

// Get list of active users (in last one hour)
$time = time()-60;
$result = $mysqli->query("SELECT * FROM users WHERE last_active > '".$time."'");

// If username doesn't exist return error
if($result->num_rows == 0)
{
    echo "No Online Users";
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
