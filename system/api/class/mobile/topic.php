<?php

	/**
	 * app的专题接口
	 *
	 */

	$result = array();
	
	$sql = "SELECT b.banner_id,b.topic_id,b.link,b.thumb,t.type FROM " . table('app_topic_banner')." b,".table('app_topic')." t ";
	$sql.= " where b.topic_id=t.topic_id ";
	$sql.= " and b.enabled=1 ";
	$sql.= " and t.enabled=1 ";
	$sql.= " ORDER BY t.displayorder DESC,b.displayorder desc ";
	
	$banner_list = mysqld_selectall($sql);
	
	if(!empty($banner_list))
	{
		$result['data']['banner_list']	= $banner_list;
	}
	else{
		$result['data']['banner_list']	= array();
	}
	
	$result['code']	= 1;
		
	echo apiReturn($result);
	exit;
