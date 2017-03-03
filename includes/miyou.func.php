<?php
/**
 * Created by PhpStorm.
 * User: 刘建凡
 * Date: 2017/3/2
 * Time: 17:18
 */
/**
 * @param $openid
 * @param $miyou_openid
 * @param $miyouInfo   用户的member表中的基本信息
 * @return mixed
 */
function getMiyouInfo($openid,$miyou_openid,$miyouInfo){

    if(!empty($miyouInfo))
    {
        if(!empty($miyouInfo['nickname']))
            $miyouInfo['name'] = $miyouInfo['nickname'];
        else if(!empty($miyouInfo['realname']))
            $miyouInfo['name'] = $miyouInfo['realname'];
        else
            $miyouInfo['name'] = substr_cut($miyouInfo['mobile']);

        //积分等级名称
        $member_rank_model = member_rank_model($miyouInfo['credit']);
        $miyouInfo['rank_name'] = $member_rank_model['rank_name'];

        //分享该用户进来得到的奖励
        $inviteFee = mysqld_select("select fee from".table('member_paylog')." where openid={$openid} and friend_openid={$miyou_openid} and type='addgold_byinvite'" );
        $miyouInfo['invite_fee'] = (float)$inviteFee['fee'];

        //该用户带给我的佣金
        $sql = "SELECT sum(fee) as commision_fee FROM " . table('member_paylog');
        $sql.= " where openid={$openid} and friend_openid={$miyou_openid}";
        $commisionFee = mysqld_select($sql);
        $miyouInfo['commision_fee'] = $commisionFee['commision_fee']-$inviteFee['fee'];

        //她有多个觅友
        $count_sql   = "select count(openid) from ".table('member')." where recommend_openid={$miyou_openid}";
        $miyou_count = mysqld_selectcolumn($count_sql);
        $miyouInfo['miyou_count'] = $miyou_count;
    }
    else{
        $miyouInfo = array();
    }
    return $miyouInfo;
}

/**
 * 从所有的订单中移除掉正在的分享的，并返回分享的订单
 * @param $data  所有的订单
 * @return array
 */
function getShareOrder(&$data){
    if(empty($data)){
        return array();
    }else{
        $result = array();
        foreach($data as $key => $one){
            if($one['share_status'] == 1){
                $result = $one;
                unset($data[$key]);
                break;
            }
        }
        return $result;
    }
}