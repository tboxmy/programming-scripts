<?php
/*
  Purpose: Scripts to visitor session information from WP Statistics in Wordpress 5
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
$html ="<h1>Visitors Log</h1>";
$html .= "<a href=/cmd>home</a><br/>";
echo $html;
// Select query execution
$sql1 = "SELECT * FROM `wp_statistics_visitor` ";
$sql = $sql1;
if($result = mysqli_query($link, $sql)){
    if(mysqli_num_rows($result) > 0){
        echo "<table>";
        echo "<tr>";
        echo "<th>ID</th>";
        echo "<th>Location</th>";
        echo "<th>IP</th>";
        echo "<th>Date</th>";
        echo "<th>Referrer</th>";
        echo "</tr>";
        $file = fopen('export-p1.csv', 'w');
        fputcsv($file, array('ID', 'Location', 'IP', 'Date' , 'Referrer'));
        while($row = mysqli_fetch_array($result)){
            echo "<tr>";
            echo "<td>" . $row['ID'] . "</td>";
            echo "<td>" . $row['location'] . "</td>";
            echo "<td>" . $row['ip'] . "</td>";
            echo "<td>" . $row['last_counter'] . "</td>";
            echo "<td>" . $row['referred'] . "</td>";
            echo "</tr>";
            fputcsv($file, array($row['ID'], $row['location'], $row['ip'], $row['last_counter'] , $row['referred']));
        }
        echo "</table>";
        fclose($file);
        mysqli_free_result($result);
    } else{
        echo "No records matching your query were found.";
    }
} else{
    echo "ERROR: Could not execute $sql. " . mysqli_error($link);
}

// Close connection
mysqli_close($link);
$html = "<br/><a href=\"export-p2.csv\">Download CSV</a>";
echo $html;
?>
