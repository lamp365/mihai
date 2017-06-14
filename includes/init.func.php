<?php
/*
公共加载
*/

if (file_exists(WEB_ROOT . '/config/version.php')) {
    require WEB_ROOT . '/config/version.php';
}
if (file_exists(WEB_ROOT . '/config/debug.php')) {
    require WEB_ROOT . '/config/debug.php';
}

//公共函数的命名 一定是要放在 includes下面，然后xxx.func.php命名
$listFuncs = glob(WEB_ROOT.'/includes/*.func.php');
foreach( $listFuncs as $func) {
	require_once $func;
}
//加载完公共函数后 再加载cmmon lib下的文件
if (file_exists(WEB_ROOT . '/config/config.php') && file_exists(WEB_ROOT . '/config/install.link')) {
	require (WEB_ROOT . '/system/common/lib/lib.php');
}
//引入verndon 自动加载类库
require_once WEB_ROOT.'/includes/vendor/autoload.php';

$cfg = globaSetting();
