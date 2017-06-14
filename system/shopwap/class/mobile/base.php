<?php
/**
 * Created by PhpStorm.
 * User: 刘建凡
 * Date: 2017/4/19
 * Time: 11:52
 * 基类，用于操作一些父类的东西 如登录授权  权限控制
 */
namespace shopwap\controller;


class base extends \common\controller\basecontroller{

    public $rule_ids   = '';
    public $other_rule = '';
}