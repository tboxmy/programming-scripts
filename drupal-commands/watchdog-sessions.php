<?php
/*
  Purpose: Scripts to list session information from Watchdog module in Drupal 7, 8
  Author: Nicholas A S
  Created date: 20 July 2020
  
*/
/*
  Configure access to MySQL 
  Format
  $link = mysqli_connect(HOSTNAME, DATABASE_USERNAME, USERNAME_PASSWORD, DATABASE_NAME);
*/
$link = mysqli_connect("localhost", "root", "password", "database_name");
 
// Check connection
if($link === false){
    die("ERROR: Could not connect. " . mysqli_connect_error());
}
$html ="<h1>Session Log</h1>";
$html .= "<a href=/drupal-commands>home</a><br/>";
echo $html;
// Select query execution
$sql1 = "SELECT * FROM `watchdog` where message like \"% opened%\"";
$sql2 = "SELECT * FROM `watchdog` INNER JOIN `users_field_data` ON `watchdog`.uid = `users_field_data`.uid where message like \"% opened%\"";
 $sql3 = "SELECT * FROM `watchdog` INNER JOIN `users_field_data` ON `watchdog`.uid = `users_field_data`.uid where message like \"% opened%\" ORDER BY `users_field_data`.name ASC";
$sql = $sql2;
if($result = mysqli_query($link, $sql)){
    if(mysqli_num_rows($result) > 0){
        echo "<table>";
        echo "<tr>";
        echo "<th>uid</th>";
        echo "<th>Message</th>";
        echo "<th>Name</th>";
        echo "<th>Hostname</th>";
        echo "<th>date</th>";
        echo "</tr>";
        while($row = mysqli_fetch_array($result)){
            echo "<tr>";
            echo "<td>" . $row['uid'] . "</td>";
            $someArray = $row['variables'];
            preg_match('#\{(.*?)\}#', $someArray, $match);
            $some1 = explode(";",$match[1]);
            preg_match('#\"(.*?)\"#', $some1[1], $fullname);
            $newMessage = str_replace("%name", $fullname[1], $row['message']);
            echo "<td>" . $newMessage . "</td>";
            $newTimestamp = date('d/m/Y H:i:s', strtotime('+8 hours', $row['timestamp']));
            echo "<td>" . $row['name'] . "</td>";
            echo "<td>" . $row['hostname'] . "</td>";
            echo "<td>" . $newTimestamp . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        mysqli_free_result($result);
    } else{
        echo "No records matching your query were found.";
    }
} else{
    echo "ERROR: Could not execute $sql. " . mysqli_error($link);
}

// Close connection
mysqli_close($link);
?>
