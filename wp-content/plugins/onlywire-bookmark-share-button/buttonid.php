<?php
include ("postrequest.php");
// this file takes care of posting the "form" data to onlywire to get the button id

$url = "http://www.onlywire.com/thebuttonform.php";
$a = PostRequest($url,"", $_GET);

echo $a[1];
?>
