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
}