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
        body {background-image: url('Acromage_title.jpg'); background-size: 100%;background-repeat: no-repeat; opacity: 0.9;}
        </style>
        <script>
            function SubmitChatText(input) {
                document.getElementById("chatbox").innerHTML += input + "<br>";
                document.getElementById("chatfield").value = "";
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
            <td style="height: 500px; width:50%; overflow-y: scroll; overflow-x: hidden;"></td>
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
