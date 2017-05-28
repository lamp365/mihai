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
	}

	//发送注册短信
	public function regedit_sms()
	{
		$_GP = $this->request;
		$loginService = new \service\shopwap\loginService();
		$res = $loginService->do_regeditSms($_GP['accout']);
		if($res){
			ajaxReturnData(1,'发送成功！');
		}else{
			ajaxReturnData(0,$loginService->getError());
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
			message($loginService->getError(),'','error');
		}

		//开始注册
		$res = $loginService->do_signin($_GP);
		if($res){
			message('注册成功！',to_member_loginfromurl(),'success');
		}else{
			message($loginService->getError(),'','error');
		}

	}
}