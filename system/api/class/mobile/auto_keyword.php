<?php
/**
 * app 搜索页的联想搜索接口
 */

$keyword = trim($_GP['keyword']);		//搜索关键字

if($keyword!='')
{	
	$sql= "SELECT title FROM " . table('shop_dish') . "  where status=1 and deleted=0 and title like '%".$keyword."%' order by title";
	
	$dish_list = mysqld_selectall($sql);

	$result['data']['dish_list']= $dish_list;
	$result['code'] 			= 1;
}
else{
	$result['message']	= '请输入搜索关键字!';
	$result['code'] 	= 0;
}

echo apiReturn($result);
exit;