<?php
/**
 * Created by PhpStorm.
 * User: 刘建凡
 * Date: 2017/3/9
 * Time: 14:56
 * 不在使用   统一使用 ｌａｎｇｕａｅ下面的语言包 操作 paylog
 */
class PayLogEnum{

    public static $LogEnumValues = array(
        
        'LOG_REGISTER_TIP'        => '邀请@好友注册奖励',
        'LOG_INVITE_TIP'          => '邀请@好友助力免单奖励',
        'LOG_BUYORDER_TIP'        => '好友@下单佣金奖励',
        'LOG_FREEORDER_TIP'       => '获得@分类免单',

        'LOG_SHOPBUY_TIP'        => '购买了商品,现金支付',
        'LOG_FREE_BALANCE_TIP'   => '购买了商品,免单余额抵用',
        'LOG_BALANCE_TIP'        => '购买了商品,余额抵用',
        'LOG_INVITE_CHECK_TIP'   => '助力免单审核通过',

        'LOG_BACK_CASH_TIP'      => '退款成功至现金余额',
        'LOG_BACK_FREE_TIP'      => '退款成功至免单收入',
        'LOG_BACK_THIRD_TIP'     => '退款成功至支付账户',
        'LOG_BACK_FEE_TIP'       => '取消订单返还免单余额',

        'LOG_LOGIN_TIP'             => '登录后与临时账户合并所得余额',

        'LOG_SHOPBUY_CREDIT_TIP'    => '购买了商品',          //下单时入账积分提示
        'LOG_BACK_CREDIT_RATIO_TIP' => '好友@下单积分奖励',  //确认收货时，卖家积分奖励

        //提款
        'LOG_OUTMONEY_FAIL_TIP'    => '提现审核失败返回账户',
        
        //购买
        'LOG_BUY_SHOP_LEVEL_REMARK'    => '购买店铺等级',
        
        //LOG type类型
        'LOG_TYPE_OF_CHARGE_MONEY'  => 1,
        'LOG_TYPE_OF_USE_MONEY'     => -1,
        'LOG_TYPE_OF_CHARGE_POINT'  => 2,
        'LOG_TYPE_OF_USE_POINT'     => -2,
        'LOG_TYPE_OF_CHARGE'        => 3,
        //积分
        'LOG_REGIST_JIFEN_TIP'     => '注册系统赠送积分',
        'LOG_REGIST_VIPJIFEN_TIP'  => '天猫老客户注册赠送积分',
        'LOG_TORDER_JIFEN_TIP'     => '关联天猫订单积分奖励',
        'LOG_TORDER_MONEY_TIP'     => '天猫用户验证现金奖励',
        'LOG_SIGN_JIFEN_TIP'       => '每天签到送积分',
        'LOG_SIGN_4JIFEN_TIP'      => '连续4天签到送积分',
        'LOG_SIGN_7JIFEN_TIP'      => '连续7天签到送积分',
        'LOG_JIFEN_CHANGE_TIP'     => '积分兑换礼品',
        'LOG_JIFEN_CHANGE_FAIL_TIP'=> '积分兑换礼品审核失败',
    );


    public static function getLogTip($key,$replace = ''){
        $tip = '';
        if(array_key_exists($key,self::$LogEnumValues)){
            $tip =  self::$LogEnumValues[$key];
            if(!empty($replace)){
                $tip = str_replace('@',$replace,$tip);
            }
        }
        return $tip;
    }


}