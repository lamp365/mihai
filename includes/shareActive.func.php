<?php
/**
 * @param $openid
 * 用户每次近来活动主页优先创建 活动主表的记录
 * 没有则创建，有了则判断是否是第二天，是的话，跟新当天的可参与活动次数
 */
function checkIsAddShareActive($openid){
    $info = array();
    if(!empty($openid)){
        //今天的凌晨时间
        $curt_time = strtotime(date("Y-m-d"),time());
        $member    = member_get($openid);
        $info      = mysqld_select("select * from ".table('share_active')." where openid='{$openid}'");

        if(empty($info)){
            $rank      = mysqld_select("SELECT rank_level FROM " . table('rank_model')." where experience<='".$member['experience']."' order by rank_level desc limit 1 " );
            $rank_level= empty($rank['rank_level']) ? 2 : $rank['rank_level'];
            $info = array(
              'openid'      => $openid,
              'total_num'   => $rank_level*2+1,
              'createtime'  => time(),
              'modifytime'  => time(),
              'zero_time'   => $curt_time
            );
            mysqld_insert('share_active',$info);
        }else{
            $id     = $info['id'];
            $update = array();

            //如果过了第二天，则初始化活动参与次数
            if($curt_time>$info['zero_time']){
                $rank      = mysqld_select("SELECT rank_level FROM " . table('rank_model')." where experience<='".$member['experience']."' order by rank_level desc limit 1 " );
                $rank_level= empty($rank['rank_level']) ? 2 : $rank['rank_level'];
                $update['total_num']  = $rank_level*2+1;
                $update['zero_time']  = $curt_time;
                $update['modifytime'] = time();
                mysqld_update("share_active",$update,array('id'=>$id));
            }
        }
    }
    return $info;
}

function getOpenidFromWeixin($openid){
    if(empty($openid)){
        //未登录  获取微信表所存的openid
        //如果是用微信端打开可以获取到  如果用qq分享出去打开，则没有该信息
        $unionid = get_weixin_session_account('unionid');
        if(!empty($unionid)){
            $weixin_info = mysqld_select("select openid from ".table('weixin_wxfans')." where unionid={$unionid}");
            if(!empty($weixin_info)){
                $openid = $weixin_info['openid'];
            }

        }
    }
    return $openid;
}

/**
 * @content 获取二维码，用于扫描关注绑定用户信息
 * @param $act_id  share_active中的活动id
 * @return string
 */
function getShareActiveWeixinErweima($act_id){
    $weixin  = new WeixinTool();
    $erweima = $weixin->get_weixin_erweima($act_id);
    return $erweima;
}
/**
 * @content 得到今天已经分享出去的一些用户
 * @parame array $share_active  活动主表的记录
 * @param int $num      控制取出的个数 为空时，则全部取
 * @param bool $today   为true则默认取今天的，不为true则不限制时间
 * @return array
 */
function getHasShareMember($share_active,$num=10,$today=true){
    $member = array();
    if(!empty($share_active)){
        $limit = '';
        $where = '';
        if($today){
            //获取当天凌晨年月日的时间戳
            $time  = strtotime(date('Y-m-d'),time());
            $where = " and createtime = {$time}";
        }
        if($num){
            $limit = "limit {$num}";
        }
        $sql     = "select * from ".table('share_active_record')." where active_id={$share_active['id']} {$where}  order by id desc {$limit} ";
        $member  = mysqld_selectall($sql);
        if(!empty($member)){
            foreach($member as $key=>&$item){
                if(!empty($item['visted_weixin_openid'])){
                    $man = mysqld_select("select nickname,avatar from ".table('weixin_wxfans')." where weixin_openid='{$item['visted_weixin_openid']}'");
                    if($man){
                        $item['nickname'] = $man['nickname'];
                        $item['avatar']   = $man['avatar'];
                    }else{
                        unset($member[$key]);
                    }
                }else if(!empty($item['visted_openid'])){
                    $man = mysqld_select("select realname,avatar from ".table('member')." where openid='{$item['visted_openid']}'");
                    if($man){
                        $item['nickname'] = $man['realname'];
                        $item['avatar']   = $man['avatar'];
                    }else{
                        unset($member[$key]);
                    }
                }
            }
        }
    }
    return $member;
}

