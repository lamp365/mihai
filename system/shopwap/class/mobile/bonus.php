<?php
header('Access-Control-Allow-Origin:*');
$member=get_member_account(true,true);
$openid = $member['openid'];
$member=member_get($openid);
$op = $_GP['op'];

//禁止直接领取的优惠卷 新手卷 活动卷 礼品兑换卷
$fobiden_bonus = array(0,4,5);
// 领取优惠卷
if ( $op == 'get' ){
   $id = intval($_GP['id']);
   if ( empty( $id ) ){
	   //由于app嵌套了自定义活动页 活动页里有领取优惠券的操作，需要用ajax操作，不能跳转操作，故才加的
	   if(empty($_GP['showajax'])){
		   message('当前优惠卷领取地址有误',refresh(),'error');
	   }else{
		   die(showAjaxMess(1002,'当前优惠卷领取地址有误'));
	   }
   }
   // 找到优惠卷的信息 send_start_date 	send_end_date
   $bonus = mysqld_select("SELECT * FROM " . table('bonus_type')." where type_id='".$id."' and deleted = 0 ");
   //and send_start_date <= " .time(). " and send_end_date > ".time()
   if ( !$bonus ){
	   if(empty($_GP['showajax'])) {
		   message('当前优惠卷已失效',refresh(),'error');
	   }else{
		   die(showAjaxMess(1002,'当前优惠卷已失效'));
	   }
   }
   if ( $bonus['send_start_date'] > time() ){
	   if(empty($_GP['showajax'])) {
		   message('该优惠券还未开抢',refresh(),'error');
	   }else{
		   die(showAjaxMess(1002,'该优惠券还未开抢'));
	   }
   }

   if ( $bonus['send_end_date'] < time() ){
	   if(empty($_GP['showajax'])) {
		   message('该优惠券已被抢光，您可以关注一下其他优惠哦！',refresh(),'error');
	   }else{
		   die(showAjaxMess(1002,'该优惠券已被抢光'));
	   }
   }

	if(in_array($bonus['send_type'],$fobiden_bonus)){
		if(empty($_GP['showajax'])) {
			message('该优惠券用于活动时领取',refresh(),'error');
		}else{
			die(showAjaxMess(1002,'该优惠券用于活动时领取'));
		}
	}

	//用于判断当前是否是最后一张，是的话，前端ajax请求后，需要刷亲页面
	$limit_bonus = 1;
   if ( $bonus['send_max'] > 0 ){
        $user_had = mysqld_selectcolumn("SELECT count(*) FROM " .table('bonus_user'). " WHERE openid = :openid and bonus_type_id = :bonus_type_id ", array(":bonus_type_id"=>$id, ":openid"=> $openid));
		if ( $user_had >= $bonus['send_max']){
			if(empty($_GP['showajax'])) {
				message("该优惠券每人可领{$user_had}张，快去选购喜欢的宝贝吧！",refresh(),'error');
			}else{
				die(showAjaxMess(1002,"该优惠券每人可领{$user_had}张"));
			}
		}
	   $limit_bonus = $bonus['send_max'] - $user_had -1; //如果是最后一次是0
   }

	$bonus_sn=date("Ymd",time()).$id.rand(1000000,9999999);
	$bonus_user = mysqld_select("SELECT * FROM " . table('bonus_user')."where bonus_sn='".$bonus_sn."'" );
	while(!empty($bonus_user['bonus_id']))
	{
		   $bonus_sn=date("Ymd",time()).$id.rand(1000000,9999999);
		   $bonus_user = mysqld_select("SELECT * FROM " . table('bonus_user')."where bonus_sn='".$bonus_sn."'" );
	}
	$data=array('createtime'=>time(),
				'openid'=>$openid,
				'bonus_sn'=>$bonus_sn,
				'deleted'=>0,
				'isuse'=>0,
				'bonus_type_id'=>$id);
   mysqld_insert('bonus_user',$data);
	if(empty($_GP['showajax'])) {
		message("恭喜，领取成功","refresh","success");
	}else{
		if($limit_bonus == 0){
			die(showAjaxMess(202,'恭喜，领取成功'));
		}else{
			die(showAjaxMess(200,'恭喜，领取成功'));
		}
	}

   exit;
}
//新手礼
if ( $op == 'new_member' ){
	//领取优惠券一些别处地方也要用，故提取作为函数
	$data_info = new_member_bonus($openid);
	//如果需要ajax请求的则，带参数 showajax
	if(!empty($_GP['showajax'])){
		die(json_encode($data_info));
	}else{
		//直接提示跳转
		$msg = $data_info['message'];
		message($msg,refresh(),'success');
	}
}

