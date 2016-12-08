<?php
$member=get_vip_member_account(true,true);
$openid =$member['openid'] ;
$memberinfo=member_get($openid);
$op = $_GP['op'];

if ( $op == 'list' ){
	if ( isset($_GP['type']) && !empty($_GP['type'])){
		 $type = $_GP['type'];
		 if ( $type == 2 ){
			 $title = '积分明细';
			 $keys = 'credit';
             $paytype = ' and (type = "addcredit" or type = "usecredit") ';
		 }else{
			 $title = '余额清单';
			 $keys = 'gold';
             $paytype = ' and (type = "addgold" or type = "usegold") ';
		 }
		 $list = mysqld_selectall('SELECT * FROM '.table('member_paylog').' WHERE openid = '.$openid.' '.$paytype.' order by createtime desc');
		 foreach ( $list as &$paylist ){
               $paylist['createtime'] = date('Y-m-d H:i:s', $paylist['createtime']);
		 }
         include themePage('purchase_property');
	}
}else{
           if ( strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false ) {
              $weixinthirdlogin = mysqld_select("SELECT * FROM " . table('thirdlogin') . " WHERE enabled=1 and `code`='weixin'");
              if(!empty($weixinthirdlogin)&&!empty($weixinthirdlogin['id'])){
	                $isweixin=true;
					$weixin_openid=get_weixin_openid();
			   }
			}
			if (checksubmit("submit")) {
				    if ( !empty( $_GP['outgold_bankcardcode'] ) ){
						$bank_check = bankInfo($_GP['outgold_bankcardcode']);
						if ( !$bank_check ){
							 message('请输入正确的银行卡号码');
						}
					}
					$outgoldinfo=array(
						'outgold_paytype'=>$_GP['outgold_paytype'],
						'outgold_bankname'=>$bank_check,
						'outgold_bankcardname'=>$_GP['outgold_bankcardname'],
						'outgold_bankcardcode'=>$_GP['outgold_bankcardcode'],
						'outgold_alipay'=>$_GP['outgold_alipay'],
						'outgold_weixin'=>$_GP['outgold_weixin']
					);
					if ( !empty($_GP['email']) ){
						$objValidator	= new Validator();
						if(!$objValidator->is($_GP['email'],'email')){
							   message('请输入正确的邮箱地址');
						}
					}
					$data = array(
						'realname' => $_GP['realname'],
                    	'email' => $_GP['email'],
						'outgoldinfo'=>serialize($outgoldinfo)
					);

				mysqld_update('member', $data,array('openid'=>$openid));
			
			    message('资料修改成功！', mobile_url('purchase_member'), 'success');
			  
			}
		   include themePage('purchase_member');
}