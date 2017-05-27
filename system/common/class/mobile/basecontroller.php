<?php
/**
 * Created by PhpStorm.
 * User: 刘建凡
 * Date: 2017/4/22
 * Time: 13:18
 */
namespace common\controller;


class basecontroller {
    //这个值等价于$_GP
    public $request = '';

    public function __construct() {
       ##what to do  如后期可以引入 vendor 组件化
    }
    /**
     * 没有定义op则默认显示index
     */
    public function index()
    {
        $name = $this->request['name'];
        $do = $this->request['do'];
        $op = $this->request['op'];
        if(DEVELOPMENT){
            die("请在{$name}/{$do}中定义个index的方法");   //开发环境提示错误
        }else{
            die("请在{$name}/{$do}中定义个index的方法");  //线上环境可以引导到报错友好页面
        }
    }


    //用于显示 空方法 或者控制器不存在
    public function _baseEmpty($msg)
    {
        if(DEVELOPMENT){
            die($msg);   //开发环境提示错误
        }else{
            die('ContRoller Not Existed');  //线上环境可以引导到报错友好页面
        }
    }

    /**
     * app数据校验签名
     */
    public function checkAppSign()
    {
        //app接口访问暂停时
        if(API_MAINTAIN) {
            ajaxReturnData(0,'亲，服务器维护中，请稍等');
        }

        if(is_mobile_request()){
            if(empty($_REQUEST['device_code'])){
                ajaxReturnData(0,'未获取到设备号！');
            }
        }else{
            //方便 pc端直接不用传值 可以调数据
            $_REQUEST['device_code'] = $_REQUEST['device_code'] ?: session_id();
        }
        //不是debug模式下
        if(!API_DEBUG)
        {
            $objRsa 		= new \Rsa();			//RSA 加解密已签名验证相关类对象
            $url_parts 		= parse_url($_SERVER['REQUEST_URI']);
            /**
            Array(
            [dirname] => /api/shopruler
            [basename] => userlist.html
            [extension] => html
            [filename] => userlist
            )
             */
            $url_filename 	= pathinfo($url_parts['path']);
            $url_filename 	= $url_filename['filename'];   //得到请求的方法

            //RSA加密的token为空时
            if(empty($_REQUEST['token']))
            {
                ajaxReturnData(0,'访问请求不被允许');
            }
            //签名串为空时
            elseif(empty($_REQUEST['sign']))
            {
                ajaxReturnData(0,'此访问请求不被允许');
            }
            else{
                //待签名数据
                $signToken = $objRsa->public_decrypt(trim($_REQUEST['token']));

                if(empty($signToken))
                {
                    ajaxReturnData(0,'token加密有误！');
                }
                elseif($signToken!=$url_filename)
                {
                    ajaxReturnData(0,'签名不匹配！');
                }
                //签名验证不通过时
                elseif(!$objRsa->getSignVerify($signToken,$_REQUEST['sign']))
                {
                    ajaxReturnData(0,'签名信息有误！');
                }
            }
        }
        return true;
    }
}