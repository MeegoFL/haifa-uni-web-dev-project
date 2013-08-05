<?php
include 'verifyCookie.php';
session_start();
if (verifyCookie() && ($_SESSION['nickname'] == "admin")) {

    $db_ini = parse_ini_file('Arcomage.ini');
    $mysqli = new mysqli($db_ini['host'], $db_ini['username'], $db_ini['password'], $db_ini['db']);
    if ($mysqli->connect_errno) echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;

    // Drop existing table and start a new
    $mysqli->query("DROP TABLE IF EXISTS users");
    if($mysqli->errno) echo "Table drop failed: (" . $mysqli->errno . ") " . $mysqli->error;
    $mysqli->query("DROP TABLE IF EXISTS games");
    if($mysqli->errno) echo "Table drop failed: (" . $mysqli->errno . ") " . $mysqli->error;

    // Create Table users
    $query = "CREATE TABLE IF NOT EXISTS users(
	username CHAR(50) NOT NULL,
	password CHAR(50) NOT NULL,
	nickname CHAR(50) NOT NULL,
	last_active INT(10) DEFAULT 0,
    free_to_play TINYINT(1) DEFAULT 0,
	games_won int(10) DEFAULT 0,
	games_lost int (10) DEFAULT 0,
	games_played int(10) DEFAULT 0,
	num_tower_wins int(10) DEFAULT 0,
	num_resources_wins int(10) DEFAULT 0,
	num_destroy_wins int(10) DEFAULT 0,
	num_surrender_wins int(10) DEFAULT 0,
	win_max_cards int(10) DEFAULT 0,
	win_min_cards int(10) DEFAULT 1000,
	num_tower_loses int(10) DEFAULT 0,
	num_resources_loses int(10) DEFAULT 0,
	num_destroy_loses int(10) DEFAULT 0,
	num_surrender_loses int(10) DEFAULT 0,
    last_game_result int(10) DEFAULT 0,
    PRIMARY KEY (`username`)
);";

    //Use this to safely create a table while testing
    if(!$mysqli->query($query)) echo "Table creation failed: (" . $mysqli->errno . ") " . $mysqli->error;


    // Create Table games
    $query = "CREATE TABLE IF NOT EXISTS games(
	game_id INT(10) NOT NULL,
	nickname CHAR(50) NOT NULL,
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
    last_played_card INT(10) DEFAULT 0,
    current_flag TINYINT(1) DEFAULT 0,
    cards_played INT(10) DEFAULT 0,
    game_end_status INT(10) DEFAULT 0,
    last_active BIGINT(20) DEFAULT 0,
    start_time BIGINT(20) DEFAULT 0,
    PRIMARY KEY (`nickname`)
);";

    //Use this to safely create a table while testing
    if(!$mysqli->query($query)) echo "Table creation failed: (" . $mysqli->errno . ") " . $mysqli->error;

    // Add the administraor user
    $password = sha1("TalRanalpine");
    $query = "INSERT INTO users (username, password, nickname) VALUES ('administrator','" .$password. "', 'admin');";

    //Use this to safely create a table while testing
    if(!$mysqli->query($query)) echo "Table creation failed: (" . $mysqli->errno . ") " . $mysqli->error;
}

else
{
    echo "<script type='text/javascript'>
        window.alert('You are not Administrator');
        window.location.href='index.html';
        </script>";
}
?>