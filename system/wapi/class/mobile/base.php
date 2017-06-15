<?php
/**
 * Created by PhpStorm.
 * User: 刘建凡
 * Date: 2017/4/20
 * Time: 18:39
 */

namespace wapi\controller;


class base extends \common\controller\basecontroller
{
    public function __construct()
    {
        parent::__construct();
        //小程序不支持 session  cookie
        if(!class_exists('Memcached')){
            ajaxReturnData(0,'请安装Memcache扩展');
        }
    }

    /**
     * app数据校验签名
     */
    public function check_devicecode()
    {
        if($_GET['do'] == 'login' && $_GET['op'] == 'index'){
            //登陆操作 没有设备号  小程序中的设备号是服务端在登录通信后生成的
            return '';
        }
        if(is_mobile_request()){
            if(empty($_REQUEST['device_code'])){
                ajaxReturnData(0,'未获取到设备号！');
            }
        }else{
            //方便 pc端直接不用传值 可以调数据
            $_REQUEST['device_code'] = $_REQUEST['device_code'] ?: session_id();
        }

        //从memcahe中获取信息
        $device_code = $_REQUEST['device_code'];
        $mcache   = new \Mcache();
//        get_member_account()
        $userinfo = $mcache->get($device_code);
    }
}