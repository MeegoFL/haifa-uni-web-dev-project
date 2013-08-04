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
    <style type="text/css">
        body
        {
            background-image: url('Images/Arcomage_title.jpg');
            background-size: 100%;
            background-repeat: no-repeat;
        }
    </style>
    <script>
        function LoadGameStat() {
            // Init

            // We assume we work on: IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp = new XMLHttpRequest();

            // Wait for response
            xmlhttp.onreadystatechange = function () {
                if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                    response = xmlhttp.responseText;
                    userGameStat = JSON.parse(response);

                    // Update player's game stat on screen
                    if (userGameStat['last_game_result'] == 1) document.getElementById("game_result").innerHTML = "You've WON!";
                    else if (userGameStat['last_game_result'] == 2) document.getElementById("game_result").innerHTML = "You've LOST!";

                    document.getElementById("games_won").innerHTML = userGameStat['games_won'];
                    document.getElementById("games_lost").innerHTML = userGameStat['games_lost'];
                    document.getElementById("win_max_cards").innerHTML = userGameStat['win_max_cards'];
                    document.getElementById("win_min_cards").innerHTML = userGameStat['win_min_cards'];
                    document.getElementById("num_tower_wins").innerHTML = userGameStat['num_tower_wins'];
                    document.getElementById("num_resources_wins").innerHTML = userGameStat['num_resources_wins'];
                    document.getElementById("num_destroy_wins").innerHTML = userGameStat['num_destroy_wins'];
                    document.getElementById("num_surrender_wins").innerHTML = userGameStat['num_surrender_wins'];
                    document.getElementById("num_tower_loses").innerHTML = userGameStat['num_tower_loses'];
                    document.getElementById("num_resources_loses").innerHTML = userGameStat['num_resources_loses'];
                    document.getElementById("num_destroy_loses").innerHTML = userGameStat['num_destroy_loses'];
                    document.getElementById("num_surrender_loses").innerHTML = userGameStat['num_surrender_loses'];
                    document.getElementById("games_played").innerHTML = userGameStat['games_played'];
                    document.getElementById("win_precentage").innerHTML = Math.round((userGameStat['games_won'] / userGameStat['games_played']) * 100);

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
    <table style="width:400px; margin: auto; background-color: #fff; opacity: 0.8; text-align: center;">
        <tr style="background-color: #ffd800;">
            <td colspan="2">
                <h1>Game Statistics</h1>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <h1 id="game_result">Win/Lose</h1>
            </td>
        </tr>
        <tr style="background-color: #0094ff; font-size: 20px; font-weight: 900;">
            <td style="text-align: justify; width: fit-content;">
                <div>Total Games Played</div>
            </td>
            <td style="text-align: left;">
                <div id="games_played">num</div>
            </td>
        </tr>
        <tr style="background-color: #b6ff00; font-size: 20px; font-weight: 900;">
            <td style="text-align: justify; width: fit-content;">
                <div>Games Won</div>
                <div>Win Precentage</div>
                <div>Win Max Cards Used</div>
                <div>Win Min Cars Used</div>
                <div>Tower Wins</div>
                <div>Resources Wins</div>
                <div>Destroy Wins</div>
                <div>Opponent Surrender Wins</div>
            </td>
            <td style="text-align: left;">
                <div id="games_won">num</div>
                <div id="win_precentage">num</div>
                <div id="win_max_cards">num</div>
                <div id="win_min_cards">num</div>
                <div id="num_tower_wins">num</div>
                <div id="num_resources_wins">num</div>
                <div id="num_destroy_wins">num</div>
                <div id="num_surrender_wins">num</div>
            </td>
        </tr>
        <tr style="background-color: #f00;  font-size: 20px; font-weight: 900;">
            <td style="text-align: justify; width: fit-content;">
                <div>Games Lost</div>
                <div>Tower Loses</div>
                <div>Resources Loses</div>
                <div>Tower Destroyed Loses</div>
                <div>Surrendered Loses  </div>
            </td>
            <td style="text-align: left;">
                <div id="games_lost">num</div>
                <div id="num_tower_loses">num</div>
                <div id="num_resources_loses">num</div>
                <div id="num_destroy_loses">num</div>
                <div id="num_surrender_loses">num</div>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <input type="submit" value="Back to Lobby" onclick="location.href='Lobby.php';">
            </td>
        </tr>
    </table>

</body>
</html>


<?php
}
else {
    //    header('Location: Login.html');
}
?>
