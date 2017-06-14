<?php
/**
 * Created by PhpStorm.
 * User: 刘建凡
 * Date: 2017/4/20
 * Time: 18:39
 */

namespace api\controller;
use  api\controller;

class login extends homebase{

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
    
        ajaxReturnData(1,  LANG('COMMON_OPERATION_SUCCESS') );
    }

    //表单提交 操作登录
    public function do_login()
    {
        $_GP = $this->request;

        $loginService = new \service\shopwap\loginService();
        $member_info = $loginService->do_login($_GP);//返回的是用户信息

        if(!$member_info){
            ajaxReturnData(0,$loginService->getError(),array('error_location'=>$loginService->getErrorLocation()));
        }
        $member_info['seller_roler'] = checkSellerRoler();
        $store_data   = $loginService->getStoreData($member_info);
        ajaxReturnData(1, LANG('COMMON_LOGIN_SUCCESS'),
            array(
                'app_key'       => $member_info['app_key'],
                'id'            => $member_info['store_sts_id'],
                'register_step' => is_array($store_data['store_info']) ? $store_data['store_info']['sts_info_status'] : -1, //已注册会员，但是未注册商铺，给APP -1
                'sts_shop_type' => is_array($store_data['store_info']) ? $store_data['store_info']['sts_shop_type'] : 0,
                'store'         => $store_data['store_info'],
                'store_identity'=> $store_data['store_identity'],
                'member'        => $member_info,
            )
        );
    }

    /**
     * 退出操作
     */
    public function logout()
    {
        $_GP    = $this->request;
        $d_code = $_GP['device_code'];
        $mcache = new \Mcache();
        $mcache->del_msession($d_code);
        ajaxReturnData(1,"登陆信息删除成功!");
    }


}