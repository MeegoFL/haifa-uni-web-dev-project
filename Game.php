<?php
include 'verifyCookie.php';
if( verifyCookie() ) {
?>

<!DOCTYPE html>

<html lang="en">
<head>
    <meta charset="utf-8" />
    <title></title>
    <script>
            function allowDrop(ev) {
                ev.preventDefault();
            }

            function drag(ev) {
                ev.dataTransfer.setData("Text", ev.target.id);
            }

            function drop(ev) {
                ev.preventDefault();
                var data = ev.dataTransfer.getData("Text");
                ev.target.appendChild(document.getElementById(data));
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
                        document.getElementById("myTowerVal").innerHTML = userGameStat['tower'];
                        document.getElementById("myWallVal").innerHTML = userGameStat['wall'];
                        document.getElementById("myMagic").innerHTML = userGameStat['magic'];
                        document.getElementById("myGems").innerHTML = userGameStat['gems'];
                        document.getElementById("myQuarry").innerHTML = userGameStat['quarry'];
                        document.getElementById("myBricks").innerHTML = userGameStat['bricks'];
                        document.getElementById("MyDungeon").innerHTML = userGameStat['dungeon'];
                        document.getElementById("MyRecruits").innerHTML = userGameStat['recruits'];
                        //document.getElementById("myCard1").innerHTML = userGameStat['card1_id'];
                        //document.getElementById("myCard2").innerHTML = userGameStat['card2_id'];
                        //document.getElementById("myCard3").innerHTML = userGameStat['card3_id'];
                        //document.getElementById("myCard4").innerHTML = userGameStat['card4_id'];
                        //document.getElementById("myCard5").innerHTML = userGameStat['card5_id'];
                        //document.getElementById("myCard6").innerHTML = userGameStat['card6_id'];
                        if (userGameStat['current_flag'] == 1) {
                            document.getElementById("userMessages").innerHTML = "YOUR TURN!";
                        }
                        else {
                            document.getElementById("userMessages").innerHTML = "OPPONENT'S TURN";
                        }

                        //document.getElementById("myTowerVal").innerHTML = userGameStat['tower'];
                        //document.getElementById("myWallVal").innerHTML = userGameStat['wall'];
                        //document.getElementById("myMagic").innerHTML = userGameStat['magic'];
                        //document.getElementById("myGems").innerHTML = userGameStat['gems'];
                        //document.getElementById("myQuarry").innerHTML = userGameStat['quarry'];
                        //document.getElementById("myBricks").innerHTML = userGameStat['bricks'];
                        //document.getElementById("MyDungeon").innerHTML = userGameStat['dungeon'];
                        //document.getElementById("MyRecruits").innerHTML = userGameStat['recruits'];
                        setTimeout('RefreshView()', 3000);
                    }
                }

                // Ready the values and POST the request
                xmlhttp.open("POST", "RefreshGamePage.php", true);
                xmlhttp.send();
            }

            function PerformAction() {

            }

            window.onload = function () {
                RefreshView();
            }
    </script>
</head>

<body style="background-image: url(Images/game-background.jpg);background-repeat:no-repeat;background-size:cover;">
    <audio autoplay="" loop="" controls="">
        <source src="Media/GOT_Soundtrack.ogg" type="audio/ogg">
            <source src="Media/GOT_Soundtrack.mp3" type="audio/mp3"></source>
        </source>
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
                    <br style="line-height: 95px;"><b id = "MyRecruits">num</b> &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp +<b id = "MyDungeon">num</b></br>
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
                    <br>num &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp +n</br>
                    <br style="line-height: 95px;">num &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp +n</br>
                    <br style="line-height: 95px;">num &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp +n</br>
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
                <img id="drag1" src="Images/back.jpg" alt="card1" draggable="true" ondragstart="drag(event)" width="120" height="180" />
            </td>
            <td style="height:180px;width:120px;">
                <img id="drag2" src="Images/back.jpg" alt="card2" draggable="true" ondragstart="drag(event)" width="120" height="180" />
            </td>
            <td style="height:180px;width:120px;">
                <img id="drag3" src="Images/back.jpg" alt="card3" draggable="true" ondragstart="drag(event)" width="120" height="180" />
            </td>
            <td style="height:180px;width:120px;">
                <img id="drag4" src="Images/back.jpg" alt="card4" draggable="true" ondragstart="drag(event)" width="120" height="180" />
            </td>
            <td style="height:180px;width:120px;">
                <img id="drag5" src="Images/back.jpg" alt="card5" draggable="true" ondragstart="drag(event)" width="120" height="180" />
            </td>
            <td style="height:180px;width:120px;">
                <img id="drag6" src="Images/back.jpg" alt="card6" draggable="true" ondragstart="drag(event)" width="120" height="180" />
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
