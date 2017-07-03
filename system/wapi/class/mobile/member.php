<?php
/**
 * Created by PhpStorm.
 * User: 刘建凡
 * Date: 2017/6/16
 * Time: 13:48
 */
namespace wapi\controller;

class member extends base
{
    public function __construct()
    {
        parent::__construct();
        if(!checkIsLogin()){
            ajaxReturnData(0,'请授权登录！');
        }
    }
    /**
     * 获取个人小程序推广二维码
     */
    public function get_qrcode()
    {
        $member =  get_member_account();
        $weixin = new \WeixinTool();
        $result = $weixin->get_xcx_erweima($member['openid'],2);
        if($result['errno'] == 0){
            ajaxReturnData(0,$result['message']);
        }else{
            ajaxReturnData(1,$result['message']);
        }
    }

}