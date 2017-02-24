<?php
$op           = empty($_GP['op'])? 'display' : $_GP['op'];
$openid       = checkIsLogin();
if($op == 'display'){
    //记住当前地址
    tosaveloginfrom();
    //查找出有兑换礼品的商品
    $config     = mysqld_select("SELECT * FROM " . table('addon7_config') );
    $goods_list = array();
    if($config['open_gift_change'] == 1){
        $goods_list  = mysqld_selectall("select * from ".table('addon7_award')." where add_jifen_change=1 order by id desc");
    }
    //获取个人信息
    $member = empty($openid) ? array() : member_get($openid);
    $credit = intval($member['credit']);
    //获取默认地址
    $defaultAddress   = array();
    if($openid)
        $defaultAddress   =   mysqld_select("SELECT * FROM " . table('shop_address') . " WHERE  deleted = 0 and isdefault =1 and openid = {$openid} order by isdefault desc ");


    include themePage('integral');
}else if($op == 'change'){
    //兑换商品
    if(empty($_GP['id'])){
        die(showAjaxMess(1002,'参数有误！'));
    }
    if(empty($openid)){
        die(showAjaxMess(1002,'您还没登录！'));
    }
    $award = mysqld_select("select * from ".table('addon7_award')." where id={$id}");
    if(empty($award) || $award['add_jifen_change'] == 0){
        die(showAjaxMess(1002,'商品不存在！'));
    }
    $member = member_get($openid);
    if($member['credit'] < $award['jifen_change']){
        die(showAjaxMess(1002,'您的积分不足以兑换！'));
    }
    //扣除积分
    member_credit($openid,$award['jifen_change'],'usecredit',"积分兑换商品扣除{$award['jifen_change']}分");
    //插入一个记录
}
