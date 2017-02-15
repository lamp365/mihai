<?php
/*
    根据产品数据信息来获取适用的优惠卷信息
    in: array('price'=>'产品总金额，不包含运费和税费', 'openid'=>'用户标识符', 'goods'=>array(array('id'=>'产品ID','num'=>'产品数量'),array('id'=>'产品ID','num'=>'产品数量')));
    out: array(array(优惠卷),array(优惠卷),array(优惠卷));
    优惠卷数据结构：
    Array ( [bonus_id] => 5 [bonus_type_id] => 3 [openid] => 2015111911924 [type_id] => 3 [type_name] => 这是一个优惠卷 [type_money] => 100.00 [send_type] => 2 [deleted] => 0 [min_amount] => 0.00 [max_amount] => 0.00 [send_start_date] => 1477044060 [send_end_date] => 1480545000 [use_start_date] => 1477044060 [use_end_date] => 1480545000 [min_goods_amount] => 200.00 ) 
	bonus_id : 个人优惠卷的ID
	bonus_type_id: 个人优惠卷对应的主题ID
	openid: 优惠卷所属用户的ID
	type_id: 主题ID
	type_name:主题名称
	type_money:优惠卷减免金额
	send_type: 优惠卷类型
	deleted : 优惠卷状态 0: 正常 1:不可用
	min_goods_amount : 优惠卷订单金额需求
	本函数已经通过订单及用户的ID，把可以使用的优惠卷按金额大小排序，不可用的优惠卷已经做了过滤, 后期会继续完善本功能，目前只针对满足商品，及满足订单金额2种情况的优惠卷
*/
function get_bonus_list($order_date=array()){
	if ( empty ( $order_date['openid'] ) ||  empty ( $order_date['price'] ) ){
         return false;
	}
	// 获取优惠卷信息
	$bonus = mysqld_selectall("SELECT a.bonus_id,a.bonus_type_id,a.openid,a.bonus_sn,b.* FROM " . table('bonus_user')." a left join ".table('bonus_type')." b on a.bonus_type_id = b.type_id where b.deleted = 0 and a.deleted = 0 and a.isuse = 0 and b.use_start_date <= " .time(). " and b.use_end_date > ".time() ." and min_goods_amount <= ".$order_date['price']." and a.openid = '".$order_date['openid']."' order by type_money desc, min_goods_amount asc, use_end_date asc ");
	$bonus_list = array();
	// 开始过滤不适用的优惠卷
	foreach ( $bonus as $bonus_value ) {

		 $goods = array();
		 // 默认为可以适用
		 $iscan = 0;
		// 排查按商品发放的优惠卷
         if ( ($bonus_value['send_type'] == 1) && !empty($order_date['goods'])){
               $goods = mysqld_selectall("SELECT good_id FROM".table('bonus_good')." WHERE bonus_type_id = ".$bonus_value['type_id']);
	           foreach ( $order_date['goods'] as $goods_value ) {
				  foreach ( $goods as $good_value ){
                        if ( $good_value['good_id'] == $goods_value['id'] ){
                            $iscan = 1;
					        continue;
				        }
				  }  
			   }
		 }
		 // 排查满减的优惠卷   或者  //按照特殊活动的优惠卷
		if ( $bonus_value['send_type'] == 2 or $bonus_value['send_type'] == 0 or $bonus_value['send_type'] == 4){
               if ($bonus_value['min_goods_amount'] <= $order_date['price'] ){
                  $iscan =1;
			   }
		 }
		 if ( $iscan == 1 ){
               $bonus_list[] = $bonus_value;
		 }
	}

    return $bonus_list;
}