/**
 * 用户注册的时候调用，获取是否有分享者分享过来的，有的话，返回分享者openid，
 * 并给分享者加一次可参与的活动次数
 * @return bool|int
 */
function getShareOpenidFromCookie(){
    $accesskey    = getShareAccesskeyCookie();
    $share_openid = decodeShareAccessKey($accesskey);
    ppd($share_openid,'ssss');
    if($share_openid){
        //给分享者加1次机会
        $time = time();
        $res  = mysqld_query("update " .table('share_active'). " set `total_num`=total_num+1,`modifytime`={$time} where openid='{$share_openid}'");
    }else{
        $share_openid = 0;
    }
    cleanShareAccesskeyCookie();
    return $share_openid;
}


/**
 * @content 保存微信的图片到本地
 * @param $weixin_openid
 * @param $weixin_img    图片地址 二维码或者头像
 * @param bool $is_erweima
 * @return string
 */
function saveWeixinImgToLocal($weixin_openid,$weixin_img,$is_erweima=true){
    if($is_erweima){
        $name = $weixin_openid."_erweima.png";
    }else{
        $name = $weixin_openid."_touxiang.png";
    }
    $dir = "./attachment/shareactive";
    if(!is_dir($dir)){
        mkdir($dir,0777);
        chmod($dir, 0777); //给目录操作权限
    }
    $img_url = $dir."/".$name;
    if(file_exists($img_url)){
        return $img_url;
    }else{
        $content = file_get_contents($weixin_img);
        $res     = file_put_contents($img_url,$content);
        if($res){
            return $img_url;
        }else{
            return '';
        }
    }

}
function encodeShareAccessKey($openid){
    $openid.="@@@share";
    return DESService::instance()->encode($openid);
}

function decodeShareAccessKey($accesskey){
    if(empty($accesskey)){
        return false;
    }
    $code = DESService::instance()->decode($accesskey);
    $codeArr = explode('@@@',$code);
    if($codeArr['1']!= 'share'){
        return false;
    }else{
        $openid = $codeArr['0'];
        return $openid;
    }
}

function isReloadOrSetCache($openid,$accesskey){
    if(empty($accesskey)){
        if($openid){
            //如果已经登录过，则让地址带上加密信息，便于分享出去
            $accesskey = encodeShareAccessKey($openid);
            $url       = mobile_url('shareActive',array('op'=>'display', 'accesskey'=>$accesskey));
            header("location:".$url);
        }
    }else{
        //如果带有加密信息  缓存起来
        $share_openid = decodeShareAccessKey($accesskey);
        if($openid != $share_openid){
            //当不是自己的时候记录缓存
            setShareAccesskeyCookie($accesskey);
        }

        //暂时不做记录
       /* if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger')) {
            //微信端操作
            $weixin_openid = get_weixin_session_account('weixin_openid');
            addShareActiveRecordMember($share_openid,$weixin_openid);
        }else{
            //非微信端操作
            if($openid){
                //如果已经登录，则进行 邀请添加会员的记录 share_active_record
                addShareActiveRecordMember($share_openid,$openid);
            }
        }*/

    }
}


function setShareAccesskeyCookie($accesskey){
    $accesskey_cookie = getShareAccesskeyCookie();
    if($accesskey != $accesskey_cookie){
        $cookie      = new LtCookie();
        $cookie->setCookie('shareAccesskey',$accesskey,time()+3600*2);
    }
}

/**
 * @content 从cookie中获取分享者的accesskey
 * @return bool|string
 */
function getShareAccesskeyCookie(){
    $cookie        = new LtCookie();
    $accesskey     = $cookie->getCookie('shareAccesskey');
    return $accesskey;
}
function cleanShareAccesskeyCookie(){
    $cookie      = new LtCookie();
    $cookie->delCookie('shareAccesskey');
}


/**
 * 分享活动记录表  添加当天的成员
 * @param $share_openid  分享者的openid
 * @param $openid  个人openid
 * @return string
 */
