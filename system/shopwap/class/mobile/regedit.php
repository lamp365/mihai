<?php
/**
 * Created by PhpStorm.
 * User: 刘建凡
 * Date: 2017/4/20
 * Time: 18:39
 */

namespace shopwap\controller;
use  shopwap\controller;

class regedit{

	//这个值等价于$_GP
	public $request = '';

	//没有op默认显示 index
	public function index()
	{
		$cfg    = globaSetting();
		if(empty($cfg['shop_openreg'])) {
			message("商城已关闭注册");
		}

		$showqqlogin = false;
		$qqlogin = mysqld_select("SELECT * FROM " . table('thirdlogin') . " WHERE enabled=1 and `code`='qq'");
		if(!empty($qqlogin['id'])) {
			$showqqlogin = true;
		}

		if(is_mobile_request()){
			include Page('wap_regedit');
		}else{
			include Page('regedit');
		}
	}

	//发送注册短信
	public function regedit_sms()
	{
		$_GP = $this->request;
        $existMemberValidate =$_GP['is_already_member']==1?1:$_GP['is_already_member'];//已经注册的用户用来接收手机验证码
        
		$loginService = new \service\shopwap\loginService();
		$res = $loginService->do_regeditSms($_GP['accout'],$existMemberValidate);
		if($res){
			ajaxReturnData(1,LANG('COMMON_SMS_SEND_SUCCESS'));
		}else{
			ajaxReturnData(0,$loginService->getError(),array( 'error_location'=>$loginService->getErrorLocation()));
		}
	}

	//提交表单注册
	public function signin()
	{
		$_GP = $this->request;

		$loginService = new \service\shopwap\loginService();
		//先检测数据
		$res = $loginService->do_checksignin($_GP);
		if(!$res){
            checkIsAjax()
                ?ajaxReturnData(0, $loginService->getError(),array('error_location'=>$loginService->getErrorLocation()))
                :message($loginService->getError(),'','error');
		}

		//开始注册
		$res = $loginService->do_signin($_GP);
		if($res){
			message(LANG('COMMON_SIGNIN_SUCCESS'),to_member_loginfromurl(),'success');
		}else{
			message($loginService->getError(),'','error');
		}

	}
}