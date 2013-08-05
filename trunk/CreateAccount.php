<?php
// Get the values and check for XSS or SQL injection
preg_match('/^[a-zA-Z0-9]+$/',$_REQUEST["username"]) ? $username = $_REQUEST["username"] : exit('Attack detected!'); //Check why $_POST didn't work
preg_match('/^[a-zA-Z0-9_!@#$%^&]+$/',$_REQUEST["password"]) ? $password = ($_REQUEST["password"]) : exit('Attack is detected!');
preg_match('/^[a-zA-Z0-9_!@#$%^&]+$/',$_REQUEST["repeat_password"]) ? $repeat_password = ($_REQUEST["repeat_password"]) : exit('Attack is detected!');
preg_match('/^[a-zA-Z0-9]+$/',$_REQUEST["nickname"]) ? $nickname = $_REQUEST["nickname"] : exit('Attack is detected!');

session_start();

// Parse ini file for datbase data
$db_ini = parse_ini_file('Arcomage.ini');

// Check password strength
$error = NULL;
if( strlen($password) < 8 ) {
    $error .= "Password too short!<br>";
}
if( !preg_match("#[0-9]+#", $password) ) {
    $error .= "Password must include at least one number!<br>";
}
if( !preg_match("#[a-z]+#", $password) ) {
    $error .= "Password must include at least one letter!<br>";
}
if( !preg_match("#[A-Z]+#", $password) ) {
    $error .= "Password must include at least one CAPS!<br>";
}
if( !preg_match("#\W+#", $password) ) {
    $error .= "Password must include at least one symbol!";
}


// Exit on weak password repeat password mismatch, else update password to md5 if strong enough
if($error) exit("<br><b style=\"color:red\">Weak Password:<br>$error</b>");
else if( $password != $repeat_password ) {
    $error .= "Password must match Repeat Password!<br>";
    exit("<br><b style=\"color:red\">Repeat Password mismatch:<br>$error</b>");
}
else $password = sha1($db_ini['hash_key'].$password); //Create encrypted password with salt from ini

// Connect to Database
$mysqli = new mysqli($db_ini['host'], $db_ini['username'], $db_ini['password'], $db_ini['db']);
if ($mysqli->connect_errno) echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;

// Check if username already exist
$stmt = $mysqli->prepare("SELECT * FROM users WHERE username = ?");
$stmt->bind_param('s', $username);
$stmt->execute();
$result = $stmt->get_result();

// If username exist return error
if($result->num_rows != 0) die("<p style=\"color:red\">Username already exist, please choose another</p>");

$stmt = $mysqli->prepare("SELECT * FROM users WHERE username = ?");
$stmt->bind_param('s', $nickname);
$stmt->execute();
$result = $stmt->get_result();

// If nickname exist return error
if($result->num_rows != 0) die("<p style=\"color:red\">Nickname already exist, please choose another</p>");

// If we got here insert the user details to Database
else
{
    $stmt = $mysqli->prepare("INSERT INTO users (username, password, nickname) VALUES (?, ?, ?)");
    $stmt->bind_param('sss', $username, $password, $nickname);
    $stmt->execute();
    $stmt->close();

    // set cookie:
	$expiration = time() + 7200; // 2 hours

    // generate cookie:
    $key = hash_hmac( 'md5', $nickname . $expiration, 'TalRan' );
    $hash = hash_hmac( 'md5', $nickname . $expiration, $key );
    $cookie = $nickname . '|' . $expiration . '|' . $hash;

    if ( !setcookie( 'ArcomageCookie', $cookie, $expiration ) ) {
		exit('Error: Unable to set cookie');
	}

    // store session data for later use
    $_SESSION['nickname'] = $nickname;

    echo "SUCCESS";
}

// Close the connection
$mysqli->close();
?>