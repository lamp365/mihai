<?php
/**
 * 助力订单管理
 */


$operation = ! empty ( $_GP ['op'] ) ? $_GP ['op'] : '';

switch($operation){
	case 'getrandUser':    //获取随机的一个虚拟用户
	    $dummy_mem = mysqld_selectall("select realname,openid from ".table('member')." where dummy =1");
		$dummy_key = array_rand($dummy_mem);
	    $dummy = $dummy_mem[$dummy_key];
		$data  = array('name'=>$dummy['realname'],'openid'=>$dummy['openid']);
	    die(showAjaxMess(200,$data));
		break;
	case 'shua_member':   //刷单 post提交
		if(empty($_GP['usernum'])){
			message("人数不能为空！",refresh(),'error');
		}
		if(empty($_GP['username'])){
			message("用户名不能为空！",refresh(),'error');
		}
		if($_GP['usernum']>20){
			message("刷的人数一次性不能太多！",refresh(),'error');
		}
		if(empty($_GP['openid'])){
			//根据用户名查找虚拟用户
			if(is_numeric($_GP['username']))
				$dummy_mem = mysqld_select("select realname,openid from ".table('member')." where dummy =1 and mobile={$_GP['username']}");
			else
				$dummy_mem = mysqld_select("select realname,openid from ".table('member')." where dummy =1 and realname='{$_GP['username']}'");

			if(empty($dummy_mem)){
				message("该虚拟用户不存在",refresh(),'error');
			}
			$share_openid = $dummy_mem['openid'];
			$data_arr = mysqld_selectall("select * from ".table('member')." where dummy=1 limit {$_GP['usernum']}");
			foreach($data_arr as $data){
				user_award_by_register($data,$share_openid,'');
			}
		}else{
			$share_openid = $_GP['openid'];
			$data_arr = mysqld_selectall("select * from ".table('member')." where dummy=1 limit {$_GP['usernum']}");
			foreach($data_arr as $data){
				user_award_by_register($data,$share_openid,'');
			}
		}
		message("操作成功！",refresh(),'success');
		break;

	case 'apply_process':		//处理申请

		$share_status 	= (int)$_GP['share_status'];
		$ordersn 		= trim($_GP['ordersn']);
		$from_platform 	= trim($_GP['from_platform']);
		
		$data = array(	'share_status' 	=>$share_status,
						'updatetime' 	=>time()
		);
		
		//觅海订单
		if($from_platform=='self')
		{
			$table = 'shop_order';
		}
		//天猫等第三方订单
		else{
			
			$table = 'third_order';
		}
		
		//更新订单的分享状态
		if(mysqld_update($table,$data,array('ordersn'=>$ordersn,'share_status'=>2)))
		{
			//审核通过时，给用户返现
			if($share_status==3)
			{
				//订单信息
				$orderInfo = mysqld_select( "SELECT openid,updatetime FROM " . table ( $table )." where ordersn='{$ordersn}' ");
				
				$sql = "SELECT sum(fee) as order_fee FROM " . table('member_paylog_detail');
				$sql.= " where ordersn='{$ordersn}' ";
				$sql.= " and type='addgold_byinvite' ";
				
				$paylogDetail = mysqld_select($sql);
				
				//新增账单记录
				$remark = PayLogEnum::getLogTip('LOG_INVITE_CHECK_TIP');
				member_invitegold($orderInfo['openid'],'',$paylogDetail['order_fee'], 'addgold_byinvite', $remark,$ordersn);
				
				//更新账单详情表
				mysqld_update('member_paylog_detail',array('status'=>1),array('ordersn'=>$ordersn,'type'=>'addgold_byinvite'));
				
				pushImByApplyProcess($orderInfo['openid'],$ordersn,$paylogDetail['order_fee'],$orderInfo['updatetime']);
			}
			//驳回时
			elseif($share_status==-1)
			{
				//更新账单详细表
				mysqld_update('member_paylog_detail',array('status'=>-1),array('ordersn'=>$ordersn,'type'=>'addgold_byinvite'));
			}
				
			message ( '处理成功！', web_url ( 'share_order',array('from_platform'=>$from_platform)), 'success' );
		}
		else{
			message ( '处理失败！', web_url ( 'share_order',array('from_platform'=>$from_platform)), 'error' );
		}

		break;

	default:					//已配置免单列表页

		$from_platform = isset($_GP['from_platform']) ? $_GP['from_platform'] : '';		//订单平台来源
		
		$pindex = max(1, intval($_GP['page']));
		$psize 	= 10;

		if($from_platform=='shua'){
			include page ( 'share_order_list' );

			break;
		}else{
			if($from_platform=='tmall')
			{
				$sql = "SELECT t.ordersn,t.price,t.createtime,t.updatetime,t.address_realname,t.address_mobile,t.share_status,t.from_platform,count(p.friend_openid) as friend_cnt,sum(p.fee) as order_fee FROM " . table('third_order')." t ";
				$sql.= " left join ".table('member_paylog_detail')." p on p.ordersn=t.ordersn and p.type='addgold_byinvite' ";
				$sql.= " where t.share_status!=0 ";
				$sql.= " group by t.ordersn ";
				$sql.= " ORDER BY t.updatetime DESC limit ".($pindex - 1) * $psize . ',' . $psize;
			}
			else{
				$sql = "SELECT o.ordersn,o.price,o.createtime,o.updatetime,o.address_realname,o.address_mobile,o.share_status,'self' as from_platform,count(p.friend_openid) as friend_cnt,sum(p.fee) as order_fee FROM " . table('shop_order')." o ";
				$sql.= " left join ".table('member_paylog_detail')." p on p.ordersn=o.ordersn and p.type='addgold_byinvite' ";
				$sql.= " where o.share_status!=0 ";
				$sql.= " group by o.ordersn ";
				$sql.= " ORDER BY o.updatetime DESC limit ".($pindex - 1) * $psize . ',' . $psize;
			}

			$list 	= mysqld_selectall ( $sql );

			$total = mysqld_select("SELECT FOUND_ROWS() as total;");
			$pager = pagination($total['total'], $pindex, $psize);
			include page ( 'share_order_list' );

			break;
		}



}

/**
 * 助力订单处理申请后，发送IM消息
 * 
 * @param $openid:用户ID
 * @param $ordersn:订单编号
 * @param $order_fee:减免的助力金额
 * @param $time:提交时间
 * 
 */
function pushImByApplyProcess($openid,$ordersn,$order_fee,$time)
{
	$objOpenIm = new OpenIm();
	
	$datetime = date('Y-m-d H:i:s',$time);
	
	$immsg['from_user']	= IM_WEALTH_FROM_USER;
	$immsg['to_users']	= $openid;
	$immsg['context']	= "恭喜，掌门！您的订单减免申请通过啦!
	
减免金额已打入【现金余额】中。可在我的钱包中查看，支持提现到银行卡与支付宝。
	
减免金额:{$order_fee}元

提交时间:{$datetime}
	
订单编号:{$ordersn}";
	
	$objOpenIm->imMessagePush($immsg);
}