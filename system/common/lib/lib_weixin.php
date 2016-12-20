<?php
// +----------------------------------------------------------------------
// | WE CAN DO IT JUST FREE
// +----------------------------------------------------------------------
// | Copyright (c) 2015 http://www.squdian.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 小物社区 <QQ:119006873> <http://www.squdian.com>
// +----------------------------------------------------------------------
defined('SYSTEM_IN') or exit('Access Denied');

function get_weixin_fans_byopenid($openid, $weixin_openid)
{
    $weixin_wxfans = mysqld_select("SELECT * FROM " . table('weixin_wxfans') . " where openid=:openid or weixin_openid=:weixin_openid", array(
        ':openid' => $openid,
        ':weixin_openid' => $weixin_openid
    ));
    return $weixin_wxfans;
}

function get_js_ticket()
{
    $configs = globaSetting(array(
        "jsapi_ticket",
        "jsapi_ticket_exptime"
    ));
    
    $jsapi_ticket = $configs['jsapi_ticket'];
    $jsapi_ticket_exptime = intval($configs['jsapi_ticket_exptime']);
    if (empty($jsapi_ticket) || empty($jsapi_ticket_exptime) || $jsapi_ticket_exptime < time()) {
        
        $accessToken = get_weixin_token();
        $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=$accessToken";
        $content = http_get($url);
        $res = @json_decode($content, true);
        $ticket = $res['ticket'];
        
        if (! empty($ticket)) {
            $cfg = array(
                'jsapi_ticket' => $ticket,
                'jsapi_ticket_exptime' => time() + intval($res['expires_in'])
            );
            refreshSetting($cfg);
            return $ticket;
        }
        return '';
    } else {
        return $jsapi_ticket;
    }
}

function get_weixin_token($refresh = false)
{
    if ($refresh) {
        $cfg = array(
            'weixin_access_token' => ''
        );
        refreshSetting($cfg);
    }
    $configs = globaSetting(array(
        "weixin_access_token",
        "weixin_appId",
        "weixin_appSecret"
    ));
    $weixin_access_token = unserialize($configs['weixin_access_token']);
    if (is_array($weixin_access_token) && ! empty($weixin_access_token['token']) && ! empty($weixin_access_token['expire']) && $weixin_access_token['expire'] > TIMESTAMP) {
        return $weixin_access_token['token'];
    } else {
        $appid = $configs['weixin_appId'];
        $secret = $configs['weixin_appSecret'];
        
        if (empty($appid) || empty($secret)) {
            message('请填写公众号的appid及appsecret！');
        }
        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$appid}&secret={$secret}";
        $content = http_get($url);
		
        if (empty($content)) {
            message('获取微信公众号授权失败, 请稍后重试！');
        }
        $token = @json_decode($content, true);
        if (empty($token) || ! is_array($token)) {
            message('获取微信公众号授权失败, 请稍后重试！ 公众平台返回原始数据为:' . $token);
        }
        if (empty($token['access_token']) || empty($token['expires_in'])) {
            message('解析微信公众号授权失败, 请稍后重试！');
        }
        $record = array();
        $record['token'] = $token['access_token'];
        $record['expire'] = TIMESTAMP + $token['expires_in'];
        $cfg = array(
            'weixin_access_token' => serialize($record)
        );
        refreshSetting($cfg);
        return $record['token'];
    }
}

function weixin_send_custom_message($from_user, $msg)
{
    $access_token = get_weixin_token();
    $url = "https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token={$access_token}";
    $msg = str_replace('"', '\\"', $msg);
    $post = '{"touser":"' . $from_user . '","msgtype":"text","text":{"content":"' . $msg . '"}}';
    
    http_post($url, $post);
}

