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
        $con = mysqli_connect('localhost','root','12345','test');
        if (!$con)
        {
            die('Could not connect: ' . mysqli_error($con));
        }
        
        $lastactive = $_SERVER['REQUEST_TIME'];

        //$sql = "SELECT FROM users '"lastactive"' WHERE username = '".$nickname."'";
        //$result = mysqli_query($con,$sql);
        //$result->data_seek(0);
        //$row = $result->fetch_assoc();
        //if ($row - time() < -1000) {
           //return FALSE;
        
        
        $sql = "UPDATE users SET lastactive = '".$lastactive."' WHERE nickname = '".$nickname."'";
        $result = mysqli_query($con,$sql);
        
        return TRUE;
    }
?>