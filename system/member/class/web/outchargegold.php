<?php
	$op = $_GP['op']?$_GP['op']:'display';
	$status = intval($_GP['status']);
 	if($op=='display')
	{
		$list = mysqld_selectall("SELECT teller.*,member.nickname,member.mobile FROM ".table('gold_teller')." teller  left join " . table('member') . " member on teller.openid=member.openid where teller.status=:status order by teller.createtime desc",array('status'=>$status));
		foreach($list as &$one){
			//查找店铺信息
			if(!empty($one['sts_id'])){
				$store = member_store_getById($one['sts_id'],'sts_name');
				$one['sts_name'] = $store['sts_name'];
			}else{
				$one['sts_name'] = '';
			}
		}

		include page('outchargegold');
		exit;
	}

	if($op=='post')
	{
		$id=intval($_GP['id']);
		$gold_teller = mysqld_select("SELECT teller.* FROM ".table('gold_teller')." teller where teller.status=0 and id=:id",array(':id'=>$id));
		if(empty($gold_teller)){
			message('记录不存在！',refresh(),'error');
		}
		$res   = mysqld_update('gold_teller',array('status'=>intval($_GP['tostatus'])),array('id'=>$id));
		$money = $gold_teller['draw_money'] + $gold_teller['fee'];
		if($res){
			if(intval($_GP['tostatus'])==-1) {
				//审核失败，退回钱
				if(!empty($gold_teller['sts_id'])){
					//退回店铺   不用再次打下 paylog
					mysqld_query("update ".table('store_shop')." set recharge_money=recharge_money+{$money} where sts_id={$gold_teller['sts_id']}");
				}else{
					//退给个人
					mysqld_query("update ".table('member')." set gold=gold+{$money} where openid='{$gold_teller['openid']}'");
				}
				//把账单记录也更新过来
				mysqld_update('member_paylog',array('check_step'=>2),array('cash_id'=>$id));
			}else if(intval($_GP['tostatus'])== 1){
				//把账单记录也更新过来
				mysqld_update('member_paylog',array('check_step'=>3),array('cash_id'=>$id));
			}
			mysqld_update('gold_teller',array('modifytime'=>time()),array('id'=>$id));
			message("审核完成！",'refresh','success');
		}else{
			message('操作失败！',refresh(),'error');
		}
	}