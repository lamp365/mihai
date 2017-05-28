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
        $now_time    = time();
        $goods_list  = mysqld_selectall("select * from ".table('addon7_award')." where add_jifen_change=1 and endtime<={$now_time} order by id desc");
    }
    //获取个人信息
    $member = empty($openid) ? array() : member_get($openid);
    $credit = intval($member['credit']);
    //获取默认地址
    $defaultAddress   = array();
    if($openid)
        $defaultAddress   =   mysqld_select("SELECT * FROM " . table('shop_address') . " WHERE  deleted = 0 and isdefault =1 and openid = {$openid} order by isdefault desc ");

    //兑换记录
    $changeRecorder      = array();
    if($openid)
        $changeRecorder  = mysqld_selectall("select r.*,a.title,a.logo,a.jifen_change from ".table('addon7_request')." as r left join ". table('addon7_award')." as a on a.id=r.award_id where openid={$openid} and request_type=2");

    include themePage('integral');
}else if($op == 'change'){
    $config     = mysqld_select("SELECT * FROM " . table('addon7_config') );
    if($config['open_gift_change'] == 0){
        die(showAjaxMess(1002,'积分兑换活动已结束！'));
    }
    //兑换商品
    if(empty($_GP['id'])){
        die(showAjaxMess(1002,'参数有误！'));
    }
    if(empty($openid)){
        die(showAjaxMess(1002,'您还没登录！'));
    }
    $award = mysqld_select("select * from ".table('addon7_award')." where id={$_GP['id']}");
    if(empty($award) || $award['add_jifen_change'] == 0){
        die(showAjaxMess(1002,'商品不存在！'));
    }
    $member = member_get($openid);
    if($member['credit'] < $award['jifen_change']){
        die(showAjaxMess(1002,'您的积分不足以兑换！'));
    }
    $defaultAddress   =   mysqld_select("SELECT * FROM " . table('shop_address') . " WHERE  deleted = 0 and isdefault =1 and openid = {$openid} order by isdefault desc ");
    if(empty($defaultAddress)){
        die(showAjaxMess(1004,'请您先设置收货地址'));
    }
    //扣除积分
    member_credit($openid,$award['jifen_change'],'usecredit',PayLogEnum::getLogTip('LOG_JIFEN_CHANGE_TIP'));
    //插入一个记录
    $star_num_arr = get_star_num($_GP['id'],2);
    $data = array(
        'openid'         => $openid,
        'award_id'       => $_GP['id'],
        'createtime'     => time(),
        'star_num'       => $star_num_arr['star_num'],
        'star_num_order' => $star_num_arr['star_num_order'],
        'jifen_status'   => 1,
        'request_type'   => 2,
        'realname'       => $defaultAddress['realname'],
        'mobile'         => $defaultAddress['mobile'],
        'address'        => $defaultAddress['address'],
        'city'           => $defaultAddress['city'],
        'province'       => $defaultAddress['province'],
    );
    $res  = mysqld_insert('addon7_request',$data);
    die(showAjaxMess(200,array('tit'=>'兑换成功','des'=>'系统正在审核中')));
}
