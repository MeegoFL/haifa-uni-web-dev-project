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
       function RefreshView() {
            // Init

            // We assume we work on: IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp = new XMLHttpRequest();

            // Wait for response
            xmlhttp.onreadystatechange = function () {
                if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                    response = xmlhttp.responseText;
                    eval(response);
                    //alert(response);

                    // Update player's game stat on screen
                    document.getElementById("gamesPlayed").innerHTML = userGameStat['games_played'];

                                        setTimeout('RefreshView()', refreshInterval);
                }
            }

            // Ready the values and POST the request
            xmlhttp.open("POST", "RefreshGamePage.php", true);
            xmlhttp.send();
        }
        window.onload = function () {
            RefreshView();
        }
    </script>

</head>

<body onload = "RefreshView();">


<div id="container" style="width:800px;margin-left: auto;margin-right: auto;">

<div id="header" style="background-color:#FFA500;">
<h1 style="margin-bottom:0;">Game Stats</h1></div>

<div id="menu" style="background-color:#FFD700;height:200px;width:200px;float:left;">
<b>Current game</b><br>
Winner<br>
Total game time<br>
Total number of moves<br>
bla bla bla more stats
</div>

<div id="content" style="background-color:#EEEEEE;height:200px;width:600px;float:left;">
<b id="winnerName">Winner name</b><br>
<b id="gameTime">Game time</b><br>
<b id="numOfMoves">Number of moves</b><br>
</div>

<div id="menu" style="background-color:#FFD700;height:200px;width:200px;float:left;">
<b>Overall games won</b><br>
Games Won<br>
Longest winning streak<br>
Tower wins<br>
Resource wins<br>
Enemy's tower Destroyed<br>
Enemy surrendered<br>
</div>

<div id="content" style="background-color:#EEEEEE;height:200px;width:200px;float:left;">
Games Played: <b id="gamesPlayed">games played</b><br>
</div>

<div id="menu" style="background-color:#FFD700;height:200px;width:200px;float:left;">
<b>Overall games lost</b><br>
Games lost<br>
Longest losing streak<br>
Enemy tower wins<br>
Enemy resource wins<br>
Tower destroyed<br>
Surrendered<br>
</div>

<div id="content" style="background-color:#EEEEEE;height:200px;width:200px;float:left;">
Games Played: <b id="gamesPlayed">games played</b><br>
</div>

<div id="menu" style="background-color:#FFD700;height:200px;width:200px;float:left;">
<b>Current game</b><br>
Games Played<br>
Win percentage<br>
Current streak<br>
Maximum turns<br>
Minimum turns<br>
Avarage turns<br>

</div>

<div id="content" style="background-color:#EEEEEE;height:200px;width:600px;float:left;">
<b id=""></b><br>
</div>

<div id="footer" style="background-color:#FFA500;clear:both;text-align:center;">
Copyright © TalRan</div>

</div>

</body>
</html>


<?php
}
else {
    //    header('Location: Login.html');
}
?>
