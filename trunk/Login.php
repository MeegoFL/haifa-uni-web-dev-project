<?php
 // Get the values and check for XSS or SQL injection
 preg_match('/^[a-zA-Z0-9]+$/',$_REQUEST["username"]) ? $username = $_REQUEST["username"] : exit('XSS is detected!');
 preg_match('/^[a-zA-Z0-9]+$/',$_REQUEST["password"]) ? $password = md5($_REQUEST["password"]) : exit('XSS is detected!');

 // Connect to Database
 $mysqli = new mysqli("localhost", "root", "12345", "test");
 if ($mysqli->connect_errno) 
 {
     echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
 }

 // Check if username exists
 $result = $mysqli->query("SELECT * FROM users WHERE username = '$username'");
 
 // If username doesn't exist return error
 if($result->num_rows == 0) 
 {
     echo "<p style=\"color:red\">User Name not found please Register</p>";
 } 
 
 // Get user password and compare
 else 
 {
     $result->data_seek(0);
     $row = $result->fetch_assoc();

     // Compare password and output error if wrong
     if ($password != $row['password'])
     {
         echo "<p style=\"color:red\">WRONG PASSWORD</p>";
     }
     else
     {
         // Need to find how to redirect to Lobby.html
         setcookie("ArcomageCookie", $row['username'], $row['password']);
         echo "Location:Lobby.html";
         exit;
     }
 }
 ?>