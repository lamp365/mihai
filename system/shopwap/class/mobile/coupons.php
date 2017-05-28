<?php
$op     = empty($_GP['op']) ? 'list' : $_GP['op'];
$openid = checkIsLogin();

if($op == 'list'){  //领券列表
    $sql = "SELECT type_id,type_name,type_money,use_start_date,use_end_date,min_goods_amount,send_max FROM " . table('bonus_type');
    $sql.= " where (send_type =1 or send_type =2) and deleted = 0 ";		//非新手礼优惠券
    $sql.= " and send_start_date<=".time()." and send_end_date >=".time();

    //显示新手礼之外的可领优惠券
    $bonus = mysqld_selectall($sql);

    //已登录用户
    if ($openid && !empty($bonus)) {
        //已领过的优惠劵  以及领取过的次数
        $arrBonusUser = mysqld_selectall('SELECT bonus_type_id,count(openid) as cnt FROM ' . table('bonus_user') . " where openid={$openid} group by bonus_type_id");

        foreach($bonus as $bk => $bv){
            //如果该优惠卷已经在积分兑换中存在，则去除掉
            $award = mysqld_select("select id from ".table('addon7_award')." where award_type=2 and gid={$bv['type_id']}");
            if($award){
                unset($bonus[$bk]);
            }

            if(!empty($arrBonusUser)) {
                if($bv['send_max']==0){
                    continue;
                }else {
                    foreach($arrBonusUser as $uk=>$uv) {
                        //次数已经上限了
                        if($uv['bonus_type_id'] == $bv['type_id'] && $uv['cnt']>=$bv['send_max']) {
                            unset($bonus[$bk]);
                        }
                    }
                }
            }
        }

    }
    //记住当前地址
    tosaveloginfrom();
    include themePage('coupons');
}
