<?php
        $member=get_member_account(false);
		if ( $member ){
               $_users = mysqld_select('SELECT * FROM' . table('weixin_mess'). " WHERE openid = :openid", array(':openid' => $member['openid'] ));
		}
        function check_verify($verify)
     	{
		if(strtolower($_SESSION["VerifyCode"])==strtolower($verify))
		{
			unset($_SESSION["VerifyCode"]);
			return true;
		}
		return false;
	   }
         if ( !empty($_GP['accout']) && !checksubmit("submit") ){
                        $Recive_Phone_Number= $_GP['accout'];
						$URL='http://userinterface.vcomcn.com/Opration.aspx';
						$pwd = strtoupper(md5("ab8888"));
						$account="mslsw";
						$ctime=date("Y-m-d h:i:s",time());
						//发送的POST数据
						//include "includes/TopSdk.php";
						date_default_timezone_set('Asia/Shanghai'); 	
						$code = set_sms_code($Recive_Phone_Number);
						$regedits = new LtCookie();
                        $regedits->setCookie($Recive_Phone_Number, $code);
						echo 'yes';
						/*
						$c = new TopClient;
						$c->appkey = '23364190';
						$c->secretKey = 'f40319d910d5e7cef4811f99f8e4ea17';
						//您的${product}验证码：${code}，10分钟内有效，感谢您的支持！
						$req = new AlibabaAliqinFcSmsNumSendRequest;
						$req->setSmsType("normal");
						$req->setSmsFreeSignName("小物社区");
						$req->setSmsParam("{\"code\":\"{$_code}\",\"product\":\"小物社区\"}");
						$req->setRecNum("$Recive_Phone_Number");
						$req->setSmsTemplateCode("SMS_9625259");
						$resp = $c->execute($req);
						echo 'yes';
						*/
				exit;
         }
	     if(empty($cfg['shop_openreg']))
			{
					message("商城已关闭注册");	
			}
		
		
		if (checksubmit("submit")) {
			$member = mysqld_select("SELECT * FROM ".table('member')." where mobile=:mobile ", array(':mobile' => $_GP['mobile']));
			$regedits = new LtCookie();
            $mobile = $regedits->getCookie($_GP['mobile']);
			if( !isset($mobile) || ($_GP['mobilecode'] != $mobile))
			{
				message('手机验证码输入错误！','refresh','error');	
			}	
			if(!empty($member['openid']))
			{
					message($_GP['mobile']."已被注册。");	
			}
				if(empty($_GP['mobile']))
			{
					message("请输入手机号！");	
			}
		if(empty($_GP['third_login']))
			{
					if(empty($_GP['pwd']))
				{
						message("请输入密码！");	
				}
				$pwd=md5($_GP['pwd']);
		}else{
			$pwd='';
		}
		  $shop_regcredit=intval($cfg['shop_regcredit']);
		  $openid=date("YmdH",time()).rand(100,999);
		  $hasmember = mysqld_select("SELECT * FROM " . table('member') . " WHERE openid = :openid ", array(':openid' => $openid));
			if(!empty($hasmember['openid']))
			{
				$openid=date("YmdH",time()).rand(100,999);
			}
			//推荐人openid 用于PC分享 注册后每次都能得到佣金，相当于app的开店
			$recommend_openid = getOpenshopSellerOpenid();
			if(empty($recommend_openid)){
				//从活动分享中获取推荐人的openid
				$recommend_openid = getShareOpenidFromCookie();
			}
			$data = array(
					'mobile' => $_GP['mobile'],
                    'pwd'    => $pwd,
                    'createtime'       => time(),
                    'status'           => 1,
                    'istemplate'       =>0,
                    'experience'       => 0 ,
				    'mess_id'          => !empty($_users['mess_id'])?$_users['mess_id']:0 ,
                    'openid'           =>$openid,
                    'recommend_openid' => empty($recommend_openid)? '' : $recommend_openid,
			);
				mysqld_insert('member', $data);
			    //她的觅友加1
			    recommend_frend_count($recommend_openid);
				if(!empty($shop_regcredit))
				{
				    member_credit($openid,$shop_regcredit,"addcredit","注册系统赠送积分");
				}
			   // 老用户积分赠送
			   $regular = mysqld_select("SELECT * FROM ".table('shop_customers')." WHERE mobile='".$telephone."' AND status=0");
		       if (!empty($regular)) {
					// 老用户积分为消费金额的一半
					$regular_credit = intval((float)$regular['price']/10);
					member_credit($openid, $regular_credit, 'addcredit', '尊贵天猫老客户积分赠送');
					// 该老用户更改为已入驻
					mysqld_update('shop_customers', array('status'=>1), array('mobile'=>$telephone));
				}
				$member=get_session_account();
				$oldsessionid = $member['openid'];
				$unionid      = $member['unionid'];
				$loginid=save_member_login('',$openid);

				integration_session_account($loginid,$oldsessionid, $unionid);

			    message('注册成功！', to_member_loginfromurl(), 'success');
		}
			$qqlogin = mysqld_select("SELECT * FROM " . table('thirdlogin') . " WHERE enabled=1 and `code`='qq'");
				if(!empty($qqlogin)&&!empty($qqlogin['id']))
				{
					$showqqlogin=true;
				}
		// 获取使用条款
        $use_page = getArticle(1,2);
		if ( !empty($use_page) ){
           $use_page = mobile_url('article',array('name'=>'addon8','id'=>$use_page[0]['id']));
		}else{
           $use_page = 'javascript:void(0)';
		}
		// 获取用户隐私
        $use_private = getArticle(1,3);
		if ( !empty($use_private) ){
           $use_private = mobile_url('article',array('name'=>'addon8','id'=>$use_private[0]['id']));
		}else{
           $use_private =  'javascript:void(0)';
		}
		include themePage('regedit');