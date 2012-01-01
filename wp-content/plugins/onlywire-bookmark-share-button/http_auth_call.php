<?php
include ("postrequest.php");
// this file takes care of posting the response data to onlywire to confirm user's authetication
extract($_GET);

$url = "http://onlywire.com/widget/http_auth.php?auth_user=".urlencode($auth_user)."&auth_pw=".urlencode($auth_pw);
$a = EncodePostRequest($url,"", $_GET);

echo $a[1];
?>