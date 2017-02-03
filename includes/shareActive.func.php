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
        $info      = mysqld_select("select * from ".table('share_active')." where openid='{$openid}'");

        if(empty($info)){
            $rank      = mysqld_select("SELECT rank_level FROM " . table('rank_model')." where experience<='".$member['experience']."' order by rank_level desc limit 1 " );
            $rank_level= empty($rank['rank_level']) ? 2 : $rank['rank_level'];
            $info = array(
              'openid'      => $openid,
              'total_num'   => $rank_level*2,
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
                $update['total_num']  = $rank_level*2;
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
        setShareAccesskeyCookie($accesskey);
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger')) {
            //微信端操作
            $weixin_openid = get_weixin_session_account('weixin_openid');
            addShareActiveRecordMember($share_openid,$weixin_openid);
        }else{
            //非微信端操作
            if($openid){
                //如果已经登录，则进行 邀请添加会员的记录 share_active_record
                addShareActiveRecordMember($share_openid,$openid);
            }
        }

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

function get_active_total_people(){
    $total = mysqld_selectcolumn("select count(id) from ".table('addon7_request'));
    return $total+72385;
}