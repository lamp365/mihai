<?php
/*
setting相关
*/

function refreshSetting($arrays)
{
    if (is_array($arrays)) {
        foreach ($arrays as $cid => $cate) {
            $config_data = mysqld_selectcolumn('SELECT `name` FROM ' . table('config') . " where `name`=:name", array(
                ":name" => $cid
            ));
            if (empty($config_data)) {
                $data = array(
                    'name' => $cid,
                    'value' => $cate
                );
                mysqld_insert('config', $data);
            } else {
                mysqld_update('config', array(
                    'value' => $cate
                ), array(
                    'name' => $cid
                ));
            }
        }
        mysqld_update('config', array(
            'value' => ''
        ), array(
            'name' => 'system_config_cache'
        ));
    }
}

function globaSetting($conditions = array())
{
    $domain = $_SERVER['HTTP_HOST'];
    //根据访问的域名查找 微信配置信息
    $weixin_config = mysqld_select("select * from ".table('weixin_config')." where domain='{$domain}' and is_used=1");
    if(empty($weixin_config)){
        //如果找不到 则使用默认的
        $weixin_config = mysqld_select("select * from ".table('weixin_config')." where is_default=1");
    }

    $config = array();
    $system_config_cache = mysqld_select('SELECT * FROM ' . table('config') . " where `name`='system_config_cache'");
    if (empty($system_config_cache['value'])) {
        $configdata = mysqld_selectall('SELECT * FROM ' . table('config'));
        foreach ($configdata as $item) {
            $config[$item['name']] = $item['value'];
        }
        if (! empty($system_config_cache['name'])) {
            mysqld_update('config', array(
                'value' => serialize($config)
            ), array(
                'name' => 'system_config_cache'
            ));
        } else {
            mysqld_insert('config', array(
                'name' => 'system_config_cache',
                'value' => serialize($config)
            ));
        }
        $config['weixinname']       = $weixin_config['weixinname'];
        $config['weixintoken']      = $weixin_config['weixintoken'];
        $config['EncodingAESKey']   = $weixin_config['accesskey'];
        $config['weixin_appId']     = $weixin_config['appid'];
        $config['weixin_appSecret'] = $weixin_config['appsecret'];
        $config['weixin_access_token'] = $weixin_config['weixin_access_token'];
        return $config;
    } else {
        $config_arr = unserialize($system_config_cache['value']);
        $config_arr['weixinname']       = $weixin_config['weixinname'];
        $config_arr['weixintoken']      = $weixin_config['weixintoken'];
        $config_arr['EncodingAESKey']   = $weixin_config['accesskey'];
        $config_arr['weixin_appId']     = $weixin_config['appid'];
        $config_arr['weixin_appSecret'] = $weixin_config['appsecret'];
        $config_arr['weixin_access_token'] = $weixin_config['weixin_access_token'];
        return $config_arr;
    }
}

/**
 * @param $cfg
 * @return array|string
 * @content qq客服可设置为上班和下班  数据格式发生变化存为json {7918458:1,5468988:0} 1表示上班  0表不上班 返回上班中的qq
 */
function getQQ_onWork($cfg){
    $mobile = new MobileDetect();
    if($mobile->isSafari()){
       $qq_src = "mqqwpa://im/chat?chat_type=wpa&uin=[qq_mark]&version=1&src_type=web&web_src=qq.com";
    }else{
       $qq_src = "http://wpa.qq.com/msgrd?v=3&uin=[qq_mark]&site=himrc.com&menu=yes";
//       $qq_src = "tencent://message/?uin=[qq_mark]&Site=himrc.com&Menu=yes";
    }

    $qqarr = array();
    if(!empty($cfg['shop_kfcode'])){
        $data = json_decode($cfg['shop_kfcode'],true);
        if(is_array($data)){
            foreach($data as $qq => $num){
                if($num == 1)
                    $qqarr[] = str_replace('[qq_mark]',$qq,$qq_src);
            }
        }
    }
    return $qqarr;
}

function save_weixin_access_token($seriaze_access_token){
    $domain = $_SERVER['HTTP_HOST'];
    $res = mysqld_update("weixin_config",array('weixin_access_token'=>$seriaze_access_token),array('domain'=>$domain));
    if(!$res){
        $res = mysqld_update("weixin_config",array('weixin_access_token'=>$seriaze_access_token),array('is_default'=>1));
    }
}