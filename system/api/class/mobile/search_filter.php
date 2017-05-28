<?php
/**
 * app 搜索页搜索结果接口
 */

	$keyword = trim($_GP['keyword']);						//搜索关键字
	
	if($keyword!='')
	{
		//国家列表
		$country_list = mysqld_selectall("SELECT id,name FROM " . table('shop_country') . "  where deleted=0");
	
		$result['data']['country_list'] = $country_list;
		$result['data']['brand_list'] 	= getBrandByKeyWord($keyword);
		$result['code'] 				= 1;
	}
	else{
		$result['message']	= '请输入搜索关键字!';
		$result['code'] 	= 0;
	}
	
	echo apiReturn($result);
	exit;
	
	/**
	 * 获得指定搜索关键字下的商品品牌
	 * 
	 * @param $keyword:搜索关键字
	 * @return 品牌数组
	 */
	function getBrandByKeyWord($keyword)
	{
		
		//限时促销商品不显示
		$where = " where d.status=1 and d.deleted=0 ";
		
		$keySearch = " and d.title like '%".$keyword."%' ";
		$whereSearch = $where.$keySearch;
		
		$sql = "SELECT distinct b.id as brand_id,b.brand FROM " . table('shop_dish');
		$sql.= " as d LEFT JOIN ". table('shop_goods') ." as g on d.gid = g.id ";
		$sql.= " LEFT JOIN ".table('shop_brand')." as b on b.id = g.brand ";
		$sql.= $whereSearch;
		$sql.= ' and b.id is not null';
		
		$brand_list = mysqld_selectall($sql);
		
		if ( empty($brand_list) && !empty($keyword) && function_exists('scws_new') ){
			$word = get_word($keyword);
			if ( !empty($word) && is_array($word) ){
				foreach ($word as $word_value ) {
					$keys[] = " d.title like '%".$word_value."%' ";
				}
			}
			$keys = implode(' or ' , $keys);
			$keySearch = ' and ('.$keys.')';
			$whereSearch = $where.$keySearch;
		
			$sql = "SELECT distinct b.id as brand_id,b.brand FROM " . table('shop_dish');
			$sql.= " as d LEFT JOIN ". table('shop_goods') ." as g on d.gid = g.id ";
			$sql.= " LEFT JOIN ".table('shop_brand')." as b on b.id = g.brand ";
			$sql.= $whereSearch;
			$sql.= ' and b.id is not null';
				
			$brand_list = mysqld_selectall($sql);
		}
		
		return $brand_list;
	}
