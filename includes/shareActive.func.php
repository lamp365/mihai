<?php
/**
 * @param $openid
 * 用户每次近来活动主页优先创建 活动主表的记录
 */
function checkIsAddShareActive($openid){
    $info = array();
    if(!empty($openid)){
        //今天的凌晨时间
        $curt_time = strtotime(date("Y-m-d"),time());
        $member    = member_get($openid);
        $rank      = mysqld_select("SELECT rank_level FROM " . table('rank_model')." where experience<='".$member['experience']."' order by rank_level desc limit 1 " );
        $info      = mysqld_select("select id,zero_time from ".table('share_active')." where openid='{$openid}'");
        $rank_level= empty($rank['rank_level']) ? 2 : $rank['rank_level'];
        if(empty($info)){
            $info = array(
              'openid'      => $openid,
              'total_num'   => $rank_level*2,
              'createtime'  => time(),
              'modifytime'  => time(),
              'zero_time'   => $curt_time
            );
            mysqld_insert('share_active',$info);
            $erweima    = getShareActiveWeixinErweima($info['id']);
            $info['id']      = mysqld_insertid();
            $info['erweima'] = $erweima;
            mysqld_update("share_active",array('erweima'=>$erweima),array('id'=>$info['id']));
        }else{
            $id   = $info['id'];
            //如果二维码已经过6天了，再次获取
            $diff_time = time()-$info['createtime'];
            $update    = array();
            if($diff_time > 3600*24*6){
                $erweima           = getShareActiveWeixinErweima($info['id']);
                $info['erweima']   = $erweima;
                $update['erweima'] = $erweima;
            }
            //如果过了第二天，则初始化活动参与次数
            if($curt_time>$info['zero_time']){
                $update['total_num']  = $rank_level*2;
                $update['zero_time']  = $curt_time;
                $update['modifytime'] = time();
            }

            if(!empty($update)){
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
            $where = " and s.createtime = {$time}";
        }
        if($num){
            $limit = "limit {$num}";
        }
        $sql     = "select s.visted_openid,wx.* from ".table('share_active_record')." as s left join ".table('weixin_wxfans')." as wx ";
        $sql    .= " on s.visted_openid=wx.openid  where s.active_id={$share_active['id']} {$where}  order by s.id desc {$limit}";
        $member  = mysqld_selectall($sql);

    }
    return $member;
}

/**
 * 设置缓存，把自己的$weixin_openid作为key,  推荐人的openid作为值
 * 目前只用于微信，因为key是$weixin_openid
 * @param $act_id
 */
function setShareActiveCache($act_id,$weixin_openid){
    if(!empty($act_id)){
        $share_info = mysqld_select("select openid from ".table('share_active')." where id={$act_id}");
        if(!empty($share_info)){
            $key      = "share_".$weixin_openid;
            if(class_exists('Memcached')){
                $memcache = new Mcache();
                $memcache->set($key,$share_info['openid'],time()+3600*10);   //缓存10个小时
            }else{
                $cookie     = new LtCookie();
                $cookie->setCookie($key,$share_info['openid'],time()+3600*10);
            }
        }
    }
}

/**
 * @content 获取活动推荐人的openid  目前只用于微信，因为key是unionid
 * @param $own_openid
 * @return mixed|string
 */
function getShareActiveCache(){
    $weixin_openid = get_weixin_session_account('weixin_openid');
    $share_openid  = 0;
    if(!empty($weixin_openid)){
        $key          = "share_".$weixin_openid;
        if(class_exists('Memcached')){
            $memcache       = new Mcache();
            $share_openid   = $memcache->get($key);   //缓存10个小时
        }else{
            $cookie       = new LtCookie();
            $share_openid = $cookie->getCookie($key);
        }
    }
    return $share_openid;
}
/**
 * 解密accesskey  得到openid与当前用户比对，是自己的返回true,否则返回false
 * @param $openid  当前用户
 * @return bool
 */
/*function checkAccessKeyIsSelf($openid){
    $accesskey     = getShareAccesskeyCookie();
    $decode_openid = decodeShareAccessKey($accesskey);
    if($decode_openid){
        if($openid == $decode_openid){
            //说明是自己
            return true;
        }else{
            //说明不是自己  或者当前用户还没登录无法比对
            return false;
        }
    }else{
        //解出来没值，说明，上次分享的用户没有登录
        return false;
    }
}*/

/**
 * @content   获取活动分享者的微信信息
 * @param string $openid  可以不给，不给的话，默认解密accesskey后得到的openid直接去查询微信信息
 * @return array|bool|mixed
 */
/*function getSharerOfWeixin($openid=''){
    $accesskey     = getShareAccesskeyCookie();
    $decode_openid = decodeShareAccessKey($accesskey);
    if($decode_openid){
        //获取该分享者的微信
        if(!empty($openid)){
            if($openid == $decode_openid){
                //如果是自己，不用再次查询
                $data = array();
            }else{
                //如果不是自己，则需要得知上次分享者的微信
                $data =  mysqld_select("select nickname from ".table('weixin_wxfans')." where openid={$decode_openid}");
            }
        }else{
            //如果没有传入 openid ，直接解密后，获取分享者微信
            $data =  mysqld_select("select nickname from ".table('weixin_wxfans')." where openid={$decode_openid}");
        }
    }else{
        //解出来没值，说明，上次分享的用户没有登录
        $data = array();
    }
    return $data;
}*/

/*function setShareAccesskeyCookie($accesskey){
    $cookie      = new LtCookie();
    $cookie->setCookie('shareAccesskey',$accesskey,time()+3600*2);

}*/

/**
 * @content 从cookie中获取分享者的accesskey
 * @return bool|string
 */
/*function getShareAccesskeyCookie(){
    $cookie        = new LtCookie();
    $accesskey     = $cookie->getCookie('shareAccesskey');
    return $accesskey;
}
function cleanShareAccesskeyCookie(){
    $cookie      = new LtCookie();
    $cookie->delCookie('shareAccesskey');
}*/

/**
 * @content 分享活动记录表  添加当天的成员
 * @param $activeId  主表share_active   id
 */
function addShareActiveRecordMember($activeId,$weixin_openid){
    if(!empty($activeId)){
        $share_info  = mysqld_select("select openid from ".table('share_active')." where id={$activeId}");
        if(empty($share_info)){
            return '';
        }
        $own_openid    = getOpenidFromWeixin('');  //可能是空，微信用户第一次关注,还没登录注册
        if(empty($own_openid)){
            //判断weixin_openid是否一样
            $share_weixin_info = mysqld_select("select weixin_openid from ".table('weixin_wxfans')." where openid='{$share_info['openid']}'");
            if($share_weixin_info['weixin_openid'] == $weixin_openid){
                return '';
            }
        }else if($own_openid == $share_info['openid']){
            return '';
        }

        $time = strtotime(date("Y-m-d"),time());
        $info = mysqld_select("select id from ".table('share_active_record')." where visted_openid='{$own_openid}' and createtime={$time}");
        if(empty($info)){
            $data = array(
                'active_id'      => $activeId,
                'visted_openid'  => $own_openid,
                'visted_weixin_openid' => $weixin_openid,
                'createtime'     => $time,
            );
            mysqld_insert('share_active_record',$data);
        }
    }
}

/**
 * 用户注册时候  通过缓存的cookie中取得分享者信息 并给该分享者总的参加活动次数加1
 */
function shareActive_addToalNum(){
    $share_openid = getShareActiveCache();
    if($share_openid){
        $time = time();
        $res  = mysqld_query("update " .table('share_active'). " set `total_num`=total_num+1,`modifytime`={$time} where openid='{$share_openid}'");
    }

}

/*function encodeShareAccessKey($openid){
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
}*/
/*
function isReloadShareActivePage($openid){
    $accesskey = getShareAccesskeyCookie();
    if(empty($accesskey)){
        if(empty($_GET['accesskey'])){
            //给自己openid加密后，得到accesskey用于分享
            $accesskey = encodeShareAccessKey($openid);
        }else{
            //获取当前
            $accesskey = $_GET['accesskey'];
        }

        $url       = mobile_url('shareActive',array('op'=>'display', 'accesskey'=>$accesskey));
        //把accesskey记入缓存，用于注册或者其他地方用到
        setShareAccesskeyCookie($accesskey);
        //再重新加载页面，是为了让cookie生效，也同时，让地址确保带上accesskey，便于分享
        header("location:".$url);
    }else{
        //如果解出来的openid 不对，则说明上次没有登录就分享了，则清空缓存
        $decode_share_openid = decodeShareAccessKey($accesskey);
        if(!$decode_share_openid){
            cleanShareAccesskeyCookie();
        }
    }
}*/

