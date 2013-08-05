<?php
include 'verifyCookie.php';
if( verifyCookie() ) {
    session_start();
?>

<!DOCTYPE html>

<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Game Page</title>
    <script>
        function allowDrop(ev) {
            ev.stopPropagation();
            ev.preventDefault();
        }

        function drag(ev) {
            ev.dataTransfer.setData("Text", ev.target.id);
            //ev.dataTransfer.setData("CardID", ev.target.alt);
        }

        function StartMove(cardID) {
            window.moveEvent = cardID;
            cards = document.getElementsByClassName("card_hand");
            for (var i = 0; i < cards.length; i++) {
                cards[i].removeAttribute("style");
            }
            document.getElementById(cardID).setAttribute("style", "border:1px solid red; opacity:0.8;");
        }

        function EndMove() {
            if (window.moveEvent) {
                document.getElementById("played_card").src = document.getElementById(window.moveEvent).src;
                PerformAction(window.moveEvent);
                window.moveEvent = null;
            }
        }

        function drop(ev) {
            ev.preventDefault();
            var elmID = ev.dataTransfer.getData("Text");
            ev.target.src = document.getElementById(elmID).src;
            PerformAction(elmID);
        }

        function EndGame(result) {
            switch (result) {
                case "1":
                    alert("You WIN!");
                    break;
                case "2":
                    alert("You LOSE!");
                    break;
                default:
                    return; // Error: funciton shouldn't have been called
            }
            window.location.href = 'postgame.php';
        }

        function RefreshView() {
            // Init
            refreshInterval = 2000;

            // We assume we work on: IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp = new XMLHttpRequest();

            // Wait for response
            xmlhttp.onreadystatechange = function () {
                if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                    response = xmlhttp.responseText;
                    gameStat = JSON.parse(response);
                    userGameStat = gameStat[0];
                    opponentGameStat = gameStat[1];


                    if (userGameStat['game_end_status'] != 0) 
                    {
                        refreshInterval = 10000;
                        EndGame(userGameStat['game_end_status']);
                    }

                    // Update player's game stat on screen
                    document.getElementById("myTowerImg").height = 100+200*(userGameStat['tower'] / 100);
                    document.getElementById("myWallImg").height = 60+80*(userGameStat['wall'] / 100);
                    
                    document.getElementById("player_name").innerHTML = userGameStat['nickname'];
                    document.getElementById("myTowerVal").innerHTML = userGameStat['tower'];
                    document.getElementById("myWallVal").innerHTML = userGameStat['wall'];
                    document.getElementById("myMagic").innerHTML = userGameStat['magic'];
                    document.getElementById("myGems").innerHTML = userGameStat['gems'];
                    document.getElementById("myQuarry").innerHTML = userGameStat['quarry'];
                    document.getElementById("myBricks").innerHTML = userGameStat['bricks'];
                    document.getElementById("myDungeon").innerHTML = userGameStat['dungeon'];
                    document.getElementById("myRecruits").innerHTML = userGameStat['recruits'];
                    document.getElementById("card1_id").src = "Images/" + userGameStat['card1_id'] + ".png";
                    document.getElementById("card2_id").src = "Images/" + userGameStat['card2_id'] + ".png";
                    document.getElementById("card3_id").src = "Images/" + userGameStat['card3_id'] + ".png";
                    document.getElementById("card4_id").src = "Images/" + userGameStat['card4_id'] + ".png";
                    document.getElementById("card5_id").src = "Images/" + userGameStat['card5_id'] + ".png";
                    document.getElementById("card6_id").src = "Images/" + userGameStat['card6_id'] + ".png";
                    document.getElementById("card1_id").title = userGameStat['card1_id'];
                    document.getElementById("card2_id").title = userGameStat['card2_id'];
                    document.getElementById("card3_id").title = userGameStat['card3_id'];
                    document.getElementById("card4_id").title = userGameStat['card4_id'];
                    document.getElementById("card5_id").title = userGameStat['card5_id'];
                    document.getElementById("card6_id").title = userGameStat['card6_id'];
                    document.getElementById("played_card").src = "Images/" + userGameStat['last_played_card'] + ".png";

                    if (userGameStat['current_flag'] == 1) {
                        document.getElementById("userMessages").innerHTML = "YOUR TURN!";
                        document.getElementById("input_button").removeAttribute("disabled");

                        cards = document.getElementsByClassName("card_hand");
                        for (var i = 0; i < cards.length; i++) {
                            cards[i].setAttribute("draggable", "true");
                            cards[i].setAttribute("style", "opacity:1;");
                            cards[i].setAttribute("onclick", "StartMove(this.id)");
                        }
                        refreshInterval = 60000;
                    }
                    else {
                        document.getElementById("userMessages").innerHTML = "Opponent's turn";
                        document.getElementById("input_button").setAttribute("disabled", "true");
                        cards = document.getElementsByClassName("card_hand");
                        for (var i = 0; i < cards.length; i++) {
                            cards[i].setAttribute("draggable", "false");
                            cards[i].setAttribute("style", "opacity:0.6;");
                            cards[i].setAttribute("onclick", "");
                        }
                        refreshInterval = 2000;
                    }

                    // Update opponent's game stat on screen
                    document.getElementById("opponentTowerImg").height = 50+200*(opponentGameStat['tower'] / 100);
                    document.getElementById("opponentWallImg").height = 60+80*(opponentGameStat['wall'] / 100);
                    
                    document.getElementById("opponent_name").innerHTML = opponentGameStat['nickname'];
                    document.getElementById("opponentTowerVal").innerHTML = opponentGameStat['tower'];
                    document.getElementById("opponentWallVal").innerHTML = opponentGameStat['wall'];
                    document.getElementById("opponentMagic").innerHTML = opponentGameStat['magic'];
                    document.getElementById("opponentGems").innerHTML = opponentGameStat['gems'];
                    document.getElementById("opponentQuarry").innerHTML = opponentGameStat['quarry'];
                    document.getElementById("opponentBricks").innerHTML = opponentGameStat['bricks'];
                    document.getElementById("opponentDungeon").innerHTML = opponentGameStat['dungeon'];
                    document.getElementById("opponentRecruits").innerHTML = opponentGameStat['recruits'];

                    setTimeout('RefreshView()', refreshInterval);
                }
            }

            // Ready the values and POST the request
            xmlhttp.open("POST", "RefreshGamePage.php", true);
            xmlhttp.send();
        }

        function PerformAction(cardID) {
            // Init
            document.getElementById("input_text").value = "";

            // We assume we work on: IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp = new XMLHttpRequest();

            // Wait for response
            xmlhttp.onreadystatechange = function () {
                if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                    response = xmlhttp.responseText;

                    if (response.indexOf("Error:") !== -1) {
                        alert(response.substr(7));
                    }

                    else if (response == "GameOver") {
                        RefreshView();
                    }

                    else if (response != "") {
                        alert(response);
                    }
                    RefreshView();
                }
            }

            // Ready the values and POST the request
            var str = "?card_location=" + cardID;
            xmlhttp.open("POST", "GameLogic.php" + str, true);
            xmlhttp.send();
        }

        window.onload = function () {
            RefreshView();
        }
    </script>
