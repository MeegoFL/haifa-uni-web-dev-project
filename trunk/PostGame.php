<?php
include 'verifyCookie.php';
if( verifyCookie() ) {
    session_start();
?>


<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <title>Post Game Page</title>
    <script>
        function LoadGameStat() {
            // Init

            // We assume we work on: IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp = new XMLHttpRequest();

            // Wait for response
            xmlhttp.onreadystatechange = function () {
                if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                    response = xmlhttp.responseText;
                    eval(response);

                    // Update player's game stat on screen
                    document.getElementById("games_won").innerHTML = userGameStat['games_won'];
                    document.getElementById("games_lost").innerHTML = userGameStat['games_lost'];
                    document.getElementById("win_max_cards").innerHTML = userGameStat['win_max_cards'];
                    document.getElementById("win_min_cards").innerHTML = userGameStat['win_min_cards'];
                    document.getElementById("win_avg_cards").innerHTML = userGameStat['win_avg_cards'];
                    document.getElementById("num_tower_wins").innerHTML = userGameStat['num_tower_wins'];
                    document.getElementById("num_resources_wins").innerHTML = userGameStat['num_resources_wins'];
                    document.getElementById("num_destroy_wins").innerHTML = userGameStat['num_destroy_wins'];
                    document.getElementById("num_surrender_wins").innerHTML = userGameStat['num_surrender_wins'];
                    document.getElementById("num_tower_loses").innerHTML = userGameStat['num_tower_loses'];
                    document.getElementById("num_resources_loses").innerHTML = userGameStat['num_resources_loses'];
                    document.getElementById("num_destroy_loses").innerHTML = userGameStat['num_destroy_loses'];
                    document.getElementById("num_surrender_loses").innerHTML = userGameStat['num_surrender_loses'];
                    document.getElementById("longest_win_streak").innerHTML = userGameStat['longest_win_streak'];
                    document.getElementById("longest_lose_streak").innerHTML = userGameStat['longest_lose_streak'];
                    document.getElementById("current_streak").innerHTML = userGameStat['current_streak'];
                    document.getElementById("games_played").innerHTML = userGameStat['games_played'];
                    document.getElementById("win_precentage").innerHTML = (userGameStat['games_won'] / userGameStat['games_played']) * 100;

                }
            }

            // Ready the values and POST the request
            xmlhttp.open("POST", "GetGameStats.php", true);
            xmlhttp.send();
        }

        window.onload = function () {
            LoadGameStat();
        }
    </script>

</head>

<body onload="LoadGameStat();">


    <div id="container" style="width:800px;margin-left: auto;margin-right: auto;">

        <div id="header" style="background-color:#FFA500;">
            <h1 style="margin-bottom:0;">Game Stats</h1>
        </div>

        <div id="menu" style="background-color:#FFD700;height:200px;width:200px;float:left;">
            <b>Current game</b>
            <br />
            Winner
            <br />
            Total game time
            <br />
            Total number of moves
            <br />
            bla bla bla more stats
        </div>

        <div id="content" style="background-color:#EEEEEE;height:200px;width:600px;float:left;">
            <b id="winnerName">Winner name</b>
            <br />
            <b id="gameTime">Game time</b>
            <br />
            <b id="numOfMoves">Number of moves</b>
            <br />
        </div>

        <div id="menu" style="background-color:#FFD700;height:200px;width:200px;float:left;">
            <b>Overall games won</b>
            <br />
            <br>
                Games Won:
                <b id="games_won"></b>
            </br>
            <br>
                Longest winning streak:
                <b id="longest_win_streak"></b>
            </br>
            <br>
                Tower wins:
                <b id="num_tower_wins"></b>
            </br>
            <br>
                Resource wins:
                <b id="num_resources_wins"></b>
            </br>
            <br>
                Enemy's tower Destroyed:
                <b id="num_destroy_wins"></b>
            </br>
            <br>
                Enemy surrendered:
                <b id="num_surrender_wins"></b>
            </br>
        </div>

        <div id="content" style="background-color:#EEEEEE;height:200px;width:200px;float:left;">
            Games Played:
            <b id="games_played">games played</b>
            <br />
        </div>

        <div id="menu" style="background-color:#FFD700;height:200px;width:200px;float:left;">
            <b>Overall games lost</b>
            <br />
            Games lost
            <br />
            Longest losing streak
            <br />
            Enemy tower wins
            <br />
            Enemy resource wins
            <br />
            Tower destroyed
            <br />
            Surrendered
            <br />
        </div>

        <div id="content" style="background-color:#EEEEEE;height:200px;width:200px;float:left;">
            Games Played:
            <b id="gamesPlayed">games played</b>
            <br />
        </div>

        <div id="menu" style="background-color:#FFD700;height:200px;width:200px;float:left;">
            <b>Current game</b>
            <br />
            Games Played
            <br />
            Win percentage
            <br />
            Current streak
            <br />
            Maximum turns
            <br />
            Minimum turns
            <br />
            Avarage turns
            <br />

        </div>

        <div id="content" style="background-color:#EEEEEE;height:200px;width:600px;float:left;">
            <b id=""></b>
            <br />
        </div>

        <div id="footer" style="background-color:#FFA500;clear:both;text-align:center;">
            Copyright © TalRan
        </div>

    </div>

</body>
</html>


<?php
}
else {
    //    header('Location: Login.html');
}
?>
