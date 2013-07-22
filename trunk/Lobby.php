<?php
    include 'verifyCookie.php';
    if( verifyCookie() ) {
?>

<!DOCTYPE html>

<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title>Game Lobby</title>
    </head>
    <body>
        <b>test succeeded</b>
    </body>
</html>

<?php
    }
    else {
   //    header('Location: Login.html');
    }
    ?>
