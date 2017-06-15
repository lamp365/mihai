<?php
/**
 * Created by PhpStorm.
 * User: 刘建凡
 * Date: 2017/4/20
 * Time: 18:39
 */

namespace wapi\controller;


class login extends \common\controller\basecontroller {

   public function index()
   {
       $_GP = $this->request;
       $service = new \service\wapi\loginService();
       $data = $service->do_login($_GP['code']);
       if($data){
           ajaxReturnData(1,'登录成功',$data);
       }else{
           ajaxReturnData(0,$service->getError());
       }
   }

    /**
     * 检验用户信息合法 是否已经过期
     * errno 2 表示已经重新登录  1表示信息合法还在有效期
     */
   public function checkUser()
   {
       $service = new \service\wapi\loginService();
       $_GP = $this->request;
       if(empty($_GP['session3rd'])){
           //直接登录
           $data = $service->do_login($_GP['code']);
           if($data){
               ajaxReturnData(2,'登录成功',$data);
           }else{
               ajaxReturnData(0,$service->getError());
           }
       }

       //否则的话验证 用户信息
       $session3rd_cache = $service->get_session3rd_cache($_GP['session3rd']);
       if(!$session3rd_cache){
           //已经过期 直接登录
           $data = $service->do_login($_GP['code']);
           if($data){
               ajaxReturnData(2,'登录成功',$data);
           }else{
               ajaxReturnData(0,$service->getError());
           }
       }

       /**
        * server计算signature, 并与小程序传入的signature比较, 校验signature的合法性, 不匹配则返回signature不匹配的错误. 不匹配的场景可判断为恶意请求, 可以不返回.
        * 通过调用接口（如 wx.getUserInfo）获取敏感数据时，接口会同时返回 rawData、signature，其中 signature = sha1( rawData + session_key )
        *
        * 将 signature、rawData、以及用户登录态发送给开发者服务器，开发者在数据库中找到该用户对应的 session-key
        * ，使用相同的算法计算出签名 signature2 ，比对 signature 与 signature2 即可校验数据的可信度。
        */
       $signature   = $_GP['signature'];
       $rawData     = $_GP['rawData'];
       $session_key = $session3rd_cache['session_key'];
       $signature2  = sha1($rawData . $session_key);

       if ($signature2 !== $signature) {
            //可能是因为 换了手机 临时缓存  信息不匹配 重新登录
           $data = $service->do_login($_GP['code']);
           if($data){
               ajaxReturnData(2,'登录成功',$data);
           }else{
               ajaxReturnData(0,$service->getError());
           }
       }

        ajaxReturnData(1,"用户合法");
   }
}