<?php

 // Connect to Database
 $mysqli = new mysqli("localhost", "root", "12345", "test");
 if ($mysqli->connect_errno) 
 {
     echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
 }

 // Get list of active users (in last one hour)
 $time = time()-3600;
 $result = $mysqli->query("SELECT * FROM users WHERE lastactive > '".$time."'");

 // If username doesn't exist return error
 if($result->num_rows == 0) 
 {
     echo "No Online Users";
 } 

 while ($row = $result->fetch_array())
 {
     $nickname = $row['nickname'];
     echo "<b>$nickname</b>";
 }

?>
