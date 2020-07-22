<h1>Manage git from web browser.</h1></br>

<?php
// $output = shell_exec('git branch -a');
// For Laravel 7
$output = shell_exec('cd ../.. ; git branch -a');
echo "<pre>$output</pre>";
?>
<a href="/git-commands">Main menu</a>
