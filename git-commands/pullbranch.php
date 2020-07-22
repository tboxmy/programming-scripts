<h1>Manage git from web browser.</h1></br>

<?php
// $output = shell_exec('git pull origin');
// This script requires, user access to git without having to key in passwords
// For Laravel 7
$output = shell_exec('cd ../.. ; git pull origin');
echo "<pre>$output</pre>";
?>
<a href="/git-commands">Main menu</a>

