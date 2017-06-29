<?php
/**
 * Created by PhpStorm.
 * User: 刘建凡
 * Date: 2017/5/10
 * Time: 18:16
 */
namespace api\controller;

class homebase extends \common\controller\basecontroller
{
    public function __construct()
    {
        parent::__construct();
        //检验数据签名校验
        parent::checkAppSign();
    }
}