<?php
 // Get the values and check for XSS or SQL injection
 preg_match('/^[a-zA-Z0-9]+$/',$_REQUEST["username"]) ? $username = $_REQUEST["username"] : exit('XSS is detected!'); //Check why $_POST didn't work
 preg_match('/^[a-zA-Z0-9]+$/',$_REQUEST["password"]) ? $password = ($_REQUEST["password"]) : exit('XSS is detected!');
 preg_match('/^[a-zA-Z0-9]+$/',$_REQUEST["nickname"]) ? $nickname = $_REQUEST["nickname"] : exit('XSS is detected!');

 // Check password strength
 if( strlen($password) < 6 ) { $error .= "Password too short!<br>";}
 if( !preg_match("#[0-9]+#", $password) ) {$error .= "Password must include at least one number!<br>";}
 if( !preg_match("#[a-z]+#", $password) ) {$error .= "Password must include at least one letter!<br>";}
 if( !preg_match("#[A-Z]+#", $password) ) {$error .= "Password must include at least one CAPS!<br>";}

 // Exit on weak password or update password to md5 if strong enough
 if($error) exit("<br><b style=\"color:red\">Weak Password:<br>$error</b>");
 else $password = md5($password);
 
 // Connect to Database
 $con = mysqli_connect('localhost','root','12345','test');
 if (!$con)
 {
     die('Could not connect: ' . mysqli_error($con));
 }

 // Check if username already exist
 $sql = "SELECT * FROM users WHERE username = '".$username."'";
 $result = mysqli_query($con,$sql);
 
 // If username exist return error
 if(mysqli_num_rows($result) != 0) 
 {
     die("<p style=\"color:red\">Username already exist, please choose another</p>");
 } 
 
 // Check if nickname already exist
 $sql = "SELECT * FROM users WHERE nickname = '".$nickname."'";
 $result = mysqli_query($con,$sql);
 
 // If nickname exist return error
 if(mysqli_num_rows($result) > 0) 
 {
     die("<p style=\"color:red\">Nickname already in use, please choose another</p>");
 } 
 
 // If we got here insert the user details to Database
 else 
 {
     $sql = "INSERT INTO users (username, password, nickname) VALUES ('".$username."','".$password."','".$nickname."')";
     $result = mysqli_query($con,$sql);

     echo "<p style=\"color:green\">User added successfully</p>";
 }

 // Close the connection
 mysqli_close($con);
 ?>