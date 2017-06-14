<?php
         if ( !empty($_GP['accout']) && !checksubmit("submit") ){
                        $Recive_Phone_Number= $_GP['accout'];
						$URL='http://userinterface.vcomcn.com/Opration.aspx';
						$pwd = strtoupper(md5("ab8888"));
						$account="mslsw";
						$ctime=date("Y-m-d h:i:s",time());
						//要发送的内容
                       // mt_srand((double) microtime() * 1000000);
		               // $_code = str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);    
	                   // setcookie($Recive_Phone_Number, $_code, time()+ 600);
						//发送的POST数据
						//include "includes/TopSdk.php";
						date_default_timezone_set('Asia/Shanghai'); 
						$code = set_sms_code($Recive_Phone_Number,0,1);
						$regedits = new LtCookie();
                        $regedits->setCookie($Recive_Phone_Number, $code);
				echo 'yes';
				exit;
        }
		if (checksubmit("submit")) {
			$member = mysqld_select("SELECT * FROM ".table('member')." where mobile=:mobile ", array(':mobile' => $_GP['mobile']));
			$regedits = new LtCookie();
            $mobile = $regedits->getCookie($_GP['mobile']);
			if( !isset($mobile) || ($_GP['mobilecode'] != $mobile))
			{
				    message('手机验证码输入错误！','refresh','error');	
			}	
			if(empty($member['openid']))
			{
					message($_GP['mobile']."不存在。");	
			}
				if(empty($_GP['mobile']))
			{
					message("请输入手机号！");	
			}
		if(empty($_GP['third_login'])){
				if(empty($_GP['pwd']))
				{
						message("请输入密码！");	
				}
				$pwd=encryptPassword($_GP['pwd']);
		}else{
			    $pwd='';
		}
		$data = array(
                    'pwd' => $pwd
		);
			   mysqld_update('member', $data, array('mobile' => $_GP['mobile']));
			  message('修改成功！', to_member_loginfromurl(), 'success');
		}
			
		include themePage('repassword');