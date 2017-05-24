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
 * @param $type    1获取觅友给我的佣金 和奖励   2获取推荐人他所得到得到的佣金 和奖励
 * @return mixed
 */
function getMiyouInfo($openid,$miyou_openid,$miyouInfo,$type='1'){

    if(!empty($miyouInfo))
    {
        if(!empty($miyouInfo['nickname']))
            $miyouInfo['name'] = $miyouInfo['nickname'];
        else if(!empty($miyouInfo['realname']))
            $miyouInfo['name'] = $miyouInfo['realname'];
        else
            $miyouInfo['name'] = substr_cut($miyouInfo['mobile']);

        //积分等级名称
        $member_rank_model = member_rank_model($miyouInfo['experience']);
        $miyouInfo['rank_name'] = $member_rank_model['rank_name'];

        if($type == 1){
            //该用户带给我的佣金
            $sql = "SELECT sum(fee) as commision_fee FROM " . table('member_paylog');
            $sql.= " where openid={$openid} and friend_openid={$miyou_openid} and type='addgold_byorder'";

            //该用户邀请好友得到的奖励
            $sql2 = "select fee as t_fee from".table('member_paylog_detail')." where openid={$openid} ";
            $sql2.= " and friend_openid={$miyou_openid} and type='addgold_byinvite' and status!=-1";

        }else if($type == 2){
            //推荐人自己的拥金
            $sql = "SELECT sum(fee) as commision_fee FROM " . table('member_paylog');
            $sql.= " where openid={$miyou_openid} and type='addgold_byorder'";

            //推荐人共得到的奖励
            $sql2 = "select sum(fee) as t_fee from".table('member_paylog_detail')." where openid={$miyou_openid} ";
            $sql2.= " and type='addgold_byinvite' and status!=-1";
        }
        $commisionFee = mysqld_select($sql);
        $miyouInfo['commision_fee'] = (float)$commisionFee['commision_fee'];

        $inviteFee   = mysqld_select($sql2);
        $miyouInfo['invite_fee'] = (float)$inviteFee['t_fee'];

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
 * type =1 获取10个分享达人 获得奖励比较多的10个
 * type =2 获取10用户，佣金比较多的也就是用户余额  目前产品的定义是用户余额
 * @return array
 */
function showSharersList($type = 1){
    if($type == 1){
        $sharersSql = "select openid,sum(fee) as total_fee from ".table('member_paylog_detail')." where type='addgold_byinvite' and status!=-1 group by openid order by total_fee desc limit 10";
        $sharersList = mysqld_selectall($sharersSql);
        if(!empty($sharersList)){
            foreach($sharersList as $key => $list){
                $member = mysqld_select("select nickname,avatar,mobile from ".table('member')." where openid={$list['openid']}");
                $sharersList[$key]['name'] = $member['nickname'];
                $sharersList[$key]['avatar']   = $member['avatar'];
            }
        }
    }else{
        $sharersSql = "select openid,gold as total_fee,nickname as name,avatar from ".table('member')." order by total_fee desc limit 10";
        $sharersList = mysqld_selectall($sharersSql);
    }
    return $sharersList;
}

/**
 * 根据订单或者openid 来统计中的觅友数 以及总的分享奖励
 * @param string $filed      ordersn 或者 openid
 * @param string $type      当是ordersn时用one   当是openid时用all
 * @return array|bool|mixed
 */
function zhuliOrderTotalMoneyAndNum($filed='',$type='one'){
    if(empty($filed)){
        return array();
    }
    if($type == 'one'){
        //单笔助力 $filed必须是ordersn
        $zhuli_sql  = "select count(openid) as friend_num ,sum(fee) as total_fee from ".table('member_paylog_detail')." where  ordersn='{$filed}' and type='addgold_byinvite'";
        $zhuli_info = mysqld_select($zhuli_sql);
    }else if($type == 'all'){
        //全部助力的情况
        $zhuli_sql  = "select count(openid) as friend_num ,sum(fee) as total_fee from ".table('member_paylog_detail')." where  openid='{$filed}' and status!=-1 and type='addgold_byinvite'";
        $zhuli_info = mysqld_select($zhuli_sql);
        //获取个人用户总的佣金
        $commiss_sql  = "select sum(fee) as commiss_fee from ".table('member_paylog')." where openid='{$filed}' and type='addgold_byorder'";
        $commiss_fee  = mysqld_select($commiss_sql);
        $zhuli_info['commiss_fee'] = $commiss_fee['commiss_fee'];
    }
    return $zhuli_info;
}

/**
 * 获取邀请分享出去的地址
 * 当两参数同时为空，则返回的活动页，不带用户信息
 * 当有订单的时候，两参数都给
 * 当没有订单  只给openid参数
 * @param string $openid
 * @param string $ordersn
 * @return string
 */
function getMiyouShareUrl($openid='',$ordersn=''){
    if(empty($openid) && empty($ordersn)){
        $url = WEBSITE_ROOT.mobile_url('miyou',array('op'=>'invite','name'=>'shopwap'));
        return $url;
    }
    if(empty($ordersn)){
        $access_str = $openid.'@miyou';
    }else{
        $access_str = $openid.'@'.$ordersn.'@miyou';
    }
    $access_key = DESService::instance()->encode($access_str);
    $url = WEBSITE_ROOT.mobile_url('miyou',array('op'=>'invite','name'=>'shopwap','accesskey'=>$access_key));
    return $url;
}

/**
 * 是否跳转到注册领取优惠券活动页   如果分享出去的地址，不是本人打开则跳转，同时记录accesskey
 * @param $openid
 * @param string $ordersn
 * @return string
 */
function isGoToShareRegeditPage($openid,$ordersn=''){
    if(isset($_GET['accesskey']) && !empty($_GET['accesskey'])){
        $accesskey = $_GET['accesskey'];
    }else{
//        $accesskey = getShareAccesskeyCookie();
        $accesskey = '';
    }
    if(empty($accesskey)) {
        if($openid){
            //如果已经登录，带上加密信息 便于qq浏览器或者微信分享
            $url = getMiyouShareUrl($openid,$ordersn);
            //带上加密信息重跳转
            header("location:{$url}");
        }else{
           //登录后才能访问
            header("location:".mobile_url("login"));
        }
    }

    $access_str = DESService::instance()->decode($accesskey);
    $code_arr   = explode('@',$access_str);
    if(count($code_arr) == 3){
        //有订单的分享
        if($code_arr[0] != $openid && $code_arr[2] == 'miyou'){
            //不是当前自己的用户，则要跳转  并缓存下来
            setShareAccesskeyCookie($accesskey);
            header("location:".mobile_url('miyou',array('op'=>'share','accesskey'=>$accesskey)));
        }
    }else if(count($code_arr) == 2){
        //无订单分享
        if($code_arr[0] != $openid && $code_arr[1] == 'miyou'){
            //不是当前自己的用户，则要跳转  并缓存下来
            setShareAccesskeyCookie($accesskey);
            header("location:".mobile_url('miyou',array('op'=>'share','accesskey'=>$accesskey)));
        }
    }
}

/**
 * 根据accesskey来获得，分享的链接中，ordersn订单属于哪个分类，并取得该分类下的商品，和主图
 * @param $accessky
 * @return array
 */
function getOrderBelongCatByAccesskey($accessky){
    //获取分类 得到分类对应的图片
    $cat_sql  = "SELECT id,name  FROM " . table('shop_category') . " WHERE parentid=0 and deleted=0";
    $cat_data = mysqld_selectall($cat_sql);
    $cat_pic  = $cat_ids = array();
    foreach($cat_data as $one){
        $cat_pic[$one['id']] = "share_cat_pic_{$one['id']}.png";
        $cat_ids[]           = $one['id'];
    }

    $access_str = DESService::instance()->decode($accessky);
    $access_arr = explode("@",$access_str);
    if(count($access_arr) == 2 && $access_arr[1] == 'miyou'){
        //无订单的分享 返回随机的图
        $key = array_rand($cat_ids);
        $key = $cat_ids[$key];
        $where = array(
            'table' => 'shop_dish',
            'where' => "a.p1={$key} and a.isfirst=1",
            'limit' => 3,
            'order' => 'displayorder desc',
        );
        $goods = get_goods($where);
        return array('cat_pic'=>$cat_pic[$key],'goods'=>$goods);

    }else if(count($access_arr) == 3 && $access_arr[2] == 'miyou'){
        //有订单的分享
        $ordersn    = $access_arr[1];
        //查找自有平台的订单
        $order_info = mysqld_select("select id from ".table('shop_order')." where ordersn='{$ordersn}'");
        if(!empty($order_info)){
            //根据订单id找 order_goods中商品所属于的分类
            $cat_sql = "select d.p1 from ".table('shop_order_goods')." as g left join ". table('shop_dish') ." as d";
            $cat_sql.= " on d.id=g.goodsid where g.orderid={$order_info['id']} ";
            $cat_res = mysqld_select($cat_sql);
            $key     = $cat_res['p1'];
            $where = array(
                'table' => 'shop_dish',
                'where' => "a.p1={$key} and a.isfirst=1",
                'limit' => 3,
                'order' => 'displayorder desc',
            );
            $goods = get_goods($where);
            return array('cat_pic'=>$cat_pic[$key],'goods'=>$goods);

        }else{
            //查找第三方订单
            $order_info = mysqld_select("select order_id,p1 from ".table('third_order')." where ordersn='{$ordersn}'");
            if(empty($order_info['p1'])){
                //找不到属于分类 则随机取
                $key = array_rand($cat_ids);
                $key = $cat_ids[$key];
            }else{
                $key = $order_info['p1'];
            }

            $where = array(
                'table' => 'shop_dish',
                'where' => "a.p1={$key} and a.isfirst=1",
                'limit' => 3,
                'order' => 'displayorder desc',
            );
            $goods = get_goods($where);
            return array('cat_pic'=>$cat_pic[$key],'goods'=>$goods);
        }
    }else{
        message("对不起，参数有误！",refresh(),'error');
    }
}

/**
 * 觅友注册，完成注册者新手礼券，分享者奖励
 * @param $mobile
 * @param $pwd
 * @return bool
 */
function miyou_register($mobile,$pwd){
    $accesskey      = getShareAccesskeyCookie();
    $act_user_award = false;   //操作用户奖励开关
    $ordersn = $share_openid	= '';
    if(!empty($accesskey)){
        $code    = DESService::instance()->decode($accesskey);
        $codeArr = explode('@',$code);
        if(count($codeArr) == 3){
            //有订单的
            $act_user_award = true;
            $share_openid   = $codeArr[0];
            $ordersn        = $codeArr[1];
        }else if(count($codeArr) == 2){
            //无订单
            $act_user_award = true;
            $share_openid   = $codeArr[0];
        }
    }

    $openid = date("YmdH",time()).rand(100,999);
    $hasmember = mysqld_select("SELECT openid FROM " . table('member') . " WHERE openid = :openid ", array(':openid' => $openid));
    if(!empty($hasmember['openid']))
    {
        $openid=date("YmdH",time()).rand(100,999);
    }
    $wx_info = get_weixininfo_from_regist();
    $data = array(
        'mobile'      => $mobile,
        'pwd'         => md5($pwd),
        'nickname'	  => $wx_info['name'],
        'realname'	  => $wx_info['name'],
        'avatar'	  => $wx_info['face'],
        'createtime'  => time(),
        'status'           => 1,
        'istemplate'       =>0,
        'experience'       => 0 ,
        'mess_id'          => 0 ,
        'openid'           =>$openid,
        'recommend_openid' =>$share_openid,
    );
    $res = mysqld_insert('member', $data);
    if($res){
        //当是微信操作的时候 绑定注册用于openid 与微信用户
        bind_weixin_openid_from_regist($openid);
        // 给用户新手礼券
        new_member_bonus($openid);
        //送积分
        register_credit($mobile,$openid);
        //手动帮用户登录
        save_member_login('',$openid);
        if($act_user_award){
            //注册成功   分享者奖励
            user_award_by_register($data,$share_openid,$ordersn);
        }
        return true;
    }else{
        return false;
    }
}

/**
 * 当是微信端进入注册的 绑定用户openid跟微信用户
 * @param $openid
 */
function bind_weixin_openid_from_regist($openid){
    if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger')) {
        if(!empty( $_SESSION[MOBILE_SESSION_ACCOUNT]['unionid'])){
            $unionid =  $_SESSION[MOBILE_SESSION_ACCOUNT]['unionid'];
            mysqld_query("update ".table('weixin_wxfans')." set `openid`='{$openid}' where  unionid='{$unionid}' and openid = ''");
        }
    }
}

/**
 * 给分享者加入邀请朋友的奖励，并觅友数加1
 * @param $openid
 * @param $share_openid
 * @param $ordersn
 */
function user_award_by_register($member_data,$share_openid,$ordersn){
    $openid = $member_data['openid'];
    //邀请收益
    $invit_money   = array('direct_share_price'=>0,'order_share_price'=>'0','direct_share_jifen'=>0);
    $inviteSetting = mysqld_select ( "SELECT value FROM " . table ( 'config' ) . " where name='invite_setting' " );
    if(!empty($inviteSetting)){
        $invit_money   = unserialize($inviteSetting['value']);
    }
    $push_jifen  = $invit_money['direct_share_jifen'];
    $add_pay_log = false;
    if(empty($ordersn)){
        //没有订单，直接给分享者奖励几块钱
        $add_pay_log = true;
        $money       = $invit_money['direct_share_price'];
        if($money > 0){
            $data = array(
                'fee'             => $money,
                'openid'          => $share_openid,
                'friend_openid'   => $openid,
                'type'            => 'addgold_byinvite',
                'createtime'      => time(),
                'remark'          => "邀请用户注册成功获得奖励",
            );
            mysqld_insert('member_paylog_detail',$data);
        }

    }else{
        //有订单查看订单 是否已经提交，以及是否 钱超过了
        //获取分享的订单
        $share_order = mysqld_select("SELECT id as oid,price,share_status,'self' as from_platform  from ".table('shop_order')." where ordersn='{$ordersn}'");
        if(empty($share_order))
            $share_order = mysqld_select("SELECT order_id as oid, price,share_status,from_platform from ".table('third_order')." where ordersn='{$ordersn}'");


        if(!empty($share_order) && ($share_order['share_status'] == 2 || $share_order['share_status'] == 1)){
            //订单已经提交审核 作为普通奖励
            if($share_order['share_status'] == 2){
                $add_pay_log = true;
                $money   = $invit_money['direct_share_price'];
                $ordersn = '';  //赋值为空，该笔订单已经满人了
            }else{
                //如果未提交审核，要注意的是，邀请朋友近来的钱是否已经超过 订单的钱
                $paylog = mysqld_select("select sum(fee) as total from ".table('member_paylog_detail')." where ordersn='{$ordersn}'");
                if($paylog['total'] >= $share_order['price']){
                    $add_pay_log = true;
                    $money = $invit_money['direct_share_price'];
                    $ordersn = '';  //赋值为空，该笔订单已经满人了
                }else{
                    //加上此次奖励是否超过订单
                    $curt_total = $paylog['total']+$invit_money['order_share_price'];
                    if($curt_total >= $share_order['price']){
                        //超过订单 算为普通分享
                        $money       = $invit_money['direct_share_price'];
                        $ordersn     = '';  //赋值为空，该笔订单已经满人了
                        $add_pay_log = true;
                    }else{
                        $money  = $invit_money['order_share_price'];
                    }
                }
            }

            if($money > 0){
                $data = array(
                    'fee'             => $money,
                    'openid'          => $share_openid,
                    'friend_openid'   => $openid,
                    'ordersn'         => $ordersn,
                    'type'            => 'addgold_byinvite',
                    'createtime'      => time(),
                    'remark'          => "邀请用户注册成功获得奖励",
                );
                mysqld_insert('member_paylog_detail',$data);
            }

        }
    }

    if($add_pay_log){
        //如果是 没有订单的 或者 奖励超过订单价  则还要记录paylog
        member_invitegold($share_openid,$openid,$money,'addgold_byinvite');   //现金奖励 当为0或者小于0 不会被插入
        //推送分享者一条IM消息
        push_msg_to_shareer($money,$share_openid,$member_data);

    }
    //积分奖励
    member_credit($share_openid,$push_jifen,'addcredit',PayLogEnum::getLogTip('LOG_REGISTER_TIP',$member_data['nickname']));
    push_msg_to_shareer($push_jifen,$share_openid,$member_data);

    //觅友数 加 1
    $sql = "update ".table('member')." set `friend_count`=friend_count+1 where openid={$share_openid}";
    mysqld_query($sql);
    cleanShareAccesskeyCookie();
}

/**
 * 推送分享者一条IM消息
 * @param $money
 * @param $share_openid
 * @param $member_data
 * @param int $type  1 表示现金 2表示积分
 */
function push_msg_to_shareer($money,$share_openid,$member_data,$type=1){
    if($type == 1 && $money > 0){
        $price = number_format($money,2);
        $time  = date("Y-m-d H:i",time());
        $name  = $member_data['nickname'];
        $msg  = "恭喜，掌门！邀请好友现金已到账~
金额已打入 [现金余额] 中，可在我的钱包中查看，支持体现到银行卡和支付宝。
现金奖励：{$price}元
邀请好友：{$name}
邀请时间：{$time}";
        pushOrderImMessage(IM_WEALTH_FROM_USER,$share_openid,$msg);
    }
    if($type == 2 && $money > 0){
        $price = $money;
        $time  = date("Y-m-d H:i",time());
        $name  = $member_data['nickname'];
        $msg  = "恭喜，掌门！邀请好友积分奖励已到账~
积分已打入您的账户中，可在我的个人中心中查看。
积分奖励：{$price}元
邀请好友：{$name}
邀请时间：{$time}";
        pushOrderImMessage(IM_WEALTH_FROM_USER,$share_openid,$msg);
    }


}

/**
 * 专属码活动注册入口
 * @param $mobile
 * @param $pwd
 * @return bool
 */
function exclusive_register($mobile,$pwd,$third_order){
    $openid = date("YmdH",time()).rand(100,999);
    $hasmember = mysqld_select("SELECT openid FROM " . table('member') . " WHERE openid = :openid ", array(':openid' => $openid));
    if(!empty($hasmember['openid']))
    {
        $openid=date("YmdH",time()).rand(100,999);
    }
    $data = array(
        'mobile'      => $mobile,
        'pwd'         => md5($pwd),
        'nickname'	  => $third_order['address_realname'],
        'realname'	  => $third_order['address_realname'],
        'createtime'  => time(),
        'status'           => 1,
        'istemplate'       =>0,
        'experience'       => 0 ,
        'mess_id'          => 0 ,
        'openid'           =>$openid,
    );
    $res = mysqld_insert('member', $data);
    if($res){
        //邀请收益
       /*   暂时去除 现金奖励
       $invit_money   = array('direct_share_price'=>0,'order_share_price'=>'0');
        $inviteSetting = mysqld_select ( "SELECT value FROM " . table ( 'config' ) . " where name='invite_setting' " );
        if(!empty($inviteSetting)){
            $invit_money   = unserialize($inviteSetting['value']);
        }
       */

        // 给用户活动专属礼券
        exclusive_bonus($openid);
        //送积分
        register_credit($mobile,$openid);
        //送用户5元现金
//        member_gold($openid,$invit_money['order_share_price'],'addgold',PayLogEnum::getLogTip('LOG_TORDER_MONEY_TIP'));
        //将用户的身份证信息入到我们的 地址里
        insert_user_address($openid,$third_order);
        //手动帮用户登录
        save_member_login('',$openid);
        return $openid;
    }else{
        return false;
    }
}

function insert_user_address($openid,$third_order){
    $data = array(
        'openid'      =>  $openid,
        'realname'    =>  $third_order['address_realname'],
        'mobile'      =>  $third_order['address_mobile'],
        'province'    =>  $third_order['address_province'],
        'city'        =>  $third_order['address_city'],
        'area'        =>  $third_order['address_area'],
        'address'     =>  $third_order['address_address'],
        'isdefault'   =>  1,
    );
    mysqld_insert('shop_address',$data);
}


/**
 * 返回专属码的页面地址
 * @param string $code  专属码 可以为空也可以给对应的值
 * 区别就是在页面的输入框上，有值，会自动帮你填写
 * @return string
 */
function get_exclusive_url($code = ''){
    if(empty($code)){
        $url = mobile_url('miyou',array('op'=>'exclusive'));
    }else{
        $url = mobile_url('miyou',array('op'=>'exclusive','code'=>$code));
    }
    return WEBSITE_ROOT.$url;
}