<?php
define('SYSTEM_ACT', 'mobile');
$_SERVER['PHP_SELF']= str_replace('notify/',"",$_SERVER['PHP_SELF']);
$mname = 'shopwap';
$do    = 'weixinpay';
$_GET['op'] = 'native_notify';  //同步通知
ob_start();
$CLASS_LOADER="driver";
require '../includes/init.php';
ob_end_flush();
exit;