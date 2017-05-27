<?php
/**
 * Created by PhpStorm.
 * User: 刘建凡
 * Date: 2017/4/7
 * Time: 18:29
 * demo
 * service 层 用于简化 我们的控制器，让控制器尽量再 简洁
 * 把一些业务提取出来，放在service层中去操作
$a = new \service\testService();
if($a->todo()){
    //操作成功 则继续业务
}else{
    message($a->getError());
}
 */
namespace service;

class testService extends \service\publicService
{
    public  function todo($name = '')
    {
        //操作业务 返回true  错误则返回false 并且定义 error
        if(empty($name)){
            $this->error = '没有定义名字';
            return false;
        }else{
            return true;
        }
    }
}