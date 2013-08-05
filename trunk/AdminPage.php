<?php
include 'verifyCookie.php';
session_start();
if (verifyCookie() && ($_SESSION['nickname'] == "admin")) {
?>

<!DOCTYPE html>

<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Admin Page</title>
    <style type="text/css">
        body {background-image: url('Images/Arcomage_title.jpg'); background-size: 100%;background-repeat: no-repeat; opacity: 0.9;}
    </style>
    <script>

        function RefreshGames() {
            // Initialize missingvalue flag and clean error messages
            document.getElementById("gameslist").innerHTML = "";
            // We assume we work on: IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp = new XMLHttpRequest();
            // Wait for response
            xmlhttp.onreadystatechange = function () {
                if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                    response = xmlhttp.responseText;
                    if (response == "EMPTY") document.getElementById("gameslist").innerHTML = "No Live Games";
                    else gamesList = JSON.parse(response);
                    
                    for (var i = 0; i < gamesList.length; i++) 
                    {
                            document.getElementById("gameslist").innerHTML += "Game id-><b>" + gamesList[i][0] + "</b>: <b style = \"color: #0026ff;\">" + gamesList[i][1] + "</b> VS. <b style = \"color: #f00;\">" + gamesList[i][2] + "</b><br>";
                    }
                    setTimeout('RefreshGames()', 3000);
                }
            }
            // Ready the values and POST the request
            xmlhttp.open("POST", "GetActiveGames.php", true);
            xmlhttp.send();
        }
        window.onload = function () {
            RefreshGames();
        }
    </script>
</head>
<body>
    <h1 style="margin-bottom:0;text-align: center;color: #ffd800; font-size: 50px; text-shadow: 2px 2px 2px #333;">Admin Page</h1>
    <hr />
    <table border="1" style="width:800px; margin: auto; background-color: #fff">
        <tr>
            <td>
                <h1 style="text-align: center;">Online Games</h1>
            </td>

            <td>
                <h1 style="text-align: center;">Chat (N/A)</h1>
            </td>
            <tr>
                <td style="width: 50%;">
                    <div id="gameslist" style="height: 500px; overflow-y: scroll; overflow-x: hidden;"></div>
                </td>
                <td>
                    <div id="chatbox" style="height: 500px; overflow-y: scroll; overflow-x: hidden;"></div>
                </td>
            </tr>
        </tr>
    </table>
    <p style="text-align: center; color: #f00;text-shadow: 1px 1px 1px #333;">Copyright © TalRan</p>
</body>
</html>

<?php
} else {
        echo "<script type='text/javascript'>
                window.alert('You are not Administrator');
                window.location.href='index.html';
                </script>";
}
?>
