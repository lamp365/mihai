<?php
/**
 * Created by PhpStorm.
 * User: 刘建凡
 * Date: 2017/3/17
 * Time: 17:29
 */
function member_sign($openid){
    // 获取用户签到信息
    $merArr = mysqld_select("select * from ".table('member_sign')." where openid='{$openid}'");
    // 获取签到设置
    $set_config = globaSetting();

    // 判断是否为注册后第一次签到
    if(empty($merArr)){
        $score['openid']     = $openid;
        $score['total_num']  = 1;
        $score['num']        = 1;
        $score['createtime'] = time();
        mysqld_insert('member_sign',$score);
        if(mysqld_insertid()){
            //发放积分
            member_credit($openid,$set_config['every_day_jifen'],'addcredit',PayLogEnum::getLogTip('LOG_SIGN_JIFEN_TIP'));
            return array('errno'=>200,'message'=>array('tit'=>'您已签到成功！','desc'=>"获得{$set_config['every_day_jifen']}个积分",'day'=>1));
        }else{
            return array('errno'=>1002,'message'=>'签到失败，稍后再试！');
        }
    }else{
        // 获取登录用户签到信息
        $time     = $merArr['createtime']; // 签到时间
        $signTime = date('y-m-d',$time);   // 格式化签到时间
        // 判断今天是否已经签到
        if($signTime == date('y-m-d',time())){
            return array('errno'=>1002,'message'=>'今天您已签到过了！');
        }else{
            //签到凌晨时间戳
            $sign_zero  = strtotime($signTime);
            //今天的凌晨时间戳
            $today_zero = strtotime( date('y-m-d',time()));
            // 判断是否错过连续签到时间
            if($today_zero - $sign_zero > 60*60*24){
                $score['total_num']  = $merArr['total_num'] + 1;
                $score['num']        = 1; //重置为1
                $score['createtime'] = time();
                $result = mysqld_update('member_sign', $score, array('id'=>$merArr['id']));
                if($result){
                    //送积分
                    member_credit($openid,$set_config['every_day_jifen'],'addcredit',PayLogEnum::getLogTip('LOG_SIGN_JIFEN_TIP'));
                    return array('errno'=>200,'message'=>array('tit'=>'今天您已签到成功！','desc'=>"获得{$set_config['every_day_jifen']}个积分",'day'=>1));
                }else{
                    return array('errno'=>1002,'message'=>'签到失败啦！');
                }
            }else{
                $sign_num            = $merArr['num'] + 1;
                $score['total_num']  = $merArr['total_num'] + 1;
                $score['createtime'] = time();

                if($sign_num == 7){
                    //第七天重置掉 num
                    $score['num']    = 0;
                }else{
                    $score['num']    = $merArr['num'] + 1;
                }
                $result = mysqld_update('member_sign', $score, array('id'=>$merArr['id']));
                if($result > 0){
                    //送积分  还要判断是否连续4天或者7天
                    if($sign_num == 4){
                        $jifen = $set_config['continue_4day_jifen'] - $set_config['every_day_jifen'];
                        member_credit($openid,$set_config['continue_4day_jifen'],'addcredit',PayLogEnum::getLogTip('LOG_SIGN_4JIFEN_TIP'));
                        return array('errno'=>200,'message'=>array('tit'=>'您已连续签到4天！','desc'=>"另赠送{$jifen}个积分",'day'=>$sign_num));
                    }elseif($sign_num == 7){
                        $jifen = $set_config['continue_7day_jifen'] - $set_config['every_day_jifen'];
                        member_credit($openid,$set_config['continue_7day_jifen'],'addcredit',PayLogEnum::getLogTip('LOG_SIGN_7JIFEN_TIP'));

                        return array('errno'=>200,'message'=>array('tit'=>'您已连续签到7天！','desc'=>"另赠送{$jifen}个积分",'day'=>$sign_num));
                    }else{
                        member_credit($openid,$set_config['every_day_jifen'],'addcredit',PayLogEnum::getLogTip('LOG_SIGN_JIFEN_TIP'));
                        return array('errno'=>200,'message'=>array('tit'=>'今天您已签到成功！','desc'=>"获得{$set_config['every_day_jifen']}个积分",'day'=>$sign_num));
                    }
                }else{
                    return array('errno'=>1002,'message'=>'签到失败啦！');
                }
            }
        }
    }
}

/**
 * 统计当前还需要签到几天
 * @param $sign_data
 * @return int
 */
function count_contiune_day($sign_data){
    if(empty($sign_data)){
        return 7;
    }
    //签到凌晨时间
    $sign_zero  = strtotime(date("Y-m-d",$sign_data['createtime']));
    //今天凌晨时间
    $today_zero = strtotime( date('Y-m-d',time()));
    if($today_zero - $sign_zero > 60*60*24){
        //超过一天没有签到了，从第一天开始
        return 7;
    }
    $last_day = 7-$sign_data['num'];
    if($last_day ==0){
        return 7;
    }else{
        return $last_day;
    }
}
/**
 * 返回当前的会员信息 以及下一级的会员信息  包括特权
 * @param $openid
 * @return array|bool|mixed
 */
function get_member_priviel($openid){
    $rank = array();
    if($openid){
        //根据经验获取等级
        $member  = member_get($openid);
        $rank    = member_rank_model($member['experience']);
        $priviel_id = empty($rank['privile']) ? '' : $rank['privile'];
        if($priviel_id){
            $priviel =  mysqld_selectall("select * from ".table('rank_privile')." where id in ({$priviel_id})");
        }else{
            $priviel =  mysqld_selectall("select * from ".table('rank_privile'));
        }

        //下一级的数据
        $next_privile_id = empty($rank['rank_next']['privile']) ? '' : $rank['rank_next']['privile'];
        if($next_privile_id){
            $next_privile =  mysqld_selectall("select * from ".table('rank_privile')." where id in ({$next_privile_id})");
        }else{
            $next_privile =  mysqld_selectall("select * from ".table('rank_privile'));
        }
    }else{
        //没登录随机获取
       $priviel =  mysqld_selectall("select * from ".table('rank_privile'));
    }

    $rank['privile'] = $priviel;
    if(empty($openid)){
        $rank['rank_next'] = array();
    }else{
        $rank['rank_next']['privile'] = $next_privile;
    }
    return $rank;
}

/**
 * 发放优惠卷 给用户
 * @param $bonus_id
 * @param $openid
 * @return bool
 */
function change_user_bonus($bonus_id,$openid){
    if(empty($bonus_id) || empty($openid)){
        return false;
    }
    $bonus_sn = date("Ymd",time()).$bonus_id.rand(1000000,9999999);
    $bonus_user = mysqld_select("SELECT * FROM " . table('bonus_user')." where bonus_sn='".$bonus_sn."'" );
    while(!empty($bonus_user['bonus_id']))
    {
        $bonus_sn=date("Ymd",time()).$bonus_id.rand(1000000,9999999);
        $bonus_user = mysqld_select("SELECT * FROM " . table('bonus_user')." where bonus_sn='".$bonus_sn."'" );
    }
    $data=array(
        'createtime'	=> time(),
        'openid'		=> $openid,
        'bonus_sn'		=> $bonus_sn,
        'deleted'		=> 0,
        'isuse'			=> 0,
        'bonus_type_id'	=> $bonus_id
    );
    mysqld_insert('bonus_user',$data);
    return true;
}