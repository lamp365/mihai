<?php
error_reporting(E_ERROR);
require_once './phpqrcode.php';
$url = urldecode($_GET["data"]); 

//echo $url;
//$url="http://192.168.1.4/fjmsl/index.php?mod=mobile&name=shopwap&do=fansindex&user_id=2015112320785";
$img = QRcode::png($url);