</head>

<body onload="RefreshView();" style="background-image: url(Images/game-background.jpg);background-repeat:no-repeat;background-size:cover;">
    <audio id="audio" autoplay="" loop="">
        <source src="Media/GOT_Soundtrack.ogg" type="audio/ogg">
        <source src="Media/GOT_Soundtrack.mp3" type="audio/mpeg">
        Your browser does not support the audio element.
    </audio>
    <div>
        <button onclick="document.getElementById('audio').play()">Play the Audio</button>
        <button onclick="document.getElementById('audio').pause()">Pause the Audio</button>
    </div>
    <table style="border: 0;border-spacing: 10px; padding: 12px;margin-left: auto;margin-right: auto;">
        <tr>
            <td colspan="8" style="background-color:#FFA500;text-align: center;">
                <h1>Arcomage</h1>
            </td>
        </tr>

        <tr>
            <td colspan="3" id="player_name" style="background-color:#ffd800;text-align: center; font-weight: 900;">your stats</td>
            <td colspan="2"></td>
            <td colspan="3" id="opponent_name" style="background-color:#ffd800;text-align: center; font-weight: 900;">opponent stats</td>
        </tr>

        <tr>
            <td rowspan="2" style="background-image:url(Images/resources.png);background-repeat:no-repeat;
                    background-size: 120px 350px;color: #EEEEEE;width:100px;height:280px;padding-left: 20px;
                    vertical-align: top;padding-top: 70px;white-space: nowrap;">
                <b>
                    <br>
                        <b id="myGems">num</b>
                        &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp +
                        <b id="myMagic">num</b>
                    </br>
                    <br style="line-height: 95px;">
                        <b id="myBricks">num</b>
                        &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp +
                        <b id="myQuarry">num</b>
                    </br>
                    <br style="line-height: 95px;">
                        <b id="myRecruits">num</b>
                        &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp +
                        <b id="myDungeon">num</b>
                    </br>
                </b>
            </td>

            <td rowspan="2" colspan="2" style="vertical-align: bottom; text-align: center; width: 250px;">
                <span style="background-color: #ffd800; position: relative; bottom: 0px;">
                    <br><b>tower: </b><b id="myTowerVal">num</b></br>
                        <br><b>wall: </b><b id="myWallVal">num</b></br>
                </span>
                <img id="myTowerImg" src="Images/towe_trans.gif" alt="tower1" width="100" height="250" style="position: relative; bottom: -80px;" draggable="false" />
                <img id="myWallImg" src="Images/wall_trans.gif" alt="wall1" width="250" height="80" style="position: relative;" draggable="false"/>
            </td>

            <td>
                <img id="deck" src="Images/back.jpg" alt="deck" width="120" height="180" draggable="false"/>
            </td>

            <td style="height:180px;width:120px;">
                <img id="played_card" title="" src="Images/0.png" alt="card1" ondrop="drop(event)" ondragover="allowDrop(event)" onclick="EndMove()" width="120" height="180" draggable="false"/>
            </td>

            <td rowspan="2" colspan="2" style="vertical-align: bottom; text-align: center; width: 250px;">
                <span style="background-color: #ffd800 ; position: relative; bottom: 0px;">
                        <br><b>tower: </b><b id="opponentTowerVal">num</b></br>
                        <br><b>wall: </b><b id="opponentWallVal">num</b></br>
                </span>
                <img id="opponentTowerImg" src="Images/towe_trans.gif" alt="tower2" width="100" height="250" style="position: relative; bottom: -80px;" draggable="false"/>
                <img id="opponentWallImg" src="Images/wall_trans.gif" alt="wall2" width="250" height="80" style="position: relative;" draggable="false" />
            </td>

            <td rowspan="2" style="background-image:url(Images/resources.png);background-repeat:no-repeat;
                    background-size: 120px 350px;color: #EEEEEE;width:100px;height:280px;padding-left: 20px;
                    vertical-align: top;padding-top: 70px;white-space: nowrap;">
                <b>
                    <br>
                        <b id="opponentGems">num</b>
                        &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp +
                        <b id="opponentMagic">num</b>
                    </br>
                    <br style="line-height: 95px;">
                        <b id="opponentBricks">num</b>
                        &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp +
                        <b id="opponentQuarry">num</b>
                    </br>
                    <br style="line-height: 95px;">
                        <b id="opponentRecruits">num</b>
                        &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp +
                        <b id="opponentDungeon">num</b>
                    </br>
                </b>
            </td>
        </tr>

        <tr>

            <td colspan="2" style="text-align: center;">
                <span style="background-color: #FFA500;">
                    <br>
                        &nbsp
                        <b id="userMessages"></b>
                        &nbsp
                    </br>
                </span>
            </td>
        </tr>

        <tr>
            <td></td>
            <td style="height:180px;width:120px;">
                <img class="card_hand" id="card1_id" title="" src="Images/back.jpg" alt="card1" draggable="true" ondragstart="drag(event)" onclick="StartMove(this.id)" width="120" height="180" />
            </td>
            <td style="height:180px;width:120px;">
                <img class="card_hand" id="card2_id" title="" src="Images/back.jpg" alt="card2" draggable="true" ondragstart="drag(event)" onclick="StartMove(this.id)" width="120" height="180" />
            </td>
            <td style="height:180px;width:120px;">
                <img class="card_hand" id="card3_id" title="" src="Images/back.jpg" alt="card3" draggable="true" ondragstart="drag(event)" onclick="StartMove(this.id)" width="120" height="180" />
            </td>
            <td style="height:180px;width:120px;">
                <img class="card_hand" id="card4_id" title="" src="Images/back.jpg" alt="card4" draggable="true" ondragstart="drag(event)" onclick="StartMove(this.id)" width="120" height="180" />
            </td>
            <td style="height:180px;width:120px;">
                <img class="card_hand" id="card5_id" title="" src="Images/back.jpg" alt="card5" draggable="true" ondragstart="drag(event)" onclick="StartMove(this.id)" width="120" height="180" />
            </td>
            <td style="height:180px;width:120px;">
                <img class="card_hand" id="card6_id" title="" src="Images/back.jpg" alt="card6" draggable="true" ondragstart="drag(event)" onclick="StartMove(this.id)" width="120" height="180" />
            </td>

            <td>
                <span>
                    <input type="button" id="surrender" value="Surrender" style="color: #f00; font-size: 16px;" onclick="PerformAction('surrender')" />
                </span>
            </td>
        </tr>
        <tr>
            <td></td>
            <td colspan="6">
                <form id="manual_input">
                    <input type="text" placeholder="card1 or card2 or card3 etc... or surrender" id="input_text" name="input_text" style="width: 89%;" />
                    <input type="button" id="input_button" value="Execute!" onclick="PerformAction(this.form.input_text.value + '_id')" />
                </form>
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
