<?php
header('Access-Control-Allow-Origin:*');
$member=get_member_account(true,true);
$openid = $member['openid'];
$member=member_get($openid);
$op = $_GP['op'];
// 领取优惠卷
if ( $op == 'get' ){
   $id = intval($_GP['id']);
   if ( empty( $id ) ){
	   //由于app嵌套了自定义活动页 活动页里有领取优惠券的操作，需要用ajax操作，不能跳转操作，故才加的
	   if(empty($_GP['showajax'])){
		   message('当前优惠卷领取地址有误');
	   }else{
		   die(showAjaxMess(1002,'当前优惠卷领取地址有误'));
	   }
   }
   // 找到优惠卷的信息 send_start_date 	send_end_date
   $bonus = mysqld_select("SELECT * FROM " . table('bonus_type')." where type_id='".$id."' and deleted = 0 ");
   //and send_start_date <= " .time(). " and send_end_date > ".time()
   if ( !$bonus ){
	   if(empty($_GP['showajax'])) {
		   message('当前优惠卷已失效');
	   }else{
		   die(showAjaxMess(1002,'当前优惠卷已失效'));
	   }
   }
   if ( $bonus['send_start_date'] > time() ){
	   if(empty($_GP['showajax'])) {
		   message('该优惠券还未开抢');
	   }else{
		   die(showAjaxMess(1002,'该优惠券还未开抢'));
	   }
   }

   if ( $bonus['send_end_date'] < time() ){
	   if(empty($_GP['showajax'])) {
		   message('该优惠券已被抢光，您可以关注一下其他优惠哦！');
	   }else{
		   die(showAjaxMess(1002,'该优惠券已被抢光'));
	   }
   }
   if ( $bonus['send_max'] > 0 ){
        $user_had = mysqld_selectcolumn("SELECT count(*) FROM " .table('bonus_user'). " WHERE openid = :openid and bonus_type_id = :bonus_type_id ", array(":bonus_type_id"=>$id, ":openid"=> $openid));
		if ( $user_had >= $bonus['send_max']){
			if(empty($_GP['showajax'])) {
				message('您已领取过该优惠券了，快去选购喜欢的宝贝吧！');
			}else{
				die(showAjaxMess(1002,'您已领取过该优惠券了'));
			}
		}
   }
   	if(!empty($openid) && $bonus['send_type'] != 4 ){
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
				die(showAjaxMess(200,'恭喜，领取成功'));
			}
   }else{
       // 活动用优惠卷处理
	   message("恭喜","refresh", "success");
   }
   exit;
}
//新手礼
if ( $op == 'new_member' ){
	//是否有订单
	$order = mysqld_selectall("SELECT id FROM " . table('shop_order')." where openid='".$openid."' ");
	if($order)
	{
		message('非新手会员，无法领取新手');
		exit;
	}
	//是否已经领过券
	$bonusUser = mysqld_selectall("SELECT u.bonus_id FROM " . table('bonus_user')." u left join ". table('bonus_type')." t on t.type_id=u.bonus_type_id where u.openid='".$openid."' and t.send_type=0 ");
	if($bonusUser)
	{
		message('你已经领取过了。');
		exit;
	}
	// 找到优惠卷的信息 send_start_date 	send_end_date
	$bonus = mysqld_selectall("SELECT type_id FROM " . table('bonus_type')." where send_type=0 and deleted = 0 ");
	if ( !$bonus ){
		message('当前优惠卷已失效');
	}
	else{

		foreach($bonus as $bv)
		{
			$bonus_sn=date("Ymd",time()).$id.rand(1000000,9999999);
			$bonus_user = mysqld_select("SELECT * FROM " . table('bonus_user')."where bonus_sn='".$bonus_sn."'" );
			while(!empty($bonus_user['bonus_id']))
			{
				$bonus_sn=date("Ymd",time()).$id.rand(1000000,9999999);
				$bonus_user = mysqld_select("SELECT * FROM " . table('bonus_user')."where bonus_sn='".$bonus_sn."'" );
			}
			$data=array('createtime'	=> time(),
						'openid'		=> $openid,
						'bonus_sn'		=> $bonus_sn,
						'deleted'		=> 0,
						'isuse'			=> 0,
						'bonus_type_id'	=> $bv['type_id']);
			
			mysqld_insert('bonus_user',$data);
		}
		message("恭喜，领取成功","refresh","success");
	}
	
	exit;
}

// 展示优惠卷列表
if ( $op == 'list' ){
   $dates = array('price'=>'300','openid'=>'2015111911924','goods'=>array(array('id'=>2)));
   $bouns = get_bonus_list($dates);
   exit;
}
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
$bonuslist = mysqld_selectall("select bonus_user.*,bonus_type.type_name,bonus_type.type_money,bonus_type.use_start_date,bonus_type.use_end_date from " . table("bonus_user")." bonus_user left join  " . table("bonus_type")." bonus_type on bonus_type.type_id=bonus_user.bonus_type_id where bonus_type.deleted=0 and bonus_user.deleted=0  and bonus_user.openid =:openid and bonus_user.isuse = 0 and bonus_type.use_end_date > :use_end_date order by isuse,bonus_type.send_type ",array(':openid'=>$openid,':use_end_date'=>time()));
include themePage('bonuslist');