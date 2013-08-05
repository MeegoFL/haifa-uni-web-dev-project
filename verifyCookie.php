<?php
function verifyCookie() {
    if ( empty($_COOKIE['ArcomageCookie']) ) {
        echo "<script type='text/javascript'>
                window.alert('You are not logged in');
                window.location.href='index.html';
                </script>";
        return FALSE;
    }
    list( $nickname, $expiration, $hmac ) = explode( '|', $_COOKIE['ArcomageCookie'] );
    //	    $expired = $expiration;

    //	    if ( $expired < time() ) {
    if ( $expiration < time() ) {
        echo "<script type='text/javascript'>
                window.alert('Your cookie has expired, please log in again');
                window.location.href='index.html';
                </script>";
        return FALSE;
    }

    $key = hash_hmac( 'md5', $nickname . $expiration, 'TalRan' );
    $hash = hash_hmac( 'md5', $nickname . $expiration, $key );

    if ( $hmac != $hash ) {
        echo "<script type='text/javascript'>
                window.alert('Suspicious Activity, access denied');
                window.location.href='index.html';
                </script>";
        return FALSE;
    }

    // Connect to Database to keep the last active time
    $db_ini = parse_ini_file('Arcomage.ini');
    $mysqli = new mysqli($db_ini['host'], $db_ini['username'], $db_ini['password'], $db_ini['db']);
    if ($mysqli->connect_errno) echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;

    $lastactive = $_SERVER['REQUEST_TIME'];

    //$sql = "SELECT FROM users '"lastactive"' WHERE username = '".$nickname."'";
    //$result = mysqli_query($con,$sql);
    //$result->data_seek(0);
    //$row = $result->fetch_assoc();
    //if ($row - time() < -1000) {
    //return FALSE;


    $stmt = $mysqli->prepare("UPDATE users SET last_active = ? WHERE nickname = ?;");
    $stmt->bind_param('is', $lastactive, $nickname);
    $stmt->execute();
    $stmt->close();

    $mysqli->close();
    return TRUE;
}
?>