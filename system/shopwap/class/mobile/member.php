<?php
$member=get_member_account(true,true);
$openid =$member['openid'] ;
$memberinfo=member_get($openid);
$op = $_GP['op'];
if ( $op == 'collect'){
	$hobby    = mysqld_select("SELECT * FROM ".table('member_info')." WHERE openid = :openid ", array(":openid"=>$openid));
	//获取分类
	$cat_sql  = "SELECT id,name,thumb  FROM " . table('shop_category') . " WHERE parentid=0 and deleted=0";
	$cat_data = mysqld_selectall($cat_sql);
    include themePage('member_collect');
}elseif($op == 'hobby'){  // 表单提交个人购物喜好
	$mem_data['age']      =  empty($_GP['age'])? '' : $_GP['age'];
	$mem_data['sex']      =  empty($_GP['sex'])? '' : $_GP['sex'];
	$mem_data['tobuy']    =  empty($_GP['tobuy'])? '' : $_GP['tobuy'];
	$mem_data['fun']      =  empty($_GP['fun'])? '' : $_GP['fun'];
	$mem_data['category'] =  empty($_GP['category'])? '' :$_GP['category'];
	$mem_data['index']    = 4;

	foreach($mem_data as $key => $val){
		if(empty($val)){
			unset($mem_data[$key]);
		}
	}

	$hobby = mysqld_select("SELECT * FROM ".table('member_info')." WHERE openid = :openid ", array(":openid"=>$openid));
	if(empty($hobby)){
		$mem_data['openid'] = $openid;
		mysqld_insert('member_info', $mem_data);
	}else{
		mysqld_update('member_info', $mem_data, array('openid'=>$openid));
	}
	if(is_mobile_request()){
		die(showAjaxMess(200,"您的喜好已保存！"));
	}else{
		message('您的喜好已保存！',refresh(),'success');
	}


}else if ( $op == 'list' ){   //账单列表
	$psize =  10;
	$pindex = max(1, intval($_GP["page"]));
	$limit = ' limit '.($pindex-1)*$psize.','.$psize;

	if ( isset($_GP['type']) && !empty($_GP['type'])){
		 $type = $_GP['type'];
		 if ( $type == 2 ){
			 $title = '积分明细';
			 if($_GP['status'] == 2){
				 //查看出账
				 $where = "openid='{$openid}' and type = 'usecredit'";
			 }else if($_GP['status'] == 1){
				 //查看进账
				 $where = "openid='{$openid}' and type = 'addcredit'";
			 }else{
				 //查看所有
				 $where = "openid='{$openid}' and (type = 'addcredit' or type = 'usecredit')";
			 }

		 }else{
			 $title = '账单明细';
			 if($_GP['status'] == 2){
				 //查看出账
				 $where = "openid='{$openid}' and type = 'usegold'";
			 }else if($_GP['status'] == 1){
				 //查看进账
				 $where = "openid='{$openid}' and (type != 'addcredit' and type != 'usecredit' and type != 'usegold')";
			 }else{
				 //查看所有
				 $where = "openid='{$openid}' and (type != 'addcredit' and type != 'usecredit')";
			 }

		 }
		$pay_list = mysqld_selectall("SELECT * FROM ".table('member_paylog')." WHERE {$where} order by pid desc {$limit}");
		$sql_num  = "SELECT count(openid) FROM ".table('member_paylog')." WHERE {$where}";

		$pay_list = get_paylog_thumb($pay_list);

		//当手机端滑动的时候加载下一页
		if ($_GP['nextpage'] == 'ajax' && $_GP['page'] > 1 ){
			if ( empty($pay_list) ){
				die(showAjaxMess(1002,'查无数据！'));
			}else{
				die(showAjaxMess(200,$pay_list));
			}
		}

		$total = '';
		$pager = '';
		if(!empty($pay_list)){
			$total     = mysqld_selectcolumn($sql_num);
			$pager     = pagination($total, $pindex, $psize);
		}

         include themePage('property');
	}
}else if($op == 'collectshop'){   //收藏商品列表
	$psize  =  12;
	$pindex = max(1, intval($_GP["page"]));
	$limit  = ' limit '.($pindex-1)*$psize.','.$psize;
	$collectshop  = mysqld_selectall("select dish_id from ".table('goods_collection')." where openid='{$openid}' and dish_id != 0 {$limit} ");
	$sql_num      = "SELECT count(openid) FROM ".table('goods_collection')." WHERE openid='{$openid}' and dish_id != 0 ";

	$good_data = array();
	foreach($collectshop as $shop){
		$condition['table'] = 'shop_dish';
		$condition['where'] = "a.id={$shop['dish_id']}";
		$good_data[] = get_good($condition);
	}

	//当手机端滑动的时候加载下一页
	if ($_GP['nextpage'] == 'ajax' && $_GP['page'] > 1 ){
		if ( empty($pay_list) ){
			die(showAjaxMess(1002,'查无数据！'));
		}else{
			die(showAjaxMess(200,$pay_list));
		}
	}

	$total = '';
	$pager = '';
	if(!empty($collectshop)){
		$total     = mysqld_selectcolumn($sql_num);
		$pager     = pagination($total, $pindex, $psize);
	}
	include themePage('collect');

}else if($op == 'delcollect'){  //移除收藏商品
	if(empty($_GP['id'])){
		message('对不起，参数有误！',refresh(),'error');
	}
	if(empty($openid)){
		message('对不起，请您先登录！',refresh(),'error');
	}
	$res = mysqld_delete('goods_collection',array('openid'=>$openid,'dish_id'=>$_GP['id']));
	if($res){
		die(showAjaxMess(200,'删除成功！'));
	}else{
		die(showAjaxMess(200,'删除失败！'));
	}
}else{
			 if ( strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false ) {
					 $weixinthirdlogin = mysqld_select("SELECT * FROM " . table('thirdlogin') . " WHERE enabled=1 and `code`='weixin'");
					 if(!empty($weixinthirdlogin)&&!empty($weixinthirdlogin['id']))
					 {
							$isweixin      = true;
							$weixin_openid = get_weixin_openid();
					 }
			 }
			if (checksubmit("submit")) {
					if ( !empty($_GP['email']) ){
						$objValidator	= new Validator();
						if(!$objValidator->is($_GP['email'],'email')){
							   message('请输入正确的邮箱地址');
						}
					}
					$data = array(
						'realname'   => $_GP['realname'],
                    	'email'      => $_GP['email'],
                    	'nickname'   => $_GP['nickname'],
					);
                if ( empty($memberinfo['birthday']) && !empty($_GP['birthday']) ){
                    $data['birthday'] = strtotime($_GP['birthday']);
				}
				mysqld_update('member', $data,array('openid'=>$openid));
			
			    message('资料修改成功！', mobile_url('fansindex'), 'success');
			  
			}
		    include themePage('member/member');
}


