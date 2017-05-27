<?php
// 获取热搜词
function getHottpoic($classifyId) {
	$hot = mysqld_select('SELECT * FROM '.table('shop_hottopic')." WHERE classify_id=:uid" , array(':uid'=> $classifyId));
	$token = explode(';',$hot['hottopic']);
    $use_ary = array();
    foreach ($token as $u_v) {
		if ( !empty ($u_v) ){
			$ary = array();
			$ary['name'] = $u_v;
			$url = create_url('mobile', array('name' => 'shopwap','do' => 'goodlist','keyword'=>$u_v));
			$ary['url'] = $url;
			$use_ary[] = $ary;
		}
    }
	return $use_ary;
}