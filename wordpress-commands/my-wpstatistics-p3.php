<head>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.6.0/Chart.bundle.js" charset="utf-8"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

</head>
<body>
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

// showTable($link);

// function showTable($link) {
// Select query execution
$sql1 = "SELECT * FROM `wp_statistics_visitor` ";
$sql_locations = "SELECT location FROM `wp_statistics_visitor` WHERE last_counter > DATE(NOW()) - INTERVAL 7 DAY GROUP BY location";
$sql_dates = "SELECT date(last_counter) FROM `wp_statistics_visitor` WHERE last_counter > DATE(NOW()) - INTERVAL 7 DAY GROUP BY last_counter";
$sql3 = "SELECT location, last_counter, count(location) amount FROM `wp_statistics_visitor` GROUP BY location,last_counter";
$sql2 = "SELECT location, last_counter, count(location) amount FROM `wp_statistics_visitor` WHERE last_counter > DATE(NOW()) - INTERVAL 7 DAY GROUP BY location,last_counter";
/** 
 Load list of locations
*/
$sql = $sql_locations;
$locations = array();
if($result = mysqli_query($link, $sql)){
    if(mysqli_num_rows($result) > 0){
        while($row = mysqli_fetch_array($result)){
            $locations[] = $row[0];
        }
    }
    echo "<br/>Summary(last 7 days)<br/>";
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
            $statistics[$row['location']][$row['last_counter']] = $row['amount'];
        }
    }
} 

print ("<table border=1>");
print ("<tr>");
print ("<th>Country</th>");
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
// print("<td>"); print_r($count_dates); print("</td>");
    foreach($count_dates as $count_date){
       print("<td>".$count_date."</td>");
    }
    print("</tr>");
print ("</table>");

?>
<div id="chart-container">
    <canvas id="graphCanvas"></canvas>
</div>

<script>
var ctx = document.getElementById('graphCanvas').getContext('2d');
var myChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: [<?php 
    $start = "n";
    foreach($last_dates as $dates){
       if($start == "y") 
       print(", ");
       else $start="y";
       print("'".$dates."'");
       
    }
    ?>],
        datasets: [{
            label: '# of Visitors',
            data: [12, 19, 3, 5, 2, 3],
            data: [<?php
            $start = "n";
            foreach($count_dates as $count_date){
                if($start == "y") 
                print(", ");
                else $start="y";
                print($count_date);
            }
            ?>],
            backgroundColor: [
                'rgba(255, 99, 132, 0.2)',
                'rgba(54, 162, 235, 0.2)',
                'rgba(255, 206, 86, 0.2)',
                'rgba(75, 192, 192, 0.2)',
                'rgba(153, 102, 255, 0.2)',
                'rgba(255, 159, 64, 0.2)'
            ],
            borderColor: [
                'rgba(255, 99, 132, 1)',
                'rgba(54, 162, 235, 1)',
                'rgba(255, 206, 86, 1)',
                'rgba(75, 192, 192, 1)',
                'rgba(153, 102, 255, 1)',
                'rgba(255, 159, 64, 1)'
            ],
            borderWidth: 1
        }]
    },
    options: {
        scales: {
            yAxes: [{
                ticks: {
                    beginAtZero: true
                }
            }]
        }
    }
});
</script>
</body>
<?php
// Close connection
mysqli_close($link);
?>
