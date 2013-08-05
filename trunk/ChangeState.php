<?php
// Start / Update Session
session_start();

// Verify existing cookie for user to be able to see / use the page
include 'verifyCookie.php';
if (verifyCookie()) {

    // Get the current user nickname from session
    $nickname = explode('|', $_COOKIE["ArcomageCookie"])[0];

    // Connect to Database
    $db_ini = parse_ini_file('Arcomage.ini');
    $mysqli = new mysqli($db_ini['host'], $db_ini['username'], $db_ini['password'], $db_ini['db']);
    if ($mysqli->connect_errno) echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;

    // Check if username exists
    $stmt = $mysqli->prepare("SELECT * FROM users WHERE nickname = ? ");
    $stmt->bind_param('s', $nickname);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    // If username doesn't exist return error
    if($result->num_rows == 0) exit("User Name wasn't found!");

    // Else change the free_to_play to new state
    $row = $result->fetch_array();
    ($row['free_to_play'] == "0") ? $newState = "1" : $newState = "0";

    $stmt = $mysqli->prepare("UPDATE users SET free_to_play = ?  WHERE nickname = ? ");
    $stmt->bind_param('is', $newState, $nickname);
    $stmt->execute();
    $stmt->close();
    $mysqli->close();
}
// User not allowed throw out!
else 
{
    echo "<script type='text/javascript'>
                window.alert('You are not Logged In!');
                window.location.href='index.html';
                </script>";
}
?>
?>