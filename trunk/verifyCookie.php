<?php
    if ( empty($_COOKIE['ArcomageCookie']) ) {
        // redirect to login
    }
	list( $username, $expiration, $hmac ) = explode( '|', $_COOKIE['ArcomageCookie'] );
	$expired = $expiration;

	if ( $expired < time() ) {
	    // Report to user: session expired, please log in again
        // redirect to login
	}

	$key = hash_hmac( 'md5', $username . $expiration, 'TalRan' );
	$hash = hash_hmac( 'md5', $username . $expiration, $key );

	if ( $hmac != $hash ) {
	    // Report to user: Suspicious Activity, access denied.
	}

?>