function addShareActiveRecordMember($share_openid,$openid){
    if(empty($share_openid))
        return '';

    $share_info  = mysqld_select("select * from ".table('share_active')." where openid='{$share_openid}'");
    if(empty($share_info))
        return '';


    if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger')) {
        //微信端操作
        $weixin_openid = $openid;
        $own_openid    = getOpenidFromWeixin('');  //可能是空，微信用户第一次关注,还没登录注册
        if($own_openid == $share_info['openid']){
            return '';
        }

        $time = strtotime(date("Y-m-d"),time());
        $where = " where visted_weixin_openid='{$weixin_openid}'";
        if($own_openid){
            $where = " where (visted_weixin_openid='{$weixin_openid}' or visted_openid='{$own_openid}')";
        }
        $where .= " and createtime={$time}";
        $info = mysqld_select("select id from ".table('share_active_record')." {$where}");
        if(empty($info)){
            $data = array(
                'active_id'      => $share_info['id'],
                'visted_openid'  => $own_openid,
                'visted_weixin_openid' => $weixin_openid,
                'createtime'     => $time,
            );
            mysqld_insert('share_active_record',$data);
        }
    }else{
        //非微信端操作
        $time = strtotime(date("Y-m-d"),time());
        if($openid == $share_info['openid'])
            return '';

        $info = mysqld_select("select id from ".table('share_active_record')." where visted_openid='{$openid}' and createtime={$time}");
        if(empty($info)){
            //分享者跟自己不是同一个人时
            $data = array(
                'active_id'      => $share_info['id'],
                'visted_openid'  => $openid,
                'createtime'     => $time,
            );
            mysqld_insert('share_active_record',$data);
        }
    }

}

/**
 * 统计总的参与人数  因为前端用ajax不断请求，故加一层缓存
 * @return bool|string
 */
function get_active_total_people(){
    if(class_exists('Memcached')){
        $memcache = new Mcache();
        $total    = $memcache->get('shareActiveTotalPeople');
        if(!$total){
            $total = mysqld_selectcolumn("select count(id) from ".table('addon7_request'));
            $memcache->set('shareActiveTotalPeople',$total,time()+3600*2); //存两个小时
        }
    }else{
        $total = mysqld_selectcolumn("select count(id) from ".table('addon7_request'));
    }
    return $total+72385;
}

/**
 * 每次有商品满人时检测是否够6个，6个的话，就进行跟新状态为2
 */
function checkAwardGoodsIsFull(){
    $total      = mysqld_selectcolumn("select count(id) from ".table('addon7_award')." where state=1");
    $res        = '';
    if($total>=6){
        //更新前确保 当前有2的已经开奖了 才给新的更新
        $isToBeDraw = mysqld_select("select id from ".table('addon7_award')." where state=2");
        if(empty($isToBeDraw)){
            $time      = time();
            $draw_time = get_open_time($time, 'Y-m-d');
            $res  = mysqld_query("update ".table('addon7_award')." set `state`=2,`date`={$draw_time},`lock_time`={$time} where state=1 order by confirm_time desc limit 6");
            //6个满了。生成开奖数据
            $data = array(
                'lock_time'  => $time,
                'vn'         => 0,
                'states'     =>0
            );
            mysqld_insert('addon7_point',$data);
        }
    }
    return $res;
}

/**
 * @content 获取礼品 根据PC的页面  不同位置需要的数据，进行取数据
 * @param $pos 根据PC的页面，由上到下 板块用1-4区分
 * @return array|bool|mixed
 */
