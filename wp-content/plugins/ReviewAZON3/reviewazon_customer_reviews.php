<?php
require_once( dirname(__FILE__) . '../../../../wp-config.php');
$postid = $_GET['id'];
$requestURL = get_post_meta($postid,'ReviewAZON_Iframe_URL',true).'&ie=UTF8';

$header[0] = "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8";
$header[] = "Cache-Control: max-age=0";
$header[] = "Connection: keep-alive";
$header[] = "Keep-Alive: 300";
$header[] = "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7";
$header[] = "Accept-Language: en-us,en;q=0.5";

$session = curl_init($requestURL);
curl_setopt($session, CURLOPT_HTTPHEADER, $header); 
curl_setopt($session, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.0; en-US; rv:1.9.2.6) Gecko/20100625 Firefox/3.6.6');
curl_setopt($session, CURLOPT_ENCODING, "UTF-8"); 
curl_setopt($session, CURLOPT_RETURNTRANSFER, 1); 
$response = curl_exec($session);
curl_close($session); 
echo iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', $response);
?>