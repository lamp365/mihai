<?php
	/**
	 * app类目接口
	 * 
	 */

	$result = array();
	
	$field = 'id,name,parentid';
	$where = "  where deleted=0 and enabled=1 ";
	
	//父类目ID
	if(isset($_GP['parentid']))
	{
		$where.= " and parentid =".(int)$_GP['parentid'];
	}
	
	//推荐分类
	if(isset($_GP['isrecommand']))
	{
		$field.= ",app_ico";
		$where.= " and app_isrecommand =".(int)$_GP['isrecommand'];
	}
	else{
		
		$field.= ",thumb as app_ico";
	}
	
	//$sql.= " ORDER BY parentid ASC, displayorder ASC";
	$where.= " ORDER BY displayorder desc";
	
	//限制条数
	if(isset($_GP['limit']))
	{
		$where.= " LIMIT 0 , ".(int)$_GP['limit'];
	}
	
	$sql ='SELECT '.$field." FROM " . table('shop_category') . $where;

	$category_list = mysqld_selectall($sql);
	
	$result['data']['category_list']= $category_list;
	$result['code'] 				= 1;
	
	echo apiReturn($result);
	exit;
