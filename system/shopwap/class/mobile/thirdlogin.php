<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 17/7/15
 * Time: 上午10:56
 */
namespace shopwap\controller;

class thirdlogin extends \common\controller\basecontroller
{
    public $config;
    public $oauth;
    public $class_obj;

    public function _init(){
        $_GP = $this->request;
        $this->oauth = $_GP['oauth'];  //标识  微信还是QQ

        if(empty($_GP['oauth']) || !in_array($_GP['oauth'],array('weixin','qq'))){
            message('非法操作!',refresh(),'error');
        }
        //获取配置
        $data = mysqld_select("SELECT * FROM " . table('thirdlogin') . " WHERE enabled=1 and `code`='{$_GP['oauth']}'");
        $this->config = unserialize($data['configs']); // 配置反序列化
        if(empty($data))
            message("{$_GP['oauth']}登录暂时关闭",refresh(),'error');

        //实例化对应的登陆插件
        if($this->oauth == 'qq')
            $this->class_obj = new \service\shopwap\qqloginService($this->config);
        else if($this->oauth == 'weixin')
            $this->class_obj = new \service\shopwap\weixinloginService($this->config);
    }

    public function login(){
        $this->_init();
        $this->class_obj->login();
    }

    public function callback(){
        $this->_init();
        $data = $this->class_obj->respon();
        $this->class_obj->thirdUserLogin($data);
        $url  = to_member_loginfromurl();
        message('登录成功',$url,'success');
    }
}