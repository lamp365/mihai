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
        $fromURL = $_SERVER['HTTP_REFERER'];
        $fromURL = parse_url($fromURL);
        //只控制 小程序的 接口访问 严格memcache扩展,避免开发环境window也提示需要安装扩展
        if(!class_exists('Memcached') && $fromURL['host'] == 'servicewechat.com'){
            ajaxReturnData(0,'请安装Memcache扩展');
        }
        $this->check_devicecode();
    }

    /**
     * 参数验证
     */
    public function check_devicecode()
    {
        $allow_model = array('login','shopindex','good_detail');
        if(in_array($_GET['do'],$allow_model)){
           //允许直接通过！
            return '';
        }
        $fromURL= $_SERVER['HTTP_REFERER'];
        $fromURL= parse_url($fromURL);
        if($fromURL['host'] != 'servicewechat.com'){
            //不属于 小程序访问的  属于直接浏览器 调试接口的
            $_REQUEST['device_code'] = get_sessionid();
        }

        if(empty($_REQUEST['device_code'])){
            ajaxReturnData(0,'未获取到设备号！');
        }

        if($fromURL['host'] != 'servicewechat.com'){
            //不属于 小程序访问的  属于直接浏览器 调试接口的
            $memInfo = get_member_account();
        }else{
            $memc = new \Mcache();
            $memInfo = $memc->get($_REQUEST['device_code']);
        }

        if(empty($memInfo)){
            //让前端重新登录
            ajaxReturnData(5,'用户已过期,刷新页面再试！');
        }
    }
}