function get_active_goods($pos,$openid){
    switch($pos){
        case 1:
            //6个状态是1 的即将进入开奖 按照完成时间倒序，最先的显示在后面
            $sql = "select * from ".table('addon7_award')." where state=1 or state =2 order by confirm_time asc limit 6";
            $res = mysqld_selectall($sql);
            break;
        case 2:
            //推荐的  条件最好不用 isrecommand 因为如果满了，那么这边就会空出来了，取不到推荐的
            $now_time = time();
            $sql = "select * from ".table('addon7_award')." where state<=1 and endtime<={$now_time} order by isrecommand desc,id  desc";
            $res = mysqld_select($sql);
            if(!empty($res)){
                //dicount  可能会超过总的
                $diff_count        = min($res['amount'],$res['dicount']);
                $jindutiao         = round($diff_count*100/$res['amount'],2).'%';   //进度条
                $res['jindutiao']  = $jindutiao;
                if($openid){
                    $res['wish_num']  = mysqld_selectcolumn("select count(id) from ".table('addon7_request')." where award_id={$res['id']} and openid='{$openid}'");
                }else{
                    $res['wish_num']  = 0;
                }
            }
            break;
        case 3:
            //心愿专区
            $now_time = time();
            $sql = "select * from ".table('addon7_award')." where state<=1 and isrecommand=0 and endtime<={$now_time} order by id desc";
            $res = mysqld_selectall($sql);
            if(!empty($res)){
                foreach($res as $key=>&$item){
                    //dicount  可能会超过总的
                    $diff_count        = min($item['amount'],$item['dicount']);
                    $jindutiao         = round($diff_count*100/$item['amount'],2).'%';   //进度条
                    $item['jindutiao'] = $jindutiao;
                    if($openid){
                        $item['wish_num']  = mysqld_selectcolumn("select count(id) from ".table('addon7_request')." where award_id={$item['id']} and openid='{$openid}'");
                    }else{
                        $item['wish_num']  = 0;
                    }
                }
            }
            break;
        case 4:
            //即将进入心愿专区
            $now_time = time();
            $sql = "select * from ".table('addon7_award')." where state=0 and endtime>{$now_time} order by endtime asc limit 4";
            $res = mysqld_selectall($sql);
    }
    return $res;
}

/**
 * @content 获取晒单4个
 * @return bool|mixed
 */
function get_active_shaidan(){
    $info = mysqld_selectall("select * from ".table('share_active_shaidan')." order by is_top desc,id desc limit 4");
    if(!empty($info)){
        foreach($info as $key => $item){
            $member = member_get($item['openid']);
            $info[$key]['nickname'] = empty($member['realname']) ? $member['mobile'] : $member['realname'];
            $info[$key]['avatar']   = $member['avatar'];
        }
    }
    return $info;
}
/**
 * 许愿一个就获得一个幸运号码
 * @param $award_id
 * @return array
 */
function get_star_num($award_id){
    $info = mysqld_select("select star_num_order from ".table('addon7_request')." where award_id={$award_id} order by star_num_order desc limit 1");
    if(empty($info)){
        $next     = 1;
        $star_num = "p".$award_id."00000".$next;
    }else{
        $next     = $info['star_num_order']+1;
        $star_num = "p".$award_id."00000".$next;
    }
    return array('star_num'=>$star_num,'star_num_order'=>$next);
}

/**
 * @content截取标题为星星
 * @param $title
 * @return string
 */
function cut_title($title){
    $strlen = mb_strlen($title, 'utf-8');
    if($strlen>=8){
        $firstStr = mb_substr($title, 0, 2, 'utf-8');
        $lastStr = mb_substr($title, -1, 2, 'utf-8');
        $xing    = str_repeat("*",5);
    }else{
        $firstStr = mb_substr($title, 0, 1, 'utf-8');
        $lastStr = mb_substr($title, -1, 1, 'utf-8');
        $xing    = str_repeat("*",4);
    }
    return $firstStr.$xing.$lastStr;
}

/**
 * @content 统计参与者的总次数
 * @param $openid
 * @param $award_id
 * @return bool|int|string
 */
function getRecorderCount($openid,$award_id){
    if($openid){
        $total = mysqld_selectcolumn("select count(id) from ".table('addon7_request')." where award_id = $award_id and openid='{$openid}'");
    }else{
        $total = 0;
    }
    return $total;
}

/**
 * 当需要刷单开启的时候，配合使用，避免刷单时受到次数限制报错。
 * @param $openid
 */
