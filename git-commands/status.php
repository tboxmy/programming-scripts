<h1>Manage git from web browser.</h1></br>
<?php

// $output = shell_exec('git status');
// For Laravel
$output = shell_exec('cd ../.. ; git status');

echo "<pre>$output</pre>";
?>
<a href="/git-commands">Main menu</a>

