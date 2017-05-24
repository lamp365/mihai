<?php
	if(isAgentAdmin()){
		message('对不起，你是业务员身份，没权限修改用户信息');
	}
	$member 	 = mysqld_select('SELECT * FROM '.table('member').' where openid=:openid', array(':openid' => $_GP['openid']));
	$weixininfo  = mysqld_select('SELECT * FROM '.table('weixin_wxfans').' where openid=:openid', array(':openid' => $_GP['openid']));
    $bonuscount  = mysqld_selectcolumn("select count(bonus_user.bonus_id) from " . table("bonus_user")." bonus_user left join  " . table("bonus_type")." bonus_type on bonus_type.type_id=bonus_user.bonus_type_id where bonus_user.deleted=0  and `openid`=:openid order by isuse,bonus_type.send_type ",array(':openid'=> $_GP['openid']));


     if (checksubmit('submit')) {

			/*  前端已经把 手机号 disabled 不可更改了 这里接收不到值了 也不用验证
			 * if($member['mobile']!=$_GP['mobile'])
			{
			
				$checkmember = mysqld_select('SELECT * FROM '.table('member').' where mobile=:mobile', array(':mobile' => $_GP['mobile']));
		 		if(!empty($checkmember['openid']))
		 		{
					message($_GP['mobile']."已被注册。");	
				}
			}*/
			 if($_GP['parent_roler_id'] != 0 && $_GP['son_roler_id']==0){
				 message('对不起，会员身份选择有误！',refresh(),'error');
			 }
		 	$url  = empty($_GP['platform_url']) ? '' : $_GP['platform_url'];
			$datas = array(
				'realname'=> $_GP['realname'],
				'email'   => $_GP['email'],
				'relation_uid'    => empty($_GP['relation_uid']) ? 0 : $_GP['relation_uid'],
				'parent_roler_id' => empty($_GP['parent_roler_id']) ? 0 : $_GP['parent_roler_id'],
				'son_roler_id'    => empty($_GP['son_roler_id']) ? 0 : $_GP['son_roler_id'],
				'platform_name'   => $_GP['platform_name'],
				'QQ'=>$_GP['QQ'],
		         'weixin'=>$_GP['weixin'],
		         'wanwan'=>$_GP['wanwan'],
				'platform_url'    => $url
			);

		 	if(!empty($_GP['picurl'])){
				$datas['platform_pic'] = implode(',',$_GP['picurl']);
			}

	     	if(!empty($_GP['password']))
	     	{
	     			if($_GP['password']==$_GP['repassword'])
			     	{
			     		$datas['pwd']=md5($_GP['password']);
			     	}else
			     	{
			     		
			     		message("两次密码不相同");
					}
	     		
	     	}
		     mysqld_update('member', $datas, array('openid' => $_GP['openid']));
		 	 if(!empty($_GP['parent_roler_id'])){
				 //获取聚到商店铺二维码
				 get_weixin_erweima($_GP['openid']);
			 }
		     message('操作成功！', 'refresh', 'success');
	 }


	//找出业务员
	$rolers   = mysqld_select("select id,name,createtime from ".table('rolers')." where type=1 and isdelete=0");
	//业务员对应的管理员都有哪些
	$user_rolers  = '';
	if(!empty($rolers)){
		$sql = "select r.id,r.rolers_id,r.uid,u.username from ".table('rolers_relation')." as r ";
		$sql .= " left join ".table('user')." as u on u.id=r.uid where r.rolers_id={$rolers['id']}";
		$user_rolers = mysqld_selectall($sql);
	}

	//渠道商身份角色
	$purchase = mysqld_selectall("select id,pid,name,createtime from ".table('rolers')." where type<>1 order by pid asc");
	if (! empty($purchase)) {
		$childrens = '';
		foreach ($purchase as $key => $item) {
			if (! empty($item['pid'])) {
				$childrens[$item['pid']][$item['id']] = $item;
				unset($purchase[$key]);
			}
		}
	}


    include page('detail');