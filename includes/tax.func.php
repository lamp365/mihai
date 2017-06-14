<?php
/**
*税率相关
* @author  wzw<wuzy141@qq.com>
*/

/** 
* get_taxs  
* 批量获取税率并计算总值 
* 
* @param array(array('id' => xxx, 'count' => xxx, 'price' => xxx, 'taxid' => xxx),...)
* 		 id:dishid
* @since 1.0 
* @return goods:
			id: 产品ID
			tax: 产品单税率
			sum_tax: 产品总税价
			count:产品数量
		  all_sum_tax : 所有产品税价和
*/
function get_taxs($array=array()) {
	$result = array();

	foreach ($array as $a_v) {
		$item = mysqld_select("SELECT tax FROM " . table('shop_tax') ." WHERE id=".$a_v['taxid']);
		$item['id'] = $a_v['id'];
		$item['count'] = $a_v['count'];
		$item['sum_tax'] = round($a_v['price']*(float)$item['tax']*$a_v['count'], 2) ;
		$result['goods'][$a_v['id']] = $item;
	}

	foreach ($result['goods'] as $r_v) {
		$result['all_sum_tax'] += (float)$r_v['sum_tax'];
		round($result['all_sum_tax'], 2);
	}

	return $result;
}

/**
 * 仅获取税率值
 */
function get_tax($id) {
	$result = mysqld_select("SELECT tax FROM " . table('shop_tax') ." WHERE id=".$id);
	return $result;
}