<?php
include ("postrequest.php");
//$site = file_get_contents('http://onlywire.com/thebuttonwp');
//echo $site;

$site = GetRequest('http://onlywire.com/thebuttonwp');
echo $site[1];
?>