if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
    $weixinthirdlogin = mysqld_select("SELECT * FROM " . table('thirdlogin') . " WHERE enabled=1 and `code`='weixin'");
    
    if (! empty($weixinthirdlogin) && ! empty($weixinthirdlogin['id'])) {

        function xoauth($appid, $secret)
        {
            global $_GP;
            // 用户不授权返回提示说明
            if ($_GP['code'] == "authdeny") {
                exit();
            }
            // 高级接口取未关注用户Openid
            if (isset($_GP['code'])) {
                
                if (empty($appid) || empty($secret)) {
                    message('微信公众号没有配置公众号AppId和公众号AppSecret!');
                }
                
                $state = $_GP['state'];
                // 0未获取用户资料 1获取用户资料
                
                // 查询活动时间
                $code = $_GP['code'];
                $oauth2_code = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=" . $appid . "&secret=" . $secret . "&code=" . $code . "&grant_type=authorization_code";
                $content = http_get($oauth2_code);
                $token = @json_decode($content, true);

                if (empty($token) || ! is_array($token) || empty($token['access_token']) || empty($token['openid'])) {
                    message('获取微信公众号授权失败');
                    exit();
                }
                $from_user = $token['openid'];
                
                $access_token = get_weixin_token();
                $oauth2_url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=" . $access_token . "&openid=" . $from_user . "&lang=zh_CN";
                
                $content = http_get($oauth2_url);
                $info = @json_decode($content, true);
                
                if ($info['subscribe'] == 1) {
                    $follow = 1;
                } else {
                    $follow = 0;
                }
                $fans = mysqld_select("SELECT * FROM " . table('weixin_wxfans') . " WHERE weixin_openid=:weixin_openid ", array(
                    ':weixin_openid' => $from_user
                ));
                $gender = $info["gender"];
                $nickname = $info["nickname"];
                if (empty($fans) || empty($fans['weixin_openid']) || empty($fans["nickname"])) {
                    if ($follow == 0 && $state == 0) {
                        get_weixin_openid(1);
                        return;
                    }
                    
                    if ($follow == 0 && $state == 1) {
                        $access_token = $token['access_token'];
                        $oauth2_url = "https://api.weixin.qq.com/sns/userinfo?access_token=" . $access_token . "&openid=" . $from_user . "&lang=zh_CN";
                        $content = http_get($oauth2_url);
                        $info = @json_decode($content, true);
                    }
                    
                    if (empty($info) || ! is_array($info) || empty($info['openid'])) {
                        message('获取微信公众号授权失败[无法取得info], 请稍后重试');
                        exit();
                    }
                    
                    $gender = $info['sex'];
                    $nickname = $info["nickname"];
                }
                if (empty($fans['weixin_openid'])) {
                    $row = array(
                        'nickname' => $nickname,
                        'follow' => $follow,
                        'gender' => intval($gender),
                        'weixin_openid' => $from_user,
                        'avatar' => '',
                        'createtime' => TIMESTAMP
                    );
                    mysqld_insert('weixin_wxfans', $row);
                    if (! empty($info["headimgurl"])) {
                        mysqld_update('weixin_wxfans', array(
                            'avatar' => $info["headimgurl"]
                        ), array(
                            'weixin_openid' => $from_user
                        ));
                    }
                } else {
                    
                    $row = array(
                        'follow' => $follow,
                        'gender' => intval($gender),
                        'avatar' => ''
                    );
                    if (! empty($nickname)) {
                        $row['nickname'] = $nickname;
                    }
                    mysqld_update('weixin_wxfans', $row, array(
                        'weixin_openid' => $from_user
                    ));
                    
                    if (! empty($info["headimgurl"])) {
                        mysqld_update('weixin_wxfans', array(
                            'avatar' => $info["headimgurl"]
                        ), array(
                            'weixin_openid' => $from_user
                        ));
                    }
                }
                if (! empty($fans['openid']) && ! empty($nickname)) {
                    $member = mysqld_select("SELECT realname FROM " . table('member') . " WHERE openid=:openid ", array(
                        ':openid' => $fans['openid']
                    ));
                    if (empty($member['realname'])) {
                        mysqld_update('member', array(
                            'realname' => $nickname
                        ), array(
                            'openid' => $fans['openid']
                        ));
                    }
                }
                
                return $from_user;
            } else {
                message('微信端网页授权域名设置出错！');
                exit();
            }
        }     
       

        function get_weixin_openid($state = 0)
        {
           
                global $_GP;
                $settings = globaSetting(array(
                    "weixin_appId",
                    "weixin_appSecret"
                ));
                $appid = $settings['weixin_appId'];
                $secret = $settings['weixin_appSecret'];
                
                if (empty($appid) || empty($secret)) {
                    message('微信公众号没有配置公众号AppId和公众号AppSecret!');
                }
                
                $weixinfans = array();
                if (! empty($_SESSION[MOBILE_WEIXIN_OPENID]) && ! empty($_SESSION[MOBILE_SESSION_ACCOUNT]) && ! empty($_SESSION[MOBILE_SESSION_ACCOUNT]['openid'])) {
                    $weixinfans = mysqld_select("SELECT * FROM " . table('weixin_wxfans') . " WHERE weixin_openid=:weixin_openid ", array(
                        ':weixin_openid' => $_SESSION[MOBILE_SESSION_ACCOUNT]['openid']
                    ));
                    
                    if (empty($weixinfans['weixin_openid'])) {
                        if (isset($_SESSION[MOBILE_WEIXIN_OPENID]) && isset($_SESSION[MOBILE_SESSION_ACCOUNT]['openid'])) {
                            if ($_SESSION[MOBILE_WEIXIN_OPENID] != $_SESSION[MOBILE_SESSION_ACCOUNT]['openid']) {
                                unset($_SESSION[MOBILE_WEIXIN_OPENID]);
                                unset($_SESSION[MOBILE_SESSION_ACCOUNT]);
                            }
                        }
                    }
                }
               
                if (empty($_SESSION[MOBILE_WEIXIN_OPENID]) || empty($_SESSION[MOBILE_SESSION_ACCOUNT]) || empty($_SESSION[MOBILE_SESSION_ACCOUNT]['openid'])) {
                    
                    if ($state == 1 || (isset($_GP['code']) && isset($_GP['state']) && $_GP['state'] == 1)) {
                        $scope = "snsapi_userinfo";
                        
                        if (isset($_GP['code']) && isset($_GP['state']) && $_GP['state'] == 1) {
                            
                            $from_user = xoauth($appid, $secret);
                            $_SESSION[MOBILE_WEIXIN_OPENID] = $from_user;
                            $sessionAccount = array(
                                'openid' => $from_user
                            );
                            $_SESSION[MOBILE_SESSION_ACCOUNT] = $sessionAccount;
                            return $from_user;
                            exit();
                        }
                    } else {
                        $scope = "snsapi_base";
                       
                        if (isset($_GP['code'])) {
                            $from_user = xoauth($appid, $secret);
                            $_SESSION[MOBILE_WEIXIN_OPENID] = $from_user;
                            $sessionAccount = array(
                                'openid' => $from_user
                            );
                            $_SESSION[MOBILE_SESSION_ACCOUNT] = $sessionAccount;
                            return $from_user;
                            exit();
                        }
                    }
                    
                    $url = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";                  
                    $oauth2_code = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=" . $appid . "&redirect_uri=" . urlencode($url) . "&response_type=code&scope=" . $scope . "&state=" . $state . "#wechat_redirect"; // $state 0 不拉取用户资料 1拉取用户资料
                    
                    header("location:$oauth2_code");
                    exit();
                } 
                else {                                   
                    return  $_SESSION[MOBILE_WEIXIN_OPENID];                   
                }
            
        }
      
        $weixin_openid = get_weixin_openid();       
        if (! empty($weixin_openid)) {
            member_login_weixin($weixin_openid);          
        }      
    }
}