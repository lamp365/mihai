<?php
/**
 * Created by PhpStorm.
 * User: 刘建凡
 * Date: 2017/4/20
 * Time: 18:39
 */

namespace wapi\controller;

class login extends base {

    /**
     * 登录成功后，只是缓存了 微信信息和用户的 openid
     */
   public function index()
   {
       $_GP = $this->request;
       //wxee3d6d279578322b线上appid
       //7d2ac6d21c548f5160c53ae55e61d6db线上 secret
       //wxea80facbec12df2d个人appid
       //2f1e4a3fcb8620276bb8041cfbfe5b67个人 secret
       $seting = globaSetting();
       $appid  = $seting['xcx_appid'];
       $secret = $seting['xcx_appsecret'];

       $service = new \service\wapi\loginService();
       $check   = $service->check_parame($_GP);
       if(!$check){
           ajaxReturnData(0,$service->getError());
       }
       //获取sessinokey 和 对应的 openid
       $session_key_arr = $service->get_appid($_GP['code'],$appid,$secret);
       if(!$session_key_arr){
           ajaxReturnData(0,$service->getError());
       }

       $sessionKey = $session_key_arr['session_key'];
       $expires_in = $session_key_arr['expires_in'];
       $openid     = $session_key_arr['openid'];  //oxDr-0ObKhg0Ly52XMpR07WxouLE

       $rawData       = html_entity_decode($_GP['rawData']);
       $signature     = $_GP['signature'];
       $encryptedData = $_GP['encryptedData'];
       $iv = $_GP['iv'];

       /**
        * server计算signature, 并与小程序传入的signature比较, 校验signature的合法性, 不匹配则返回signature不匹配的错误. 不匹配的场景可判断为恶意请求, 可以不返回.
        * 通过调用接口（如 wx.getUserInfo）获取敏感数据时，接口会同时返回 rawData、signature，其中 signature = sha1( rawData + session_key )
        *
        * 将 signature、rawData、以及用户登录态发送给开发者服务器，开发者在数据库中找到该用户对应的 session-key
        * ，使用相同的算法计算出签名 signature2 ，比对 signature 与 signature2 即可校验数据的可信度。
        */
       $signature2 = sha1($rawData . $sessionKey);
       if ($signature2 !== $signature) ajaxReturnData(0,'签名不匹配');

       /**
        *
        * 使用返回的session_key解密encryptData, 将解得的信息与rawData中信息进行比较, 需要完全匹配,
        * 解得的信息中也包括openid, 也需要与返回的openid匹配. 解密失败或不匹配应该返回客户相应错误.
        * （使用官方提供的方法即可）
        */
       $user_info = $service->check_decryptData($sessionKey,$iv,$encryptedData,$appid);
       if(!$user_info){
            ajaxReturnData(0,$service->getError());
       }
       if(empty($user_info['unionId'])){
           ajaxReturnData(0,'请添加到微信开放平台');
       }

       $memInfo = $service->do_login($user_info,$expires_in);
       if(!$memInfo){
           ajaxReturnData(0,$service->getError());
       }
       ajaxReturnData(1,'登录成功',$memInfo);

   }

    /**
     * 用于开发的时候调试 信息登录使用
     */
    public function pc_login()
    {
        $_GP = $this->request;
        if(empty($_GP['mobile']) || empty($_GP['pwd'])){
            ajaxReturnData(0,'账户和密码不能为空！');
        }
        $login = new \service\shopwap\loginService();
        $mem_info = $login->do_login(array('mobile'=>$_GP['mobile'],'pwd'=>$_GP['pwd']));
        if(!$mem_info){
            ajaxReturnData(0,$login->getError());
        }
        ajaxReturnData(1,'成功',$mem_info);
    }
}