<?php
    include 'verifyCookie.php';
    if( verifyCookie() ) {
?>

<!DOCTYPE html>

<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title>Game Lobby</title>
        <script>
            function SubmitChatText(input) {
                document.getElementById("chatbox").innerHTML += input + "<br>";
                document.getElementById("chatfield").value = "";
            }
        </script>
    </head>
<body>
    <div id="container" style="width:500px">
        <div id="header" style="background-color:#FFA500;">
            <h1 style="margin-bottom:0;text-align: center;">Welcome to Arcomage Game!</h1></div>
        
        <div id="userlist" style="background-color:#FFD700;height:50%;width:50%;float:left;">
        <h1 style="text-align: center;">Online Users</h1>
            <div id="userlist" style="background-color: #EEEEEE;height: 50%;width:50%;"></div>
            <br>
            <br>
        </div>
        
        <div id="chat" style="background-color:#EEEEEE;height:50%;width:50%;float:left;">
            <h1 style="text-align: center;">Chat</h1>
            <div id="chatbox" style="background-color:#EEEEEE;height:100px;width:100%;overflow-y: scroll;overflow-x: hidden;"></div>
            <form><input type="text" id="chatfield">  <input type="button" value="enter" onclick="SubmitChatText(this.form.chatfield.value)"></form>
        </div>
        
        <div id="footer" style="background-color:#FFA500;clear:both;text-align:center;">Copyright © TalRan</div>
    </div>
</body>
</html>

<?php
    }
    else {
   //    header('Location: Login.html');
    }
    ?>
