<?php
/**
 *小程序地址选择
 */

namespace wapi\controller;
class address extends base{

    public function __construct()
    {
        parent::__construct();
        $this->openid = checkIsLogin();
        if(!$this->openid){
            ajaxReturnData(0,'请授权登录！');
        }
        
    }
    /**
     * 默认地址获取
     *   */
    public function getDefault(){
        $openid = $this->openid;
        $shopAddressModel = new \model\shop_address_model();
        //$_GP = $this->request;
        //$need_identity = isset($_GP['need_identity']) ? intval($_GP['need_identity']) : 0;
        $info = $shopAddressModel->getOneAddress(array('openid'=>$openid,'isdefault'=>1,'deleted'=>0),'realname,mobile,province,city,area,address,idnumber');
        if (empty($info)) ajaxReturnData(1,'暂时无数据');
        /* if ($need_identity){
            if (empty($info['idnumber'])) ajaxReturnData(1,'没有填写身份证');
        }    */     
        ajaxReturnData(1,'',$info);
    }
    /**
     * 地址获取
     *   */
    public function getInfo(){
        $openid = $this->openid;
        $_GP = $this->request;
        $id = intval($_GP['id']);
        if (empty($id)) ajaxReturnData(0,'参数错误');
        $shopAddressModel = new \model\shop_address_model();
        $info = $shopAddressModel->getOneAddress(array('openid'=>$openid,'id'=>$id,'deleted'=>0),'realname,mobile,province,city,area,address,idnumber');
        if (empty($info)) ajaxReturnData(1,'暂时无数据');
        ajaxReturnData(1,'',$info);
    }
    /**
     * 取所有地址
     *   */
    public function getAllAddress(){
        $member= get_member_account();
        $openid = $member['openid'];
        $shopAddressModel = new \model\shop_address_model();
        $info = $shopAddressModel->getAllAddress(array('openid'=>$openid,'deleted'=>0),'realname,mobile,province,city,area,address,isdefault,id,idnumber');
        if (empty($info)) ajaxReturnData(1,'暂时无数据');
        ajaxReturnData(1,'',$info);
    }
    /**
     * 新增地址
     *   */
    public function addAddress(){
        $openid = $this->openid;
        $_GP = $this->request;
        if (empty($_GP['realname']) || empty($_GP['mobile']) || empty($_GP['province']) || empty($_GP['city']) || empty($_GP['address']) || empty($_GP['area'])) {
            ajaxReturnData('0','参数不完整');
        }
        mysqld_update('shop_address',array('isdefault'=>0),array('isdefault'=>1,'openid'=>$openid,'deleted'=>0));
        $array = array(
            'realname'  =>$_GP['realname'],
            'mobile'    =>$_GP['mobile'],
            'province'  =>$_GP['province'],
            'city'      =>$_GP['city'],
            'area'      =>$_GP['area'],
            'address'   =>$_GP['address'],
            'openid'    =>$openid,
            'isdefault' =>1,
            'idnumber'  =>$_GP['idnumber'],
        );
        mysqld_insert('shop_address',$array);
        if (mysqld_insertid()) ajaxReturnData('1','添加成功');
    }
    /**
     * 更新地址
     *   */
    public function updateAddress(){
        $openid = $this->openid;
        $_GP = $this->request;
        $id = intval($_GP['id']);
          if (empty($id) || empty($_GP['realname']) || empty($_GP['mobile']) || empty($_GP['province']) || empty($_GP['city']) || empty($_GP['address']) || empty($_GP['area'])) {
            ajaxReturnData('0','参数不完整');
        }
        mysqld_update('shop_address',array('isdefault'=>0),array('isdefault'=>1,'openid'=>$openid,'deleted'=>0));
        $array = array(
            'realname'  =>$_GP['realname'],
            'mobile'    =>$_GP['mobile'],
            'province'  =>$_GP['province'],
            'city'      =>$_GP['city'],
            'area'      =>$_GP['area'],
            'address'   =>$_GP['address'],
            'idnumber'  =>$_GP['idnumber'],
            'isdefault' =>1
        );
        mysqld_update('shop_address',$array,array('id'=>$id,'openid'=>$openid,'deleted'=>0));
        ajaxReturnData('1','修改成功');
    }
    /**
     * 获取省、市、区的接口
     *   */
    public function getAddressInfo(){
        $_GP = $this->request;
        $pid = intval($_GP['pid']) ? intval($_GP['pid']) : 1;//默认是省
        $regionModel = new \model\region_model();
        $return = $regionModel->getAllRegion(array('parent_id'=>$pid),'region_id,region_code,region_name,parent_id');
        ajaxReturnData('1','',$return);
    }
    /**
     * 删除地址
     *   */
    public function delAddress(){
        $_GP = $this->request;
        $id = intval($_GP['id']);
        if (empty($id)) ajaxReturnData('0','参数错误，删除失败');
        mysqld_delete('shop_address',array('id'=>$id));
        ajaxReturnData('1','删除成功');
    }
}