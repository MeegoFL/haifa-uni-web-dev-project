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
            ev.preventDefault();
        }

        function drag(ev) {
            ev.dataTransfer.setData("ElementID", ev.target.id);
            ev.dataTransfer.setData("CardID", ev.target.alt);
        }

        function drop(ev) {
            ev.preventDefault();
            var elmID = ev.dataTransfer.getData("ElementID");
            var cardID = ev.dataTransfer.getData("CardID");
            ev.target.appendChild(document.getElementById(elmID));
            PerformAction(cardID);
        }

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
                    document.getElementById("myTowerVal").innerHTML = userGameStat['tower'];
                    document.getElementById("myWallVal").innerHTML = userGameStat['wall'];
                    document.getElementById("myMagic").innerHTML = userGameStat['magic'];
                    document.getElementById("myGems").innerHTML = userGameStat['gems'];
                    document.getElementById("myQuarry").innerHTML = userGameStat['quarry'];
                    document.getElementById("myBricks").innerHTML = userGameStat['bricks'];
                    document.getElementById("myDungeon").innerHTML = userGameStat['dungeon'];
                    document.getElementById("myRecruits").innerHTML = userGameStat['recruits'];
                    document.getElementById("myCard1").src = "Images/" + userGameStat['card1_id'] + ".png";
                    document.getElementById("myCard2").src = "Images/" + userGameStat['card2_id'] + ".png";
                    document.getElementById("myCard3").src = "Images/" + userGameStat['card3_id'] + ".png";
                    document.getElementById("myCard4").src = "Images/" + userGameStat['card4_id'] + ".png";
                    document.getElementById("myCard5").src = "Images/" + userGameStat['card5_id'] + ".png";
                    document.getElementById("myCard6").src = "Images/" + userGameStat['card6_id'] + ".png";
                    document.getElementById("myCard1").title = userGameStat['card1_id'];
                    document.getElementById("myCard2").title = userGameStat['card2_id'];
                    document.getElementById("myCard3").title = userGameStat['card3_id'];
                    document.getElementById("myCard4").title = userGameStat['card4_id'];
                    document.getElementById("myCard5").title = userGameStat['card5_id'];
                    document.getElementById("myCard6").title = userGameStat['card6_id'];
                    if (userGameStat['current_flag'] == 1) {
                        document.getElementById("userMessages").innerHTML = "YOUR TURN!";
                    }
                    else {
                        document.getElementById("userMessages").innerHTML = opponentGameStat['nickname'] + "'S TURN";
                    }

                    // Update opponent's game stat on screen
                    document.getElementById("opponentTowerVal").innerHTML = opponentGameStat['tower'];
                    document.getElementById("opponentWallVal").innerHTML = opponentGameStat['wall'];
                    document.getElementById("opponentMagic").innerHTML = opponentGameStat['magic'];
                    document.getElementById("opponentGems").innerHTML = opponentGameStat['gems'];
                    document.getElementById("opponentQuarry").innerHTML = opponentGameStat['quarry'];
                    document.getElementById("opponentBricks").innerHTML = opponentGameStat['bricks'];
                    document.getElementById("opponentDungeon").innerHTML = opponentGameStat['dungeon'];
                    document.getElementById("opponentRecruits").innerHTML = opponentGameStat['recruits'];

                    setTimeout('RefreshView()', 3000);
                }
            }

            // Ready the values and POST the request
            xmlhttp.open("POST", "RefreshGamePage.php", true);
            xmlhttp.send();
        }

        function PerformAction(cardID) {
            // Init

            // We assume we work on: IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp = new XMLHttpRequest();

            // Wait for response
            xmlhttp.onreadystatechange = function () {
                if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                    response = xmlhttp.responseText;
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

<body style="background-image: url(Images/game-background.jpg);background-repeat:no-repeat;background-size:cover;">
    <audio autoplay="" loop="" controls="">
        <source src="Media/GOT_Soundtrack.ogg" type="audio/ogg">
        <source src="Media/GOT_Soundtrack.mp3" type="audio/mpeg">
        Your browser does not support the audio element.
    </audio>
    <table style="border: 0;border-spacing: 10px; padding: 12px;margin-left: auto;margin-right: auto;">
        <tr>
            <td colspan="8" style="background-color:#FFA500;text-align: center;">
                <h1>Arcomage</h1>
            </td>
        </tr>


        <tr>
            <td rowspan="2" style="background-image:url(Images/resources.png);background-repeat:no-repeat;
                    background-size: 120px 350px;color: #EEEEEE;width:100px;height:280px;padding-left: 20px;
                    vertical-align: top;padding-top: 70px;white-space: nowrap;">
                <b>
                    <br><b id = "myGems">num</b> &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp +<b id = "myMagic">num</b></br>
                    <br style="line-height: 95px;"><b id = "myBricks">num</b> &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp +<b id = "myQuarry">num</b></br>
                    <br style="line-height: 95px;"><b id = "myRecruits">num</b> &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp +<b id = "myDungeon">num</b></br>
                </b>
            </td>

            <td rowspan="2" colspan="2" style="vertical-align: bottom; text-align: center;">
                <span style="background-color: #ffd800; position: relative; bottom: 0px;">
                    <b>
                        <br>&nbsp tower: <b id="myTowerVal">num</b>&nbsp</br>
                        <br>&nbsp wall: <b id="myWallVal">num</b>&nbsp</br>
                    </b>
                </span>
                <img id="tower1" src="Images/towe_trans.gif" alt="tower1" width="100" height="200" style="position: relative; bottom: -80px;" />
                <img id="wall1" src="Images/wall_trans.gif" alt="wall1" width="250" height="80" style="position: relative;" />
            </td>

            <td>
                <img id="deck" src="Images/back.jpg" alt="deck" width="120" height="180" />
            </td>

            <td ondrop="drop(event)" ondragover="allowDrop(event)" style="height:180px;width:120px;"></td>

            <td rowspan="2" colspan="2" style="vertical-align: bottom; text-align: center;">
                <span style="background-color: #ffd800 ; position: relative; bottom: 0px;">
                    <b>
                        <br>&nbsp tower: <b id="opponentTowerVal">num</b>&nbsp</br>
                        <br>&nbsp wall: <b id="opponentWallVal">num</b>&nbsp</br>
                    </b>
                </span>
                <img id="tower2" src="Images/towe_trans.gif" alt="tower2" width="100" height="200" style="position: relative; bottom: -80px;" />
                <img id="wall2" src="Images/wall_trans.gif" alt="wall2" width="250" height="80" style="position: relative;" />
            </td>

            <td rowspan="2" style="background-image:url(Images/resources.png);background-repeat:no-repeat;
                    background-size: 120px 350px;color: #EEEEEE;width:100px;height:280px;padding-left: 20px;
                    vertical-align: top;padding-top: 70px;white-space: nowrap;">
                <b>
                    <br><b id = "opponentGems">num</b> &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp +<b id = "opponentMagic">num</b></br>
                    <br style="line-height: 95px;"><b id = "opponentBricks">num</b> &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp +<b id = "opponentQuarry">num</b></br>
                    <br style="line-height: 95px;"><b id = "opponentRecruits">num</b> &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp +<b id = "opponentDungeon">num</b></br>
                </b>
            </td>
        </tr>

        <tr>

            <td colspan="2" style="text-align: center;">
                <span style="background-color: #FFA500;">
                    <br>&nbsp <b id="userMessages"></b>&nbsp</br>
                </span>
            </td>
        </tr>

        <tr>
            <td></td>
            <td style="height:180px;width:120px;">
                <img id="myCard1" title="" src="Images/back.jpg" alt="card1" draggable="true" ondragstart="drag(event)" width="120" height="180" />
            </td>
            <td style="height:180px;width:120px;">
                <img id="myCard2" title="" src="Images/back.jpg" alt="card2" draggable="true" ondragstart="drag(event)" width="120" height="180" />
            </td>
            <td style="height:180px;width:120px;">
                <img id="myCard3" title="" src="Images/back.jpg" alt="card3" draggable="true" ondragstart="drag(event)" width="120" height="180" />
            </td>
            <td style="height:180px;width:120px;">
                <img id="myCard4" title="" src="Images/back.jpg" alt="card4" draggable="true" ondragstart="drag(event)" width="120" height="180" />
            </td>
            <td style="height:180px;width:120px;">
                <img id="myCard5" title="" src="Images/back.jpg" alt="card5" draggable="true" ondragstart="drag(event)" width="120" height="180" />
            </td>
            <td style="height:180px;width:120px;">
                <img id="myCard6" title="" src="Images/back.jpg" alt="card6" draggable="true" ondragstart="drag(event)" width="120" height="180" />
            </td>

            <td>
                <span style="background-color: #FFA500;">
                    <b id="test">&nbsp Surrender &nbsp</b>
                </span>
            </td>
        </tr>
        <tr>
            <td></td>
            <td colspan="6" style="background-color:#FFA500;text-align:center;">
                <b>chat</b>
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
