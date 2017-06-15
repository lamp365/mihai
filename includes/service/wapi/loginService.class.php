<?php
/**
 * Created by PhpStorm.
 * User: 刘建凡
 * Date: 2017/4/20
 * Time: 18:29
 * demo
 * service 层 用于简化 我们的控制器，让控制器尽量再 简洁
 * 把一些业务提取出来，放在service层中去操作
$a = new \service\shopwap\loginService();
if($a->todo()){
    //操作成功 则继续业务
}else{
    message($a->getError());
}
 */
namespace service\wapi;

class loginService extends \service\publicService
{
    public function check_parame($data)
    {
        if(empty($data['code'])){
            $this->error = 'code参数不能为空';
            return false;
        }
        if(empty($data['rawData']) || empty($data['signature'])){
            $this->error = 'signature参数不能为空';
            return false;
        }
        if(empty($data['encryptedData']) || empty($data['iv'])){
            $this->error = 'encryptedData参数不能为空';
            return false;
        }
        return true;
    }

    public function get_appid($code,$appid,$secret)
    {
        $url = "https://api.weixin.qq.com/sns/jscode2session?appid={$appid}&secret={$secret}&js_code=".$code.'&grant_type=authorization_code';
        $res = http_get($url);
        $res = json_decode($res,true);
        if(empty($res['openid']) || empty($res['session_key'])){
            $this->error = $res['errmsg'];
            return false;
        }
        return $res;
    }

    /**
     * 检验数据的真实性，并且获取解密后的明文.
     * @param $encryptedData string 加密的用户数据
     * @param $iv string 与用户数据一同返回的初始向量
     * @param $data string 解密后的原文
     *
     * @return int 成功0，失败返回对应的错误码
     */
    public function check_decryptData($sessionKey,$iv,$encryptedData,$appid)
    {
        if(strlen($sessionKey) != 24 || strlen($iv) != 24){
            $this->error = 'iv参数有误！';
            return false;
        }
        $aesKey    = base64_decode($sessionKey);
        $aesIV     = base64_decode($iv);
        $aesCipher = base64_decode($encryptedData);

        try {

            $module = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_CBC, '');
            mcrypt_generic_init($module, $aesKey, $aesIV);

            //解密
            $decrypted = mdecrypt_generic($module, $aesCipher);
            mcrypt_generic_deinit($module);
            mcrypt_module_close($module);
        } catch (\Exception $e) {
            $this->error = 'aes解密失败！';
            return false;
        }

        try {
            //去除补位字符
            $pkc_encoder = new \service\wapi\pkcs7EncoderService();
            $result = $pkc_encoder->decode($decrypted);

        } catch (\Exception $e) {
            //print $e;
            $this->error = 'aes解密失败！！';
            return false;
        }

        $dataObj=json_decode($result,true);
        if( $dataObj  == NULL || empty($dataObj))
        {
            $this->error = 'aes解密失败！！！';
            return false;
        }
        /**  $dataObj
        [openId] => oxDr-0ObKhg0Ly52XMpR07WxouLE
        [nickName] => 建凡
        [gender] => 1
        [language] => zh_CN
        [city] => Putian
        [province] => Fujian
        [country] => CN
        [avatarUrl] => http://wx.qlogo.cn/mmopen/vi_32/Q0j4TwGTfTKGPGWdaGAibSOxv3uWMvucpnU8kBE9SBiaUPIUHS5WzsZPic7keDTw1ETuJbaIKMTLTxAATRsC7o4DQ/0
        [unionId] => sadasdasdas
        [watermark] => Array
        (
            [timestamp] => 1497504958
            [appid] => wxee3d6d279578322b
        )
         */
        if( $dataObj['watermark']['appid'] != $appid )
        {
            $this->error = 'aes解密失败。';
            return false;
        }
        return $dataObj;
    }

    public function do_login($userInfo,$expires_in)
    {
        $info = mysqld_select("select * from ".table('weixin_wxfans')." where unionid='{$userInfo['unionId']}'");
        if(empty($info)){
            //插入
            $mem_openid  = date("YmdH",time()).rand(100,999);
            $hasmember   = mysqld_select("SELECT openid FROM " . table('member') . " WHERE openid = :openid ", array(':openid' => $mem_openid));
            if(isset($hasmember['openid']) && !empty($hasmember['openid'])) {
                $mem_openid = date("YmdH",time()).rand(100,999);
            }
            $insert_data['openid'] = $mem_openid;
            $insert_data['weixin_openid'] = $userInfo['openId'];
            $insert_data['nickname']      = $userInfo['nickName'];
            $insert_data['avatar']        = $userInfo['avatarUrl'];
            $insert_data['gender']        = $userInfo['gender'];
            $insert_data['unionid']       = $userInfo['unionId'];
            $insert_data['createtime']    = time();
            //插入微信用户
            $res = mysqld_insert('weixin_wxfans',$insert_data);
            if($res){
                //插入用户 member
                $mem_data = array(
                    'nickname'	      => $userInfo['nickName'],
                    'realname'	      => $userInfo['nickName'],
                    'avatar'	      => $userInfo['avatarUrl'],
                    'createtime'       => time(),
                    'status'           => 1,
                    'istemplate'       => 0,
                    'experience'       => 0 ,
                    'openid'           => $mem_openid,
                    'member_type'      => 1,
                );
                mysqld_insert('member',$mem_data);
                //注册送积分
                register_credit('',$mem_openid);
            }else{
                $this->error = '登录失败，刷亲页面再试！';
                return false;
            }
            $device_code = $userInfo['unionId'];
            $member_info = $insert_data;
        }else{
            $device_code = $userInfo['unionId'];
            $member_info = $info;
        }

        $member_info['device_code'] = $device_code;
        //set cache  小程序 不支持 cookie  sesion
        $memcache  = new \Mcache();
        $memcache->set($device_code,$member_info,$expires_in);
        return $member_info;
    }


    public function session3rd($len)
    {
        $fp = @fopen('/dev/urandom','rb');
        $result = '';
        if ($fp !== FALSE) {
            $result .= @fread($fp, $len);
            @fclose($fp);
        }else{
            trigger_error('Can not open /dev/urandom.');
        }
        // convert from binary to string
        $result = base64_encode($result);
        // remove none url chars
        $result = strtr($result, '+/', '-_');
        return substr($result, 0, $len);
    }

    public function set_session3rd_cache($session3rd,$data,$expires_in)
    {
        $session3rd = "session3rd_".$session3rd;
        $cache_val = serialize(array('openid'=>$data['opedid'],'session_key'=>$data['session_key']));
        if(class_exists('Memcached')){
            $memcache  = new \Mcache();
            $memcache->set($session3rd,$cache_val,$expires_in);
        }else {
            ajaxReturnData(0,'请开启缓存!');
        }
    }

    public function get_session3rd_cache($session3rd)
    {
        $session3rd = "session3rd_".$session3rd;
        if(class_exists('Memcached')){
            $memcache  = new \Mcache();
            $data = $memcache->get($session3rd);
        }else {
            ajaxReturnData(0,'请开启缓存!');
        }
        if(empty($data)){
            return false;
        }
        return unserialize($data);
    }
}