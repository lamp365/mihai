<?php
/**
 * Created by PhpStorm.
 * User: 刘建凡
 * Date: 2017/4/19
 * Time: 11:52
 * 基类，用于操作一些父类的东西 如登录授权  权限控制
 */
namespace seller\controller;


class base extends \common\controller\basecontroller{

    public $rule_ids   = '';
    public $other_rule = '';

    public function __construct() {
        parent::__construct();
        //验证卖家身份
        checkSellerLoginStatus();
        //验证卖家的权限规则
       $this->checkSellerRule();
    }

    public function checkSellerRule()
    {
        $ruleService = new \service\seller\shoprulerService();
        $rule_info   = $ruleService->sellerHasRule();
        if(empty($rule_info)){
            if(checkIsAjax()){
                ajaxReturnData(0,'您没有权限访问！','');
            }else{
                message('您没有权限操作！',mobile_url('main'),'error');
            }
        }
        //权限已经改为逆向思维  如果查出有规则id  则反而是需要被限制操作的
        $this->rule_ids    = $rule_info['rule_ids'];
        $this->other_rule  = $rule_info['other_rule'];

        $memInfo = get_member_account();
        if($memInfo['store_is_admin']){
            //管理员不用权限限制
            return '';
        }

        //根据访问的url 获取规则
        $url = $_GET['name'].'/'.$_GET['do'].'/'.$_GET['op'];
        $url = rtrim($url,'/');
        //白名单  一些地址允许访问
        $allow_visted = \MenuEnum::$allowSellerToVisted;
        if(in_array($url,$allow_visted)){
            //允许用户访问
            return '';
        }
        $this_rule_id  = $ruleService->findRuleByUrl($url);
        if(empty($this_rule_id)){
//            message($ruleService->getError(),mobile_url('main'),'error');
            //没有找到允许访问
            return '';
        }

        //测该规则id是否存在于分组拥有的权限里  存在则是需要限制的操作
        if(in_array($this_rule_id,$this->rule_ids)){
            if(checkIsAjax()){
                ajaxReturnData(0,'您没有权限访问！','');
            }else{
                message('您没有权限操作！',mobile_url('main'),'error');
            }
        }

    }
    public function getLeftMenu()
    {
        //菜单
        $ruleService = new \service\seller\shoprulerService();
        $menudata    = $ruleService->getAllRule();
        $menulist    = array();
        $memInfo = get_member_account();
        if(empty($memInfo['store_is_admin'])){
            //不是管理员 需要验证权限  存在权限组里 就删掉 说明需要被禁用
            foreach($menudata as $key => $one_menu){
                if(in_array($one_menu['rule_id'],$this->rule_ids)){
                    unset($menudata[$key]);
                }
            }
        }
        shopCategoryTree($menulist,$menudata);
        return $menulist;
    }

}