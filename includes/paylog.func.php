<?php
/**
 * Created by PhpStorm.
 * User: 刘建凡
 * Date: 2017/5/16
 * Time: 16:33
 */
////////////////////////////////个人的金额操作//////////////////////////////////////
////////////////////////////////个人的金额操作//////////////////////////////////////
/**
 * 更新 credit和experience字段  只对积分 和经验。。积分添加多少，经验就添加多少。
 * 积分操作请用 member_credit()该方法    金额操作请用member_gold()
 * 佣金操作请用 member_commisiongold()
 * @param $openid
 * @param $fee
 * @param $type  2充值积分    -2使用积分
 * @param $remark
 * @return bool
 */
function member_credit($openid, $fee, $type, $remark)
{
    $add_arr = array('2');
    $use_arr = array('-2');
    $member = member_get($openid);
    if (! empty($member['openid'])) {
        if (! is_numeric($fee)) {
            //这里直接返回，不进行更新操作 不要弹出错误信息
            return false;
        }
        if (!in_array($type,$add_arr) && !in_array($type,$use_arr) ) {
            return false;
        }else if(in_array($type,$use_arr)){
            //积分为负
            $fee = -1*floor($fee);
        }
        $data = array(
            'remark' => $remark,
            'type'   => $type,
            'fee'    =>  $fee,
            'account_fee' => $member['credit'] + $fee,
            'createtime' => TIMESTAMP,
            'openid' => $openid
        );
        mysqld_insert('member_paylog', $data);
        $credit     = max(0,$member['credit']+$fee);
        $experience = max(0,$member['experience']+$fee);
        $update     = array('credit'=>$credit);
        if($fee > 0){
            //经验不扣除，一般兑换礼品只扣积分不扣经验
            $update['experience'] = $experience;
        }

        mysqld_update('member', $update, array(
            'openid' => $openid
        ));
        return true;
    }
    return false;
}


/**
 * 更新gold字段   只对金额操作
 * 积分操作请用 member_credit()该方法    金额操作请用member_gold()
 * 佣金操作请用 member_commisiongold()
 * @param $openid
 * @param $fee      金额单位是分
 * @param $type     1充值金额  -1 使用金额
 * @param $remark
 * @param $update 1更新用户的金额 0不更新用户金额
 * 有些地方不一定要更新gold，只需要有记录。如下单后，钱是第三方的，但是会记录一个paylog,这时候不是扣除余额，不能进行更新
 * @return bool
 */
function member_gold($openid, $fee, $type, $remark,$update=1,$orderid='')
{
    $add_arr = array('1');
    $use_arr = array('-1');
    $member  = member_get($openid);
    if (! empty($member['openid'])) {
        if (! is_numeric($fee)) {
            return false;
        }
        if (!in_array($type,$add_arr) && !in_array($type,$use_arr) ) {
            return false;
        }else if(in_array($type,$use_arr)){
            //金额为负
            $fee = -1*$fee;
        }
        $data = array(
            'remark' => $remark,
            'type' => $type,
            'fee' => $fee,
            'account_fee' => $member['gold'] + $fee,
            'createtime' => TIMESTAMP,
            'openid'  => $openid,
            'orderid' => $orderid,
        );
        $gold  = max(0,$member['gold'] + $fee);
        mysqld_insert('member_paylog', $data);
        $pid = mysqld_insertid();
        if($update){
            mysqld_update('member', array( 'gold' => $gold), array(
                'openid' => $openid
            ));
        }
        return $pid;
    }
    return false;
}

/**
 * 更新 gold   只对佣金操作  会给 推荐人 计算佣金
 * 积分操作请用 member_credit()该方法    金额操作请用member_gold()
 * 佣金操作请用 member_commisiongold()
 * @param $openid          推荐人 openid
 * @param $friend_openid   下线openid
 * @param $fee    金额单位是分
 * @param $type  3 佣金收入  -3佣金扣除
 * @param $remark
 * @return bool
 */
function member_commisiongold($openid, $friend_openid,$fee, $type, $orderid='',$remark='')
{
    $add_arr = array('3');
    $use_arr = array('-3');
    $member = member_get($openid);

    if(empty($remark)){
        $friend_member =  member_get($friend_openid,'nickname');
        $remark = Lang('LOG_BUYORDER_TIP','paylog',$friend_member['nickname']);
    }

    if (! empty($member['openid'])) {
        if (! is_numeric($fee)) {
            return false;
        }
        if (!in_array($type,$add_arr) && !in_array($type,$use_arr) ) {
            return false;
        }else if(in_array($type,$use_arr)){
            //金额为负
            $fee = -1*$fee;
        }
        $data = array(
            'remark' => $remark,
            'type' => $type,
            'fee' => $fee,
            'account_fee'   => $member['freeze_gold'] + $fee,
            'createtime'    => TIMESTAMP,
            'openid'        => $openid,
            'friend_openid' => $friend_openid,
            'orderid'       => $orderid,
        );
        //以免扣掉时为负数
        $freeze_gold  = max(0,$member['freeze_gold'] + $fee);
        mysqld_insert('member_paylog', $data);
        mysqld_update('member', array( 'freeze_gold' => $freeze_gold), array(
            'openid' => $openid
        ));
        return true;
    }
    return false;
}

