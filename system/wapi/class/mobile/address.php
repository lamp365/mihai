<?php
/**
 *小程序地址选择
 */

namespace wapi\controller;
class address extends base{
    /**
     * 默认地址获取
     *   */   
    public function getDefault(){
        $member= get_member_account();
        $openid = $member['openid'];
        if (empty($openid)) ajaxReturnData(0,'请先登入');
        $shopAddressModel = new \model\shop_address_model();
        $info = $shopAddressModel->getOneAddress(array('openid'=>$openid,'isdefault'=>1,'deleted'=>0),'realname,mobile,province,city,area,address');
        if (empty($info)) ajaxReturnData(1,'暂时无数据');
        ajaxReturnData(1,'',$info);
    }
    /**
     * 取所有地址
     *   */
    public function getAllAddress(){
        $member= get_member_account();
        $openid = $member['openid'];
        if (empty($openid)) ajaxReturnData(0,'请先登入');
        $shopAddressModel = new \model\shop_address_model();
        $info = $shopAddressModel->getAllAddress(array('openid'=>$openid,'deleted'=>0),'realname,mobile,province,city,area,address,isdefault');
        if (empty($info)) ajaxReturnData(1,'暂时无数据');
        ajaxReturnData(1,'',$info);
    }
    /**
     * 新增地址
     *   */
    public function addAddress(){
        $member= get_member_account();
        $openid = $member['openid'];
        if (empty($openid)) ajaxReturnData(0,'请先登入');
        $_GP = $this->request;
        extract($_GP);
        if (empty($realname) || empty($mobile) || empty($province) || empty($city) || empty($area) || empty($address)) {
            ajaxReturnData('0','参数不完整');
        }
        mysqld_update('shop_address',array('isdefault'=>0),array('isdefault'=>1,'openid'=>$openid,'deleted'=>0));
        $array = array(
            'realname'=>$realname,
            'mobile'=>$mobile,
            'province'=>$province,
            'city'=>$city,
            'area'=>$area,
            'address'=>$address,
            'openid'=>$openid,
            'isdefault'=>1
        );
        mysqld_insert('shop_address',$array);
        if (mysqld_insertid()) ajaxReturnData('1','添加成功');
    }
    /**
     * 更新地址
     *   */
    public function updateAddress(){
        $member= get_member_account();
        $openid = $member['openid'];
        if (empty($openid)) ajaxReturnData(0,'请先登入');
        $_GP = $this->request;
        extract($_GP);
        if (empty($id) || empty($realname) || empty($mobile) || empty($province) || empty($city) || empty($area) || empty($address)) {
            ajaxReturnData('0','参数不完整');
        }
        mysqld_update('shop_address',array('isdefault'=>0),array('isdefault'=>1,'openid'=>$openid,'deleted'=>0));
        $array = array(
            'realname'=>$realname,
            'mobile'=>$mobile,
            'province'=>$province,
            'city'=>$city,
            'area'=>$area,
            'address'=>$address,
            'isdefault'=>1
        );
        mysqld_update('shop_address',$array,array('id'=>$id,'openid'=>$openid,'deleted'=>0));
        ajaxReturnData('1','修改成功');
    }
}