/**
 * 获取账单记录的一张缩略图
 * @param $pay_list
 * @return mixed
 */
function get_paylog_thumb($pay_list){
	foreach($pay_list as &$one_list){
		switch($one_list['type']){
			case 'usecredit':
			case 'addcredit':
				$one_list['pic'] = WEBSITE_ROOT."themes/default/__RESOURCE__/recouse/images/paylog_jifen.png";
				break;
			case 'usegold':
			case 'addgold':
				if(empty($one_list['ordersn'])){
					$one_list['pic'] = WEBSITE_ROOT."themes/default/__RESOURCE__/recouse/images/paylog_money.png";
				}else{
					$o_sql = "select og.shopgoodsid from ".table('shop_order')." as o left join ".table('shop_order_goods')." as og ";
					$o_sql.= " on og.orderid=o.id where o.ordersn='{$one_list['ordersn']}'";
					$order = mysqld_select($o_sql);
					$goods = mysqld_select("select thumb from ".table('shop_goods')." where id={$order['shopgoodsid']}");
					$one_list['pic'] = download_pic($goods['thumb'],150,150,2);
				}
				break;
			case 'addgold_byinvite':
			case 'addgold_byorder':
				//获取觅友的头像
				if(empty($one_list['friend_openid'])){
					$face = WEBSITE_ROOT."themes/default/__RESOURCE__/recouse/images/userface.png";
				}else{
					$m_info = member_get($one_list['friend_openid']);
					$face   = empty($m_info['avatar']) ?  WEBSITE_ROOT."themes/default/__RESOURCE__/recouse/images/userface.png" : $m_info['avatar'];
				}
				$one_list['pic'] = $face;
				break;
		}
	}
	return $pay_list;
}