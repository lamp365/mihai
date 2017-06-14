<?php

		/*	
			if(!$this->check_verify($_GP['verify']))
			{
				message('验证码输入错误！','refresh','error');	
			}
			*/
		if(empty($_GP['username']) || empty($_GP['password'])){
			message('账户或者密码不能为空！','refresh','error');
		}
		$account = mysqld_select(
			'SELECT * FROM '.table('user')." WHERE  username = :username" ,
			array(':username' => $_GP['username'])
		);
		if(empty($account)){
			message('账户不存在！','refresh','error');
		}else{
			if(encryptPassword($_GP['password']) != $account['password']){
				message('密码错误！','refresh','error');
			}

			$_SESSION["account"]=$account;
			checkAddons();
			header("location:".create_url('site', array('name' => 'public','do' => 'index')));
		}