// 展示优惠卷列表
if ( $op == 'list' ){
   $dates = array('price'=>'300','openid'=>'2015111911924','goods'=>array(array('id'=>2)));
   $bouns = get_bonus_list($dates);
   exit;
}
//下单时获取优惠卷金额 ajax请求直接返回值
if ( $op == 'get_price' ){
   $bounsn = $_GP['id'];
   $bonus = mysqld_select("select bonus_user.*,bonus_type.type_name,bonus_type.type_money,bonus_type.use_start_date,bonus_type.use_end_date from " . table("bonus_user")." bonus_user left join  " . table("bonus_type")." bonus_type on bonus_type.type_id=bonus_user.bonus_type_id where bonus_user.deleted=0  and `openid`=:openid and bonus_user.isuse = 0 and bonus_user.bonus_sn = :bonus_sn ",array(':openid'=>$openid,':bonus_sn'=>$bounsn));
   if ( $bonus ){
      echo $bonus['type_money'];
   }else{
      echo 0;
   }
   exit;
}
//下单时获取余额  ajax请求直接返回值
if($op == 'balance'){
	$gold                   = $member['gold'];   //余额
	$freeorder_gold         = $member['freeorder_gold'];  //免单返现金额
	$freeorder_gold_endtime = $member['freeorder_gold_endtime'];  //免单金额使用期限
	$balance  = getMemberBalance($gold,$freeorder_gold,$freeorder_gold_endtime);
	die($balance);
}

if($op == 'bonus_goods'){
	//获取优惠卷按照商品发放 所拥有的商品
	$bonus_id = $_GP['bonus_id'];
	$endTime = time();
	$sql = "select bonus_type.deleted as bt_deleted,bonus_user.deleted as bu_deleted,bonus_user.openid as openid,";
	$sql.= " bonus_user.isuse ,bonus_type.use_end_date, bonus_type.send_type ,bonus_type.type_id";
	$sql.= " from ".table('bonus_user')." as bonus_user left join ".table("bonus_type")." as bonus_type";
	$sql.= " on bonus_type.type_id=bonus_user.bonus_type_id ";
	$sql.= " where bonus_user.bonus_id = {$bonus_id}";
	$bonus = mysqld_select($sql);

	$bonus_goods = array();
	$error_info  = '';
	if(!empty($bonus)){
		if($bonus['openid'] != $openid || $bonus['isuse'] == 1 || $bonus['send_type'] != 1){
			$error_info = "查无该优惠卷！";
		}
		if($bonus['bt_deleted'] == 1 || $bonus['bu_deleted'] == 1){
			$error_info = "该优惠卷已经作废！";
		}
		if($bonus['use_end_date'] < $endTime){
			$error_info = "该优惠卷已经过期！";
		}
		if(empty($error_info)){
			//查找对应的商品
			$bonus_goods_ids = mysqld_selectall("select good_id from ".table('bonus_good')." where bonus_type_id={$bonus['type_id']}");
			if(!empty($bonus_goods_ids)){
				foreach($bonus_goods_ids as $item){
					$where['table'] = 'shop_dish';
					$where['where'] = 'a.id='.$item['good_id'];
					$bonus_goods[] = get_good($where);
				}
			}
		}
	}

	include themePage('bonus_goods');
	exit;
}

$endTime = time();
$bonus_sql = "select bonus_user.*,bonus_type.send_type,bonus_type.type_name,bonus_type.type_money,bonus_type.use_start_date,bonus_type.use_end_date,bonus_type.min_goods_amount ";
$bonus_sql.= " from ".table('bonus_user')." as bonus_user left join ".table('bonus_type')." as bonus_type";
$bonus_sql.= " on bonus_type.type_id=bonus_user.bonus_type_id ";
$bonus_sql.= " where bonus_type.deleted=0 and bonus_user.deleted=0 and bonus_user.openid ={$openid}";
$bonus_sql.= " and bonus_user.isuse = 0 and bonus_type.use_end_date > {$endTime}";
$bonus_sql.= " order by bonus_user.bonus_id desc";  //按照最新排列
//ppd($bonus_sql);
$bonuslist = mysqld_selectall($bonus_sql);
//随机获取一个分类id
$cateArr = mysqld_selectall("select id from ".table('shop_category')." where parentid=0 and enabled=1 and deleted=0 limit 10");
$key     = array_rand($cateArr);
$cate_id = $cateArr[$key]['id'];

include themePage('bonuslist');