function shuadan_checkActiveCishu($openid){
    if(empty($openid)){
        die(showAjaxMess('1002',"请求参数有误！"));
    }
    $setting = globaSetting();
    if($setting['open_shareactive']!=1){
        die(showAjaxMess(1002,"对不起，已处于关闭中！"));
    }
    //设置该用户的参与次数给6够本次刷就行了
    $curt_time = strtotime(date("Y-m-d"),time());
    $info      = mysqld_select("select * from ".table('share_active')." where openid='{$openid}'");
    if(empty($info)){
        $info = array(
            'openid'      => $openid,
            'total_num'   => 6,
            'createtime'  => time(),
            'modifytime'  => time(),
            'zero_time'   => $curt_time
        );
        mysqld_insert('share_active',$info);
    }else{
        mysqld_update("share_active",array("total_num"=>6),array("openid"=>$openid));
    }
}

/**
 * 获取所有属于类型4 的优惠卷，并判断是否领取过
 * @param $openid
 * @return array
 */
function get_all_changebonus($openid){
    $now_time = time();
    $bonus = mysqld_selectall("select * from ".table('bonus_type')." where send_type=4 and send_start_date<={$now_time} and send_end_date>={$now_time} limit 8");
    if(!empty($bonus)){
        if($openid){
            foreach($bonus as $key => &$one_bonus){
                //查找用户是否领取过
               $info =  mysqld_select("select bonus_id from ".table('bonus_user')." where bonus_type_id={$one_bonus['type_id']} and openid={$openid}");
                if($info){
                    //找到，标记为领取
                    $one_bonus['is_get']     = 1;
                    //兑换值 是 面额比去一个固定值  当前为3
                    $one_bonus['change_num'] = ceil($one_bonus['type_money']/3);
                }else{
                    //没找到，标记为没领取
                    $one_bonus['is_get']     = 0;
                    //兑换值 是 面额比去一个固定值  当前为3
                    $one_bonus['change_num'] = ceil($one_bonus['type_money']/3);
                }
            }
        }else{
            foreach($bonus as $key => &$one_bonus){
                //未登录，标记为没领取
                $one_bonus['is_get']     = 0;
                //兑换值 是 面额比去一个固定值  当前为3
                $one_bonus['change_num'] = ceil($one_bonus['type_money']/3);
            }
        }
    }
    return $bonus;
}

/**
 * 兑换优惠卷，按照用户参与过的活动许愿次数，来判断是否可以领取优惠卷
 * @param $openid
 * @param $bonus_id
 * @return string
 */
function toChangeBonus($openid,$bonus_id){
    $info =  mysqld_select("select bonus_id from ".table('bonus_user')." where bonus_type_id={$bonus_id} and openid={$openid}");
    if($info){
        $msg = showAjaxMess(1002,"对不起，您已经领取过");
    }else{
        $total_num = mysqld_selectcolumn("select count(id) from ".table('addon7_request')." where openid={$openid}");
        //获取该优惠卷
        $now_time = time();
        $bonus = mysqld_select("select * from ".table('bonus_type')." where type_id={$bonus_id} and send_type=4");
        if(empty($bonus)){
            $msg = showAjaxMess(1002,"对不起，非法访问！");
        }else{
            if($bonus['send_start_date']<= $now_time && $bonus['send_end_date']>=$now_time){
                $change_num = ceil($bonus['type_money']/3);
                if($total_num < $change_num){
                    $msg = showAjaxMess(1002,"您的许愿数只有{$total_num}次");
                }else{
                    $bonus_sn = date("Ymd",time()).$bonus_id.rand(1000000,9999999);
                    $data = array(
                        'bonus_type_id' => $bonus_id,
                        'bonus_sn'      => $bonus_sn,
                        'openid'        => $openid,
                        'deleted'       => 0,
                        'isuse'         => 0,
                        'createtime'    => time(),
                    );
                    mysqld_insert('bonus_user',$data);
                    $msg = showAjaxMess(200,"恭喜领取成功！");
                }
            }else{
                $msg = showAjaxMess(1002,"优惠卷发放时间已经结束");
            }
        }
    }
    return $msg;
}