/////////////////////////////商铺的金额操作//////////////////////
/////////////////////////////商铺的金额操作//////////////////////
/**
 * 店铺的积分目前等待后期 具体业务在完善 目前店铺没有积分字段
 * @param $openid
 * @param $fee
 * @param $type  2充值积分    -2使用积分
 * @param $remark
 * @return bool
 */
function store_credit($sts_id, $fee, $type, $remark)
{
    $add_arr = array('2');
    $use_arr = array('-2');
    $store   = member_store_getById($sts_id,'credit');
    if (! empty($store)) {
        if (! is_numeric($fee)) {
            //这里直接返回，不进行更新操作 不要弹出错误信息
            return false;
        }
        if (!in_array($type,$add_arr) && !in_array($type,$use_arr) ) {
            return false;
        }else if(in_array($type,$use_arr)){
            //积分为负
            $fee = -1*floor($fee);
        }
        $data = array(
            'remark' => $remark,
            'type'   => $type,
            'fee'    =>  $fee,
            'account_fee' => $store['credit'] + $fee,
            'createtime'  => TIMESTAMP,
            'sts_id'      => $sts_id
        );
        mysqld_insert('member_paylog', $data);
        $credit     = max(0,$store['credit']+$fee);
        $update     = array('credit'=>$credit);
        mysqld_update('store_shop', $update, array(
            'sts_id' => $sts_id
        ));
        return true;
    }
    return false;
}


/**
 * 更新店铺金额   只对金额操作
 * @param $openid
 * @param $fee      金额单位是分
 * @param $type     1充值金额  -1 使用金额
 * @param $remark   备注信息 例：店铺租期延长
 * @param $update 1更新店铺的金额 0不更新店铺金额
 * 有些地方不一定要更新店铺金额  预留 update参数
 * @return bool
 */
function store_gold($sts_id, $fee, $type, $remark,$update=1)
{
    $add_arr = array('1');
    $use_arr = array('-1');
    $store   = member_store_getById($sts_id,'recharge_money');
    if (! empty($store)) {
        if (! is_numeric($fee)) {
            return false;
        }
        if (!in_array($type,$add_arr) && !in_array($type,$use_arr) ) {
            return false;
        }else if(in_array($type,$use_arr)){
            //金额为负
            $fee = -1*$fee;
        }
        $data = array(
            'remark' => $remark,
            'type'   => $type,
            'fee'    => $fee,
            'account_fee' => $store['recharge_money'] + $fee,
            'createtime' => TIMESTAMP,
            'sts_id'     => $sts_id,
        );
        $recharge_money  = max(0,$store['recharge_money'] + $fee);
        mysqld_insert('member_paylog', $data);
        $pid = mysqld_insertid();
        if($update){
            mysqld_update('store_shop', array( 'recharge_money' => $recharge_money), array(
                'sts_id' => $sts_id
            ));
        }
        return $pid;
    }
    return false;
}

/**
 * 更新店铺冻结金额   只对金额操作
 * @param $openid
 * @param $fee      金额单位是分
 * @param $type     1充值金额  -1 使用金额
 * @param $remark   备注信息 例：店铺租期延长
 * @param $update 1更新店铺的金额 0不更新店铺金额
 * 有些地方不一定要更新店铺金额  预留 update参数
 * @return bool
 */
function store_freeze_gold($sts_id, $fee, $type, $remark,$update=1)
{
    $add_arr = array('1');
    $use_arr = array('-1');
    $store   = member_store_getById($sts_id,'freeze_money');
    if (! empty($store)) {
        if (! is_numeric($fee)) {
            return false;
        }
        if (!in_array($type,$add_arr) && !in_array($type,$use_arr) ) {
            return false;
        }else if(in_array($type,$use_arr)){
            //金额为负
            $fee = -1*$fee;
        }
        $data = array(
            'remark' => $remark,
            'type'   => $type,
            'fee'    => $fee,
            'account_fee' => $store['freeze_money'] + $fee,
            'createtime' => TIMESTAMP,
            'sts_id'     => $sts_id,
        );
        $freeze_money  = max(0,$store['freeze_money'] + $fee);
        mysqld_insert('member_paylog', $data);
        $pid = mysqld_insertid();
        if($update){
            mysqld_update('store_shop', array( 'freeze_money' => $freeze_money), array(
                'sts_id' => $sts_id
            ));
        }
        return $pid;
    }
    return false;
}


/**
 * 获取paylog 的 icon
 * @param $data
 * @return mixed|string
 */
function get_paylog_icon($data){
    if(!empty($data['orderid'])){
        $sql = "select d.thumb as icon  from ".table('shop_order_goods')." as g left join ".table('shop_dish')." as d";
        $sql.= " on d.id = g.dishid where g.orderid={$data['orderid']}";
        $dish = mysqld_select($sql);
        $icon = download_pic($dish['icon'],100,100);
    }else{
        $icon = WEBSITE_ROOT."themes/default/__RESOURCE__/recouse/images/paylog_money.png";
    }
    return $icon;
}