<?php
/**
 * app视频接口
 *
 */

$limit 	= $_GP['limit'] ? (int)$_GP['limit'] : 10;		//显示的记录数

$arrVideo = mysqld_selectall("SELECT video_url FROM " . table('app_video') . " where enabled=1 order by createtime desc limit 0,{$limit}");

//视频信息不存在时
if(empty($arrVideo))
{
	$result['data']['video'] 	= array();
	$result['code'] 			= 1;
}
else{
	$result['data']['video'] 	= $arrVideo;
	$result['code'] 			= 1;
}

echo apiReturn($result);
exit;
