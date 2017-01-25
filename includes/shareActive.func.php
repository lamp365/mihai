<?php
/**
 * @param $openid
 * 用户每次近来活动主页优先创建 活动主表的记录
 */
function checkIsAddShareActive($openid){
    $id = 0;
    if(!empty($openid)){
        //今天的凌晨时间
        $curt_time = strtotime(date("Y-m-d"),time());
        $member    = member_get($openid);
        $rank      = mysqld_select("SELECT rank_level FROM " . table('rank_model')." where experience<='".$member['experience']."' order by rank_level desc limit 1 " );
        $info      = mysqld_select("select id,zero_time from ".table('share_active')." where openid='{$openid}'");
        $rank_level= empty($rank['rank_level']) ? 2 : $rank['rank_level'];
        if(empty($info)){
            $data = array(
              'openid'      => $openid,
              'total_num'   => $rank_level*2,
              'createtime'  => time(),
              'modifytime'  => time(),
              'zero_time'   => $curt_time
            );
            mysqld_insert('share_active',$data);
            $id = mysqld_insertid();
        }else{
            $id   = $info['id'];
            if($curt_time>$info['zero_time']){
                mysqld_update('share_active',array(
                    'total_num'  => $rank_level,
                    'zero_time'  => $curt_time,
                    'modifytime' => time()
                ),array('id'=>$id));
            }
        }
    }
    return $id;
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
 * @content 得到今天已经分享出去的一些用户
 * @param int $num      控制取出的个数 为空时，则全部取
 * @param bool $today   为true则默认取今天的，不为true则不限制时间
 * @return array
 */
function getHasShareMember($num=10,$today=true){
    $accesskey    = getShareAccesskeyCookie();
    $share_openid = decodeShareAccessKey($accesskey);
    $member = array();
    if($share_openid){
        $share = mysqld_select("select id from ".table('share_active')." where openid='{$share_openid}'");
        $limit = '';
        $where = '';
        if(!empty($share)){
            if($today){
                //获取当天凌晨年月日的时间戳
                $time  = strtotime(date('Y-m-d'),time());
                $where = " and s.createtime = {$time}";
            }
            if($num){
                $limit = "limit {$num}";
            }
            $sql     = "select s.visted_openid,wx.* from ".table('share_active_record')." as s left join ".table('weixin_wxfans')." as wx ";
            $sql    .= " on s.visted_openid=wx.openid where s.active_id={$share['id']} {$where}  order by s.id desc {$limit}";
            $member  = mysqld_selectall($sql);
        }
    }
    return $member;
}

/**
 * 解密accesskey  得到openid与当前用户比对，是自己的返回true,否则返回false
 * @param $openid  当前用户
 * @return bool
 */
function checkAccessKeyIsSelf($openid){
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
}

/**
 * @content   获取活动分享者的微信信息
 * @param string $openid  可以不给，不给的话，默认解密accesskey后得到的openid直接去查询微信信息
 * @return array|bool|mixed
 */
function getSharerOfWeixin($openid=''){
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
}

function setShareAccesskeyCookie($accesskey){
    $cookie      = new LtCookie();
    $cookie->setCookie('shareAccesskey',$accesskey,time()+3600*2);

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
 * @content 分享活动记录表  添加当天的成员
 * @param $activeId  主表share_active   id
 * @param $openid    要参与的用户openid
 */
function addShareActiveRecordMember($activeId,$openid){
    if(!empty($activeId) && !empty($openid)){
        $time = strtotime(date("Y-m-d"),time());
        $info = mysqld_select("select id from ".table('share_active_record')." where visted_openid='{$openid}' and createtime={$time}");
        if(empty($info)){
            $share_info = mysqld_select("select openid from ".table('share_active')." where id={$activeId}");
            //以免自己参与自己的活动，故再次确认
            if($share_info['openid'] != $openid){
                $data = array(
                    'active_id'      => $activeId,
                    'visted_openid'  => $openid,
                    'createtime'     => $time,
                );
                mysqld_insert('share_active_record',$data);
            }
        }
    }
}

/**
 * 用户注册时候  通过缓存的cookie中取得分享者信息 并给该分享者总的参加活动次数加1
 */
function shareActive_addToalNum(){
    $accesskey = getShareAccesskeyCookie();
    if(!empty($accesskey)){
        $share_openid = decodeShareAccessKey($accesskey);
        if($share_openid){
            $time = time();
            $res  = mysqld_query("update " .table('share_active'). " set `total_num`=total_num+1,`modifytime`={$time} where openid='{$share_openid}'");
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
}