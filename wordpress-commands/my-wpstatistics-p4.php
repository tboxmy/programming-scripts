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
$html ="<h1>Pages Statistics</h1>";
$html .= "<a href=/cmd>home</a><br/>";
echo $html;

showTable($link);
// Close connection
mysqli_close($link);

function showTable($link) {
// Select query execution
$sql1 = "SELECT * FROM `wp_statistics_pages` where type is not null AND type <> '' ORDER BY uri";

$sql_uri = "SELECT uri FROM `wp_statistics_pages` WHERE type is not null AND type <> '' AND date > DATE(NOW()) - INTERVAL 7 DAY GROUP BY uri";
$sql_dates = "SELECT date(date) FROM `wp_statistics_pages` WHERE type is not null AND type <> '' AND date > DATE(NOW()) - INTERVAL 7 DAY GROUP BY date";
// $sql_locations = "SELECT location FROM `wp_statistics_visitor` WHERE last_counter > DATE(NOW()) - INTERVAL 7 DAY GROUP BY location";
// $sql_dates = "SELECT date(last_counter) FROM `wp_statistics_visitor` WHERE last_counter > DATE(NOW()) - INTERVAL 7 DAY GROUP BY last_counter";
$sql2 = "SELECT uri, date, count amount FROM `wp_statistics_pages` WHERE type is not null AND type <> '' AND date > DATE(NOW()) - INTERVAL 7 DAY GROUP BY uri,date";
/** 
 Load list of locations
*/
$sql = $sql_uri;
$locations = array();
if($result = mysqli_query($link, $sql)){
    if(mysqli_num_rows($result) > 0){
        while($row = mysqli_fetch_array($result)){
            $locations[] = $row[0];
        }
    }
    echo "<br/>Summary(Last 7 days)<br/>";
} else {
    echo "No records matching your query were found.";
    exit (1);
}

/**
 Load list of dates
*/
$sql = $sql_dates;
$last_dates = array();
if($result = mysqli_query($link, $sql)){
    if(mysqli_num_rows($result) > 0){
        while($row = mysqli_fetch_array($result)){
            $last_dates[] = $row[0];
        }
    }
} 
/**
 Process data into array
*/
$sql = $sql2;
$statistics = array();
if($result = mysqli_query($link, $sql)){
    if(mysqli_num_rows($result) > 0){
        while($row = mysqli_fetch_array($result)){
            $statistics[$row['uri']][$row['date']] = $row['amount'];
        }
    }
} 

print ("<table border=1>");
print ("<tr>");
print ("<th>URL</th>");
foreach($last_dates as $row){
    echo "<th>".$row."</th>";
}
print ("</tr>");
$count_dates = array();
foreach($locations as $row){
    print("<tr><td>".$row."</td>");
    foreach($last_dates as $dates){
       print("<td>");
       if(  isset($statistics[$row][$dates]) ){
           print( $statistics[$row][$dates] );
           if(isset($count_dates[$dates]) ){
               $count_dates[$dates] += $statistics[$row][$dates];
           } else {
               $count_dates[$dates] = $statistics[$row][$dates];
           }
       }
       print("</td>");
    }
    print("</tr>");
}
    print("<tr><td>TOTAL:</td>");
/*
  Total to display based on date keys
*/
    foreach($last_dates as $dates){
       print("<td>".$count_dates[$dates]."</td>");
    }
    print("</tr>");
print ("</table>");

}

?>
