<?php
/**
 * app版本升级控制
 *
 */

$app_type 	= (int)$_GP['app_type'];
$device_code= trim($_REQUEST['device_code']);

$versionInfo = mysqld_select("SELECT version_no,force_update,url,comment,createtime FROM " . table('app_version') . " where app_type={$app_type} order by createtime desc");

insertAccessLog($device_code,true);

//版本信息不存在时
if(empty($versionInfo))
{
	$result['data']['version'] 	= array();
	$result['code'] 			= 0;
}
else{
	$result['data']['version'] 	= $versionInfo;
	$result['code'] 			= 1;
}

echo apiReturn($result);
exit;
