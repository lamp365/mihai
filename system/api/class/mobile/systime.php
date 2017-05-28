<?php
/**
 * app 系统时间接口
 */

$result['data']['systime'] 	= date('Y-m-d H:i:s');
$result['code'] 			= 1;

echo apiReturn($result);
exit;