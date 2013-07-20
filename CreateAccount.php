<?php
 // Get the values and check for XSS or SQL injection
 preg_match('/^[a-zA-Z0-9]+$/',$_REQUEST["username"]) ? $username = $_REQUEST["username"] : exit('XSS is detected!');
 preg_match('/^[a-zA-Z0-9]+$/',$_REQUEST["password"]) ? $password = md5($_REQUEST["password"]) : exit('XSS is detected!');
 preg_match('/^[a-zA-Z0-9]+$/',$_REQUEST["nickname"]) ? $nickname = $_REQUEST["nickname"] : exit('XSS is detected!');

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
 if(mysqli_num_rows($result) != 0) 
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