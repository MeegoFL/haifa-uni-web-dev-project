<?php
    include 'verifyCookie.php';
    verifyCookie();
     // Get the values and check for XSS or SQL injection
    preg_match('/^[a-zA-Z0-9]+$/',$_REQUEST["nickname1"]) ? $nickname1 = $_REQUEST["nickname1"] : exit('XSS is detected!');
    $nickname2 = $_COOKIE["nickname"];

    $mysqli = new mysqli("localhost", "root", "12345", "test");
    // Check connection
    if (mysqli_connect_errno())
    {
            echo "Failed to connect to MySQL: " . mysqli_connect_error();
    }
    
    
    $time = time() - 60;
    // Check if username exists
    $result = $mysqli->query("SELECT * FROM users WHERE nickname = '".$nickname1."' AND last_active > '".$time."' AND free_to_play = '1'");

    // If username doesn't exist return error
    if($result->num_rows == 0) 
    {
        exit("window.alert('Suspicious Activity, access denied');\nwindow.location.href='index.html';");
    }

    for ($i=1, $game_id=0; $game_id == 0; $i++){
        $result = $mysqli->query("SELECT * FROM games WHERE game_id = '$i'");
        if($result->num_rows == 0) {
            $game_id = $i;
        }
    }

    $first_turn = rand(0,1);
    $mysqli->query("INSERT INTO games (game_id, nickname, current_flag, card1_id, card2_id, card3_id, card4_id, card5_id, card6_id)
        VALUES ('$game_id', '$nickname1', '$first_turn', ".rand(1,102).", ".rand(1,102).", ".rand(1,102).", ".rand(1,102).", "
        .rand(1,102).", ".rand(1,102).")" );

    $mysqli->query("INSERT INTO games (game_id, LastName, current_flag,c ard1_id, card2_id, card3_id, card4_id, card5_id, card6_id)
        VALUES ('$game_id', '$nickname2','".($first_turn xor 1)."', ".rand(1,102).", ".rand(1,102).", ".rand(1,102).", ".rand(1,102).", "
        .rand(1,102).", ".rand(1,102).")" );

    echo "window.location.href='Game.php';";
?>