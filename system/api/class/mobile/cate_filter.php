<?php
/**
 * app 类目下的筛选条件接口
 * @var unknown
 *
 */

$cate_id= (int)$_GP['cate_id'];							//类目ID

if(!empty($cate_id))
{
	//国家列表
	$country_list = mysqld_selectall("SELECT id,name FROM " . table('shop_country') . "  where deleted=0");
	
	$result['data']['country_list'] = $country_list;
	$result['data']['brand_list'] 	= getBrandByCategoryId($cate_id,$dishIds);
	$result['code'] 				= 1;
}
else{
	$result['message'] 	= '请输入分类ID';
	$result['code'] 	= 0;
}


echo apiReturn($result);
exit;
/**
 * 获得指定类型下的商品品牌
 *
 * @param $cate_id:类目ID
 *
 * @return 品牌数组
 */
function getBrandByCategoryId($cate_id)
{
	//扩展分类下的商品ID
	$dishIds = getCategoryExtendDishId($cate_id);
	
	
	$sql = "SELECT distinct b.id as brand_id,b.brand FROM " . table('shop_dish');
	$sql.= " as d LEFT JOIN ". table('shop_goods') ." as g on d.gid = g.id ";
	$sql.= " LEFT JOIN ".table('shop_brand')." as b on b.id = g.brand ";

	if(!empty($dishIds))
	{
		$sql.=' where (d.p1='.$cate_id.' or d.p2='.$cate_id.' or d.p3='.$cate_id.' or d.id in('.implode(",", $dishIds).') ) ';
	}
	else{
		$sql.=' where (d.p1='.$cate_id.' or d.p2='.$cate_id.' or d.p3='.$cate_id.') ';
	}

	$sql.= ' and d.status=1 and d.deleted=0 and d.type in(0,1,2,3)';
	$sql.= ' and b.id is not null';

	return mysqld_selectall($sql);
}