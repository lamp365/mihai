<?php
$best_brand = mysqld_selectall("SELECT * FROM " . table('shop_brand') . "  where deleted=0 and recommend = 1");
foreach ( $best_brand as &$best_brand_value ){
	 $country = get_country($best_brand_value['country_id']);
	 $best_brand_value['country_img'] = $country['icon'];
}
$normal_brand = mysqld_selectall("SELECT * FROM " . table('shop_brand') . "  where deleted=0 and recommend = 0");
foreach ( $normal_brand as &$normal_brand_value ){
	 $country = get_country($normal_brand_value['country_id']);
	 $normal_brand_value['country_img'] = $country['icon'];
}
// 获取指定ID国家信息
function get_country($id) {
	$country_data = mysqld_select('SELECT * FROM '.table('shop_country')." WHERE  id=:uid AND deleted=0" , array(':uid'=> $id));
	return $country_data;
}
include themePage('brands');