<?php
/**
* app 热搜词接口
*
*
*/

$hottopic = mysqld_select("SELECT hottopic FROM " . table('shop_hottopic') . "  where classify_id=0");

if($hottopic)
{
	$result['data']['hottopic_list']= explode(";", $hottopic['hottopic']);
	$result['code'] 				= 1;
}
else{
	$result['message']	= '热搜词不存在!';
	$result['code'] 	= 0;
}

echo apiReturn($result);
exit;