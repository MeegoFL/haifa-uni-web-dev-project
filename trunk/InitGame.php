<?php

     // Get the values and check for XSS or SQL injection
    preg_match('/^[a-zA-Z0-9]+$/',$_REQUEST["username"]) ? $nickname1 = $_REQUEST["nickname1"] : exit('XSS is detected!');
    preg_match('/^[a-zA-Z0-9]+$/',$_REQUEST["password"]) ? $nickname2 = md5($_REQUEST["nickname2"]) : exit('XSS is detected!');

     // Check if username exists
    $result1 = $mysqli->query("SELECT * FROM users WHERE nickname = '$nickname1'");
    $result2 = $mysqli->query("SELECT * FROM users WHERE nickname = '$nickname2'");
 
     // If username doesn't exist return error
    if($result1->num_rows == 0 || $result2->num_rows == 0) 
    {
        echo "<script type='text/javascript'>
                window.alert('Suspicious Activity, access denied');
                window.location.href='index.html';
                </script>";
    }
 

    $con=mysqli_connect("localhost", "root", "12345", "test");
    // Check connection
    if (mysqli_connect_errno()){
            echo "Failed to connect to MySQL: " . mysqli_connect_error();
        }

    for ($i=1, $game_id=0; $game_id = 0; $i++){
        $result = $mysqli->query("SELECT * FROM games WHERE game_id = '$i'");
        if($result->num_rows == 0) {
            $game_id = $i;
        }
    }

    $first_turn = rand(0,1);
    mysqli_query($con,"INSERT INTO games (game_id, nickname, current_flag, card1_id, card2_id, card3_id, card4_id, card5_id, card6_id)
        VALUES ('$game_id', '$nickname1', '$first_turn', ".rand(1,102).", ".rand(1,102).", ".rand(1,102).", ".rand(1,102).", "
        .rand(1,102).", ".rand(1,102).")" );

    mysqli_query($con,"INSERT INTO games (game_id, LastName, current_flag,c ard1_id, card2_id, card3_id, card4_id, card5_id, card6_id)
        VALUES ('$game_id', '$nickname2','".($first_turn xor 1)."', ".rand(1,102).", ".rand(1,102).", ".rand(1,102).", ".rand(1,102).", "
        .rand(1,102).", ".rand(1,102).")" );

    mysqli_close($con);

?>