<?php
// +----------------------------------------------------------------------
// | WE CAN DO IT JUST FREE
// +----------------------------------------------------------------------
// | Copyright (c) 2015 http://www.squdian.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 小物社区 <QQ:119006873> <http://www.squdian.com>
// +----------------------------------------------------------------------
	$id=	$_CMS['account']['id'];
		 					 $username=	$_CMS['account']['username'];
		 if (checksubmit('submit')) {
		$account = mysqld_select('SELECT * FROM '.table('user')." WHERE  id = :id and password=:password" , array(':id' => $id,':password'=> md5($_GP['oldpassword'])));
		
		if(!empty($account['id']))
		{
			if(empty($_GP['newpassword']))
			{
				
					message('新密码不能为空！','refresh','error');	
			}
			
			if($_GP['newpassword']!=$_GP['confirmpassword'])
			{
				
					message('两次密码不一致！','refresh','error');	
				
			}
			$data= array('password'=> md5($_GP['newpassword']));
			 mysqld_update('user', $data, array('id' => $account['id']));
					message('密码修改成功！',create_url('site',array('name' => 'index','do' => 'changepwd')),'succes');	
		}else
		{
			message('密码错误！','refresh','error');	
		}
		 	
		}
			include page('changepwd');