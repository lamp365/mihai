<?php
/**
 * Created by PhpStorm.
 * User: 刘建凡
 * Date: 2017/4/20
 * Time: 18:39
 */

namespace shopwap\controller;
use  shopwap\controller;

class login{

    //这个值等价于$_GP
    public $request = '';

    public function __construct()
    {
        $is_login = is_login_account();
        if ( $is_login ){
            header("location:" . to_member_loginfromurl());
        }

    }


    //没有op默认显示 index
    public function index()
    {
        $showqqlogin = false;
        $qqlogin = mysqld_select("SELECT * FROM " . table('thirdlogin') . " WHERE enabled=1 and `code`='qq'");
        if ( ! empty($qqlogin['id'])) {
            $showqqlogin = true;
        }
        include themePage('login');
    }
    
    
    function resetPasswordByPhone()
    {
        $_GP = $this->request;
        !$_GP['mobilecode'] && ajaxReturnData(0,'请输入验证码');
        !$_GP['mobile'] && ajaxReturnData(0,'请输入手机号');
        !$_GP['pwd'] && ajaxReturnData(0,'请输入新密码');
        
		$loginService = new \service\shopwap\loginService();
		$res = $loginService->resetPasswordByPhone( $_GP );
        $res===false && ajaxReturnData(0,$loginService->getError(),array( 'error_location'=>$loginService->getErrorLocation() ) );
       
		//先检测数据
        ajaxReturnData(1,  LANG('COMMON_OPERATION_SUCCESS') );
    }

    //表单提交 操作登录
    public function do_login()
    {
        $_GP = $this->request;

        $loginService = new \service\shopwap\loginService();
        $res = $loginService->do_login($_GP);
        if($res){
            $url =   to_member_loginfromurl();
            checkIsAjax()?ajaxReturnData(1, LANG('COMMON_LOGIN_SUCCESS'),array('url'=>$url)): message(LANG('COMMON_LOGIN_SUCCESS'),$url,'success');
        }else{
             checkIsAjax()?ajaxReturnData(0,$loginService->getError(),array('error_location'=>$loginService->getErrorLocation())):
                 message($loginService->getError(),refresh(),'error');
        }
    }


}