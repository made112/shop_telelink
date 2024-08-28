<?php
exec("git pull 2>&1", $output);
var_dump($output);
?>
