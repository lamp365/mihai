<?php

	/**
	 * app的banner接口
	 *
	 */

	$position = json_decode($_REQUEST['position'], true);		//显示位置

	$result = array();
		
	$banner_list = mysqld_selectall("SELECT banner_id,link_type,position,link,thumb FROM " . table('app_banner') . " where position in(".implode(",", $position).") and enabled=1 ORDER BY displayorder DESC");
	
	if(!empty($banner_list))
	{
		$arrBanner = array();
		
		foreach($banner_list as $value)
		{
			//首页顶部
			if($value['position']==1)
			{
				$value['param'] = getUrlParam($value['link']);
			}
			
			$arrBanner[] = $value;
		}
		
		$result['data']['banner_list']	= $arrBanner;
	}
	else{
		$result['data']['banner_list']	= $banner_list;
	}
	
	$result['code']	= 1;
		
	echo apiReturn($result);
	exit;
	
	/**
	 * 获取URL
	 * @param unknown $query
	 * @return array URL参数
	 */
	function getUrlParam($url)
	{
		$arr_query=parse_url($url);
		
		$queryParts = explode('&', $arr_query['query']);
		$params = array();
		foreach ($queryParts as $param) {
			$item = explode('=', $param);
			$params[$item[0]] = $item[1];
		}
		return $params;
	}
