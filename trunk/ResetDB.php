<?php
$mysqli = new mysqli("localhost", "root", "12345", "test");

if($mysqli->connect_errno) echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
echo $mysqli->host_info . "\n";

// Drop existing table and start a new
if(!$mysqli->query("DROP TABLE IF EXISTS users") || !$mysqli->query($query)) echo "Table drop failed: (" . $mysqli->errno . ") " . $mysqli->error;
if(!$mysqli->query("DROP TABLE IF EXISTS games") || !$mysqli->query($query)) echo "Table drop failed: (" . $mysqli->errno . ") " . $mysqli->error;

// Create Table users
$query = "CREATE TABLE users(
	username CHAR(50),
	password CHAR(50),
	nickname CHAR(50),
    last_active INT(10) DEFAULT 0,
    free_to_play TINYINT(1) DEFAULT 0,
    PRIMARY KEY (`username`)
);";

//Use this to safely create a table while testing
if(!$mysqli->query($query)) echo "Table creation failed: (" . $mysqli->errno . ") " . $mysqli->error;


// Create Table games
$query = "CREATE TABLE games(
	game_id INT(10),
	nickname CHAR(50),
	magic INT(10) DEFAULT 2,
    gems INT(10) DEFAULT 20,
    quarry INT(10) DEFAULT 2,
    bricks INT(10) DEFAULT 20,
    dungeon INT(10) DEFAULT 2,
    recruits INT(10) DEFAULT 20,
    tower INT(10) DEFAULT 20,
    wall INT(10) DEFAULT 10,
    card1_id INT(10),
    card2_id INT(10),
    card3_id INT(10),
    card4_id INT(10),
    card5_id INT(10),
    card6_id INT(10),
    current_flag TINYINT(1) DEFAULT 0,
    cards_played BIGINT(20) DEFAULT 0,
    discard_turn TINYINT(1) DEFAULT 0,
    last_active BIGINT(20) DEFAULT 0,
    PRIMARY KEY (`nickname`)
);";

//Use this to safely create a table while testing
if(!$mysqli->query($query)) echo "Table creation failed: (" . $mysqli->errno . ") " . $mysqli->error;
?>