<?php
    function verifyCookie() {
        if ( empty($_COOKIE['ArcomageCookie']) ) {
            echo "<script type='text/javascript'>
                window.alert('You are not logged in');
                window.location.href='Login.html';
                </script>";
            return FALSE;
        }
	    list( $username, $expiration, $hmac ) = explode( '|', $_COOKIE['ArcomageCookie'] );
//	    $expired = $expiration;

//	    if ( $expired < time() ) {
        if ( $expiration < time() ) {
            echo "<script type='text/javascript'>
                window.alert('Your session has expired, please log in again');
                window.location.href='Login.html';
                </script>";
            return FALSE;
	    }

	    $key = hash_hmac( 'md5', $username . $expiration, 'TalRan' );
	    $hash = hash_hmac( 'md5', $username . $expiration, $key );

	    if ( $hmac != $hash ) {
            echo "<script type='text/javascript'>
                window.alert('Suspicious Activity, access denied');
                window.location.href='Login.html';
                </script>";
            return FALSE;
	    }
        
        return TRUE;
    }
?>