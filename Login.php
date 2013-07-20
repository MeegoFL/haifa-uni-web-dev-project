<?php
 $username=$_GET["username"];
 $password=$_GET["password"];

<script type="text/javascript">
window.alert("You message goes here!")
</script>

 $con = mysqli_connect('localhost','root','12345','test');
 if (!$con)
   {
   die('Could not connect: ' . mysqli_error($con));
   }

 mysqli_select_db($con,"test");
 $sql="SELECT * FROM user WHERE id = '".$q."'";

 $result = mysqli_query($con,$sql);

 echo "<table border='1'>
 <tr>
 <th>Firstname</th>
 <th>Lastname</th>
 <th>Age</th>
 <th>Hometown</th>
 <th>Job</th>
 </tr>";

 while($row = mysql_fetch_array($result))
   {
   echo "<tr>";
   echo "<td>" . $row['FirstName'] . "</td>";
   echo "<td>" . $row['LastName'] . "</td>";
   echo "<td>" . $row['Age'] . "</td>";
   echo "<td>" . $row['Hometown'] . "</td>";
   echo "<td>" . $row['Job'] . "</td>";
   echo "</tr>";
   }
 echo "</table>";

 mysqli_close($con);
 ?>