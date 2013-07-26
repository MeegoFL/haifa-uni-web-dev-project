<?php
    include 'verifyCookie.php';
    if( verifyCookie() ) {
?>

<!DOCTYPE html>

<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title>Game Lobby</title>
        <style type="text/css">
        body {background-image: url('Images/Arcomage_title.jpg'); background-size: 100%;background-repeat: no-repeat; opacity: 0.9;}
        </style>
        <script>
            function getCookie(c_name) {
                var c_value = document.cookie;
                var c_start = c_value.indexOf(" " + c_name + "=");
                if (c_start == -1) {
                    c_start = c_value.indexOf(c_name + "=");
                }

                if (c_start == -1) {
                    c_value = null;
                }

                else {
                    c_start = c_value.indexOf("=", c_start) + 1;
                    var c_end = c_value.indexOf(";", c_start);
                    if (c_end == -1) {
                        c_end = c_value.length;
                    }
                    c_value = unescape(c_value.substring(c_start, c_end));
                }
                return c_value;
            }

            function SubmitChatText(input) {
                var cookie = getCookie("ArcomageCookie");
                var nickName = cookie.substring(0, cookie.indexOf("|"));
                document.getElementById("chatbox").innerHTML += nickName + ": " + input + "<br>";
                document.getElementById("chatfield").value = "";
            }

            function PrintUsersList() {
                // Initialize missingvalue flag and clean error messages
                document.getElementById("userlist").innerHTML = "";

                // We assume we work on: IE7+, Firefox, Chrome, Opera, Safari
                xmlhttp = new XMLHttpRequest();

                // Wait for response
                xmlhttp.onreadystatechange = function () {
                    if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                        response = xmlhttp.responseText;
                        document.getElementById("userlist").innerHTML = response;
                        document.getElementById("userlist").innerHTML += "<input type=\"button\" value=\"Submit\">";
                        setTimeout('PrintUsersList()', 10000);
                    }
                }

                // Ready the values and POST the request
                xmlhttp.open("POST", "GetActiveUsers.php", true);
                xmlhttp.send();
            }
            window.onload = function () {
                setTimeout('PrintUsersList()', 10000);
            }
        </script>
    </head>
<body>
    <h1 style="margin-bottom:0;text-align: center;color: #ffd800; font-size: 50px; text-shadow: 2px 2px 2px #333;">Arcomage Lobby</h1><hr>  
    <table border="1" style="width:1000px; margin: auto; background-color: #fff">
        <tr>
            <td>
                <h1 style="text-align: center;">Online Users</h1>
            </td>
            
            <td>
            <h1 style="text-align: center;">Chat</h1>
            </td>
        <tr>
            <td>
                <div id="userlist" style="height: 500px; overflow-y: scroll; overflow-x: hidden;"></div>
                <input type="button" value="refresh" onclick="PrintUsersList()">
            </td>
            <td>
                <div id="chatbox" style="height: 500px; overflow-y: scroll; overflow-x: hidden;"></div>
                <form><input type="text" id="chatfield" style="width: 85%;"><input type="button" value="enter" style="width: fill-available;" onclick="SubmitChatText(this.form.chatfield.value)"></form>
            </td>
        </tr>
    </table>
    <p style="text-align: center; color: #f00;text-shadow: 1px 1px 1px #333;">Copyright © TalRan</p>
</body>
</html>

<?php
    }
    else {
   //    header('Location: Login.html');
    }
?>